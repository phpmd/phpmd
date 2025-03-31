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
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTCompoundVariable;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ClassNode;

/**
 * This rule collects all private fields in a class that aren't used in any
 * method of the analyzed class.
 *
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
final class UnusedPrivateField extends AbstractRule implements ClassAware
{
    /**
     * Collected private fields/variable declarators in the currently processed
     * class.
     *
     * @var array<string, AbstractNode<ASTVariableDeclarator>>
     */
    private array $fields = [];

    /**
     * This method checks that all private class properties are at least accessed
     * by one method.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof ClassNode) {
            return;
        }

        foreach ($this->collectUnusedPrivateFields($node) as $field) {
            $this->addViolation($field, [$field->getImage()]);
        }
    }

    /**
     * This method collects all private fields that aren't used by any class
     * method.
     *
     * @return array<string, AbstractNode<ASTVariableDeclarator>>
     * @throws OutOfBoundsException
     */
    private function collectUnusedPrivateFields(ClassNode $class): array
    {
        $this->fields = [];

        $this->collectPrivateFields($class);
        $this->removeUsedFields($class);

        return $this->fields;
    }

    /**
     * This method collects all private fields in the given class and stores
     * them in the <b>$_fields</b> property.
     */
    private function collectPrivateFields(ClassNode $class): void
    {
        foreach ($class->findChildrenOfType(ASTFieldDeclaration::class) as $declaration) {
            if ($declaration->isPrivate()) {
                $this->collectPrivateField($declaration);
            }
        }
    }

    /**
     * This method extracts all variable declarators from the given field
     * declaration and stores them in the <b>$_fields</b> property.
     *
     * @param AbstractNode<ASTFieldDeclaration> $declaration
     */
    private function collectPrivateField(AbstractNode $declaration): void
    {
        $fields = $declaration->findChildrenOfType(ASTVariableDeclarator::class);
        foreach ($fields as $field) {
            $this->fields[$field->getImage()] = $field;
        }
    }

    /**
     * This method extracts all property postfix nodes from the given class and
     * removes all fields from the <b>$_fields</b> property that are accessed by
     * one of the postfix nodes.
     *
     * @throws OutOfBoundsException
     */
    private function removeUsedFields(ClassNode $class): void
    {
        foreach ($class->findChildrenOfType(ASTPropertyPostfix::class) as $postfix) {
            if ($this->isInScopeOfClass($class, $postfix)) {
                $this->removeUsedField($postfix);
            }
        }
    }

    /**
     * This method removes the field from the <b>$_fields</b> property that is
     * accessed through the given property postfix node.
     *
     * @param AbstractNode<ASTPropertyPostfix> $postfix
     */
    private function removeUsedField(AbstractNode $postfix): void
    {
        $image = '$';
        $child = $postfix->getFirstChildOfType(ASTIdentifier::class);

        $parent = $postfix->getParent()?->getNode();
        if ($parent instanceof ASTMemberPrimaryPrefix && $parent->isStatic()) {
            $image = '';
            $child = $postfix->getFirstChildOfType(ASTVariable::class);
        }

        if ($child && $this->isValidPropertyNode($child)) {
            unset($this->fields[$image . $child->getImage()]);
        }
    }

    /**
     * Checks if the given node is a valid property node.
     *
     * @param AbstractNode<ASTExpression> $node
     * @since 0.2.6
     */
    private function isValidPropertyNode(AbstractNode $node): bool
    {
        $parent = $node->getParent();
        while (!$parent?->isInstanceOf(ASTPropertyPostfix::class)) {
            if ($parent?->isInstanceOf(ASTCompoundVariable::class)) {
                return false;
            }
            $parent = $parent?->getParent();
            if (!$parent) {
                return false;
            }
        }

        return true;
    }

    /**
     * This method checks that the given property postfix is accessed on an
     * instance or static reference to the given class.
     *
     * @param AbstractNode<ASTPropertyPostfix> $postfix
     * @throws OutOfBoundsException
     */
    private function isInScopeOfClass(ClassNode $class, AbstractNode $postfix): bool
    {
        $owner = $this->getOwner($postfix);

        return (
            $owner->isInstanceOf(ASTSelfReference::class) ||
            strcasecmp($owner->getImage(), '$this') === 0 ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0
        );
    }

    /**
     * Looks for owner of the given variable.
     *
     * @param AbstractNode<ASTPropertyPostfix> $postfix
     * @return AbstractNode<PDependNode>
     * @throws OutOfBoundsException
     */
    private function getOwner(AbstractNode $postfix): AbstractNode
    {
        $owner = $postfix->getParent()?->getChild(0);

        if ($owner?->isInstanceOf(ASTPropertyPostfix::class)) {
            $owner = $owner->getParent()?->getParent()?->getChild(0);
        }

        $parent = $owner?->getParent();
        if ($parent?->isInstanceOf(ASTArrayIndexExpression::class)) {
            $owner = $parent->getParent()?->getChild(0);
        }
        if (!$owner) {
            throw new OutOfBoundsException();
        }

        return $owner;
    }
}
