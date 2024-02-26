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

use PDepend\Source\AST\AbstractASTCombinationType;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTType;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Node\ClassNode;
use PHPMD\Node\MethodNode;
use PHPMD\Utility\LastVariableWriting;
use PHPMD\Utility\Seeker;
use SplObjectStorage;

/**
 * This rule collects all private methods in a class that aren't used in any
 * method of the analyzed class.
 */
class UnusedPrivateMethod extends AbstractRule implements ClassAware
{
    /** @var SplObjectStorage */
    private $selfVariableCache;

    /** @var SplObjectStorage */
    private $parametersForScope;

    /**
     * This method checks that all private class methods are at least accessed
     * by one method.
     *
     * @param AbstractNode $class
     * @return void
     */
    public function apply(AbstractNode $class)
    {
        $this->selfVariableCache = new SplObjectStorage();

        /** @var ClassNode $node */
        foreach ($this->collectUnusedPrivateMethods($class) as $node) {
            $this->addViolation($node, array($node->getImage()));
        }
    }

    /**
     * This method collects all methods in the given class that are declared
     * as private and are not used in the same class' context.
     *
     * @param ClassNode $class
     * @return array<string, MethodNode>
     */
    protected function collectUnusedPrivateMethods(ClassNode $class)
    {
        $methods = $this->collectPrivateMethods($class);

        return $this->removeUsedMethods($class, $methods);
    }

    /**
     * Collects all private methods declared in the given class node.
     *
     * @param ClassNode $class
     * @return AbstractNode[]
     */
    protected function collectPrivateMethods(ClassNode $class)
    {
        $methods = array();

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
     * @param ClassNode $class
     * @param MethodNode $method
     * @return boolean
     */
    protected function acceptMethod(ClassNode $class, MethodNode $method)
    {
        return (
            $method->isPrivate() &&
            false === $method->hasSuppressWarningsAnnotationFor($this) &&
            strcasecmp($method->getImage(), $class->getImage()) !== 0 &&
            strcasecmp($method->getImage(), '__construct') !== 0 &&
            strcasecmp($method->getImage(), '__destruct') !== 0 &&
            strcasecmp($method->getImage(), '__clone') !== 0
        );
    }

    /**
     * This method removes all used methods from the given methods array.
     *
     * @param ClassNode $class
     * @param array<string, MethodNode> $methods
     * @return array<string, MethodNode>
     */
    protected function removeUsedMethods(ClassNode $class, array $methods)
    {
        $this->parametersForScope = new SplObjectStorage();

        foreach ($class->getMethods() as $method) {
            list($parameters, $scope) = $method->getNode()->getChildren();
            $this->parametersForScope->offsetSet($scope, $parameters);
        }

        $methods = $this->removeExplicitCalls($class, $methods);
        $methods = $this->removeCallableArrayRepresentations($class, $methods);

        return $methods;
    }

    /**
     * $this->privateMethod() makes "privateMethod" marked as used as an explicit call.
     *
     * @param ClassNode $class
     * @param array<string, MethodNode> $methods
     * @return array<string, MethodNode>
     */
    protected function removeExplicitCalls(ClassNode $class, array $methods)
    {
        foreach ($class->findChildrenOfType('MethodPostfix') as $postfix) {
            if ($this->isClassScope($class, $postfix)) {
                unset($methods[strtolower($postfix->getImage())]);
            }
        }

        return $methods;
    }

    /**
     * [$this 'privateMethod'] makes "privateMethod" marked as used as very likely to be used as a callable value.
     *
     * @param ClassNode $class
     * @param array<string, MethodNode> $methods
     * @return array<string, MethodNode>
     */
    protected function removeCallableArrayRepresentations(ClassNode $class, array $methods)
    {
        foreach ($class->findChildrenOfTypeVariable() as $variable) {
            if ($this->isInstanceOfTheCurrentClass($class, $variable)) {
                $method = $this->getMethodNameFromArraySecondElement($variable->getParent());

                if ($method) {
                    unset($methods[strtolower($method)]);
                }
            }
        }

        return $methods;
    }

    /**
     * Return represented method name if the given element is a 2-items array
     * and that the second one is a literal static string.
     *
     * @param ASTNode|null $parent
     * @return string|null
     */
    protected function getMethodNameFromArraySecondElement($parent)
    {
        if ($parent instanceof ASTNode && $parent->isInstanceOf('ArrayElement')) {
            $array = $parent->getParent();

            if ($array instanceof ASTNode
                && $array->isInstanceOf('Array')
                && count($array->getChildren()) === 2
            ) {
                $secondElement = $array->getChild(1)->getChild(0);

                if ($secondElement->isInstanceOf('Literal')) {
                    return substr($secondElement->getImage(), 1, -1);
                }
            }
        }

        return null;
    }

    /**
     * This method checks that the given method postfix is accessed on an
     * instance or static reference to the given class.
     *
     * @param ClassNode $class
     * @param ASTNode $postfix
     * @return boolean
     */
    protected function isClassScope(ClassNode $class, ASTNode $postfix)
    {
        $owner = $postfix->getParent()->getChild(0);

        if ($owner->isInstanceOf('Variable')) {
            return $this->isInstanceOfTheCurrentClass($class, $owner);
        }

        return (
            $owner->isInstanceOf('MethodPostfix') ||
            $owner->isInstanceOf('SelfReference') ||
            $owner->isInstanceOf('StaticReference') ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0
        );
    }

    protected function isInstanceOfTheCurrentClass(ClassNode $class, ASTNode $variable)
    {
        if ($this->selfVariableCache->offsetExists($variable)) {
            return $this->selfVariableCache->offsetGet($variable);
        }

        $result = $this->calculateInstanceOfTheCurrentClass($class, $variable);
        $this->selfVariableCache->offsetSet($variable, $result);

        return $result;
    }

    protected function calculateInstanceOfTheCurrentClass(ClassNode $class, ASTNode $variable)
    {
        $name = $variable->getImage();

        if (strcasecmp($name, '$this') === 0) {
            return true;
        }

        $scope = Seeker::fromNode($variable)->getParentOfType('Scope');

        if (!$scope) {
            return false;
        }

        $lastWritingFinder = new LastVariableWriting($variable);
        $lastWriting = $lastWritingFinder->findInScope($scope);

        if (!$lastWriting) {
            $scopeNode = $scope->getNode();

            if ($this->parametersForScope->offsetExists($scopeNode)) {
                $lastWriting = $lastWritingFinder->findInParameters($this->parametersForScope->offsetGet($scopeNode));
            }
        }

        if ($lastWriting instanceof ASTType) {
            return $this->canBeCurrentClassInstance($class, $lastWriting);
        }

        return ($lastWriting instanceof ASTNode)
            && $this->isWritingOfSelfType($class, $name, $lastWriting);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function isWritingOfSelfType(ClassNode $class, $name, ASTNode $lastWriting)
    {
        if ($lastWriting->isInstanceOf('CloneExpression')) {
            $cloned = Seeker::fromNode($lastWriting)->getChildIfExist(0);

            return $cloned
                && $cloned->isInstanceOf('Variable')
                && $this->isInstanceOfTheCurrentClass($class, $cloned);
        }

        if ($lastWriting->isInstanceOf('AllocationExpression')) {
            $value = Seeker::fromNode($lastWriting)->getChildIfExist(0);

            return $value
                && ($value->isInstanceOf('SelfReference') || $value->isInstanceOf('StaticReference'));
        }

        if ($lastWriting->isInstanceOf('Variable') && $lastWriting->getImage() !== $name) {
            return $this->isInstanceOfTheCurrentClass($class, $lastWriting);
        }

        return false;
    }

    protected function canBeCurrentClassInstance(ClassNode $class, ASTType $type)
    {
        // canBeCurrentClassInstance
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

    protected function representCurrentClassName(ClassNode $class, $name)
    {
        return in_array($name, array(
            'self',
            'static',
            $class->getFullQualifiedName(),
        ), true);
    }
}
