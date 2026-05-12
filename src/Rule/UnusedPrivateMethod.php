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
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTCloneExpression;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTStaticReference;
use PDepend\Source\AST\ASTVariable;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ClassNode;
use PHPMD\Node\MethodNode;
use RuntimeException;

/**
 * This rule collects all private methods in a class that aren't used in any
 * method of the analyzed class.
 */
final class UnusedPrivateMethod extends AbstractRule implements ClassAware
{
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
        foreach ($class->findChildrenOfType(ASTVariable::class) as $variable) {
            $parent = $variable->getParent();
            if ($parent && $this->isClassScope($class, $variable) && $variable->getImage() === '$this') {
                $method = $this->getMethodNameFromArraySecondElement($parent);

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
     * @param AbstractNode<PDependNode> $parent
     * @throws OutOfBoundsException
     */
    private function getMethodNameFromArraySecondElement(AbstractNode $parent): ?string
    {
        if ($parent->isInstanceOf(ASTArrayElement::class)) {
            $array = $parent->getParent();

            if (
                $array?->isInstanceOf(ASTArray::class)
                && count($array->getChildren()) === 2
            ) {
                $secondElement = $array->getChild(1)->getChild(0);

                if ($secondElement->isInstanceOf(ASTLiteral::class)) {
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
     * @param AbstractNode<ASTExpression> $postfix
     * @throws OutOfBoundsException
     */
    private function isClassScope(ClassNode $class, AbstractNode $postfix): bool
    {
        $owner = $postfix->getParent()?->getChild(0);
        if (!$owner) {
            return false;
        }

        return (
            $owner->isInstanceOf(ASTMethodPostfix::class) ||
            $owner->isInstanceOf(ASTSelfReference::class) ||
            strcasecmp($owner->getImage(), '$this') === 0 ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0 ||
            $this->isVariableOfSelfType($class, $owner)
        );
    }

    /**
     * Checks if the given variable is assigned from a construction of the current class
     * (e.g. clone $this, new self, new static, new ClassName).
     *
     * @param AbstractNode<PDependNode> $owner
     * @throws OutOfBoundsException
     */
    private function isVariableOfSelfType(ClassNode $class, AbstractNode $owner): bool
    {
        if (!$owner->isInstanceOf(ASTVariable::class)) {
            return false;
        }

        $variableName = $owner->getImage();

        foreach ($class->findChildrenOfType(ASTAssignmentExpression::class) as $assignment) {
            $leftSide = $assignment->getChild(0);
            if ($leftSide->getImage() !== $variableName) {
                continue;
            }

            if ($this->isCloneOfSelf($class, $assignment) || $this->isNewSelf($class, $assignment)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the assignment contains a clone of $this or self.
     *
     * @param AbstractNode<PDependNode> $assignment
     * @throws OutOfBoundsException
     */
    private function isCloneOfSelf(ClassNode $class, AbstractNode $assignment): bool
    {
        foreach ($assignment->findChildrenOfType(ASTCloneExpression::class) as $clone) {
            $clonedObject = $clone->getChild(0);

            if ($this->isSelfReference($class, $clonedObject)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the assignment contains a new self/static/ClassName construction.
     *
     * @param AbstractNode<PDependNode> $assignment
     * @throws OutOfBoundsException
     */
    private function isNewSelf(ClassNode $class, AbstractNode $assignment): bool
    {
        foreach ($assignment->findChildrenOfType(ASTAllocationExpression::class) as $allocation) {
            $classReference = $allocation->getChild(0);

            if ($this->isSelfReference($class, $classReference)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given node refers to the current class ($this, self, static, or class name).
     *
     * @param AbstractNode<PDependNode> $node
     */
    private function isSelfReference(ClassNode $class, AbstractNode $node): bool
    {
        return (
            strcasecmp($node->getImage(), '$this') === 0 ||
            $node->isInstanceOf(ASTStaticReference::class) ||
            $node->isInstanceOf(ASTSelfReference::class) ||
            strcasecmp($node->getImage(), $class->getImage()) === 0
        );
    }
}
