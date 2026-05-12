<?php

/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Rule;

use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTCombinationType;
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTCloneExpression;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTScope;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTStaticReference;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTVariable;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ClassNode;
use PHPMD\Node\MethodNode;
use PHPMD\Utility\CallableArray;
use PHPMD\Utility\LastVariableWriting;
use PHPMD\Utility\Seeker;
use RuntimeException;
use SplObjectStorage;

/**
 * This rule collects all private methods in a class that aren't used in any
 * method of the analyzed class.
 */
final class UnusedPrivateMethod extends AbstractRule implements ClassAware
{
    /** @var SplObjectStorage<AbstractNode<PDependNode>, bool> */
    private $selfVariableCache;

    /** @var SplObjectStorage<PDependNode, ASTFormalParameters> */
    private $parametersForScope;

    /**
     * This method checks that all private class methods are at least accessed
     * by one method.
     *
     * @param AbstractNode<PDependNode> $class
     * @throws RuntimeException
     */
    public function apply(AbstractNode $class): void
    {
        if (!$class instanceof ClassNode) {
            return;
        }

        $this->selfVariableCache = new SplObjectStorage();

        foreach ($this->collectUnusedPrivateMethods($class) as $node) {
            $this->addViolation($node, [$node->getImage()]);
        }
    }

    /**
     * This method collects all methods in the given class that are declared
     * as private and are not used in the same class' context.
     *
     * @return array<string, MethodNode>
     * @throws OutOfBoundsException
     * @throws RuntimeException
     */
    private function collectUnusedPrivateMethods(ClassNode $class): array
    {
        $methods = $this->collectPrivateMethods($class);

        return $this->removeUsedMethods($class, $methods);
    }

    /**
     * Collects all private methods declared in the given class node.
     *
     * @return array<string, MethodNode>
     * @throws RuntimeException
     */
    private function collectPrivateMethods(ClassNode $class): array
    {
        $methods = [];

        foreach ($class->getMethods() as $method) {
            if ($this->acceptMethod($class, $method)) {
                $methods[strtolower($method->getImage())] = $method;
            }
        }

        return $methods;
    }

    /**
     * Returns <b>true</b> when the given method should be used for this rule's
     * analysis.
     *
     * @throws RuntimeException
     */
    private function acceptMethod(ClassNode $class, MethodNode $method): bool
    {
        return (
            $method->isPrivate() &&
            !$method->hasSuppressWarningsFor($this) &&
            strcasecmp($method->getImage(), $class->getImage()) !== 0 &&
            strcasecmp($method->getImage(), '__construct') !== 0 &&
            strcasecmp($method->getImage(), '__destruct') !== 0 &&
            strcasecmp($method->getImage(), '__clone') !== 0
        );
    }

    /**
     * This method removes all used methods from the given methods array.
     *
     * @param array<string, MethodNode> $methods
     * @return array<string, MethodNode>
     * @throws OutOfBoundsException
     */
    private function removeUsedMethods(ClassNode $class, array $methods): array
    {
        $this->parametersForScope = new SplObjectStorage();

        foreach ($class->getMethods() as $method) {
            $children = $method->getNode()->getChildren();

            /** @var ASTFormalParameters $parameters */
            $parameters = $children[0];
            $scope = $children[1];
            $this->parametersForScope->offsetSet($scope, $parameters);
        }

        $methods = $this->removeExplicitCalls($class, $methods);

        return $this->removeCallableArrayRepresentations($class, $methods);
    }

    /**
     * $this->privateMethod() makes "privateMethod" marked as used as an explicit call.
     *
     * @param array<string, MethodNode> $methods
     * @return array<string, MethodNode>
     * @throws OutOfBoundsException
     */
    private function removeExplicitCalls(ClassNode $class, array $methods): array
    {
        foreach ($class->findChildrenOfType(ASTMethodPostfix::class) as $postfix) {
            if ($this->isClassScope($class, $postfix)) {
                unset($methods[strtolower($postfix->getImage())]);
            }
        }

        return $methods;
    }

    /**
     * [$this 'privateMethod'] makes "privateMethod" marked as used as very likely to be used as a callable value.
     *
     * @param array<string, MethodNode> $methods
     * @return array<string, MethodNode>
     * @throws OutOfBoundsException
     */
    private function removeCallableArrayRepresentations(ClassNode $class, array $methods): array
    {
        foreach ($class->findChildrenOfTypeVariable() as $variable) {
            if ($this->isInstanceOfTheCurrentClass($class, $variable)) {
                $method = CallableArray::fromFirstArrayElement($variable->getParent())
                    ->getMethodNameFromArraySecondElement();

                if ($method) {
                    unset($methods[strtolower($method)]);
                }
            }
        }

        return $methods;
    }

    /**
     * This method checks that the given method postfix is accessed on an
     * instance or static reference to the given class.
     *
     * @param AbstractNode<ASTExpression> $postfix
     * @throws OutOfBoundsException
     */
    private function isClassScope(ClassNode $class, AbstractNode $postfix): bool
    {
        $owner = $postfix->getParent()?->getChild(0);
        if (!$owner) {
            return false;
        }

        if ($owner->isInstanceOf(ASTVariable::class)) {
            return $this->isInstanceOfTheCurrentClass($class, $owner);
        }

        return (
            $owner->isInstanceOf(ASTMethodPostfix::class) ||
            $owner->isInstanceOf(ASTSelfReference::class) ||
            $owner->isInstanceOf(ASTStaticReference::class) ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0
        );
    }

    /**
     * @param AbstractNode<PDependNode> $variable
     * @throws OutOfBoundsException
     */
    private function isInstanceOfTheCurrentClass(ClassNode $class, AbstractNode $variable): bool
    {
        if ($this->selfVariableCache->offsetExists($variable)) {
            return (bool) $this->selfVariableCache->offsetGet($variable);
        }

        $result = $this->calculateInstanceOfTheCurrentClass($class, $variable);
        $this->selfVariableCache->offsetSet($variable, $result);

        return $result;
    }

    /**
     * @param AbstractNode<PDependNode> $variable
     * @throws OutOfBoundsException
     */
    private function calculateInstanceOfTheCurrentClass(ClassNode $class, AbstractNode $variable): bool
    {
        $name = $variable->getImage();

        if (strcasecmp($name, '$this') === 0) {
            return true;
        }

        $scope = Seeker::fromNode($variable)->getParentOfType(ASTScope::class);

        if (!$scope) {
            return false;
        }

        $lastWritingFinder = new LastVariableWriting($variable);
        $lastWriting = $lastWritingFinder->findInScope($scope);

        if (!$lastWriting) {
            $scopeNode = $scope->getNode();

            if ($this->parametersForScope->offsetExists($scopeNode)) {
                /** @var ASTFormalParameters $parameters */
                $parameters = $this->parametersForScope->offsetGet($scopeNode);
                $lastWriting = $lastWritingFinder->findInParameters($parameters);
            }
        }

        if ($lastWriting instanceof ASTType) {
            return $this->canBeCurrentClassInstance($class, $lastWriting);
        }

        return ($lastWriting instanceof AbstractNode)
            && $this->isWritingOfSelfType($class, $name, $lastWriting);
    }

    /**
     * @param AbstractNode<PDependNode> $lastWriting
     * @throws OutOfBoundsException
     */
    private function isWritingOfSelfType(ClassNode $class, string $name, AbstractNode $lastWriting): bool
    {
        if ($lastWriting->isInstanceOf(ASTCloneExpression::class)) {
            $cloned = Seeker::fromNode($lastWriting)->getChildIfExist(0);

            return $cloned
                && $cloned->isInstanceOf(ASTVariable::class)
                && $this->isInstanceOfTheCurrentClass($class, $cloned);
        }

        if ($lastWriting->isInstanceOf(ASTAllocationExpression::class)) {
            $value = Seeker::fromNode($lastWriting)->getChildIfExist(0);

            if (!$value) {
                return false;
            }

            if ($value->isInstanceOf(ASTSelfReference::class) || $value->isInstanceOf(ASTStaticReference::class)) {
                return true;
            }

            return $value->isInstanceOf(ASTClassOrInterfaceReference::class)
                && $this->representCurrentClassName($class, $value->getImage());
        }

        if ($lastWriting->isInstanceOf(ASTVariable::class) && $lastWriting->getImage() !== $name) {
            return $this->isInstanceOfTheCurrentClass($class, $lastWriting);
        }

        return false;
    }

    private function canBeCurrentClassInstance(ClassNode $class, ASTType $type): bool
    {
        if ($type instanceof AbstractASTCombinationType) {
            foreach ($type->getChildren() as $child) {
                if ($child instanceof ASTType && $this->canBeCurrentClassInstance($class, $child)) {
                    return true;
                }
            }

            return false;
        }

        if ($type instanceof ASTClassOrInterfaceReference) {
            return $this->representCurrentClassName($class, $type->getImage());
        }

        return false;
    }

    private function representCurrentClassName(ClassNode $class, string $name): bool
    {
        return in_array($name, [
            'self',
            'static',
            $class->getImage(),
            $class->getFullQualifiedName(),
        ], true);
    }
}
