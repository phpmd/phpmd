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

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Node\ClassNode;

/**
 * This rule collects all private fields in a class that aren't used in any
 * method of the analyzed class.
 */
class UnusedPrivateField extends AbstractRule implements ClassAware
{
    /**
     * Collected private fields/variable declarators in the currently processed
     * class.
     *
     * @var \PHPMD\Node\ASTNode[]
     */
    protected $fields = [];

    /**
     * This method checks that all private class properties are at least accessed
     * by one method.
     */
    public function apply(AbstractNode $node): void
    {
        /** @var ClassNode $field */
        foreach ($this->collectUnusedPrivateFields($node) as $field) {
            $this->addViolation($field, [$field->getImage()]);
        }
    }

    /**
     * This method collects all private fields that aren't used by any class
     * method.
     *
     * @return \PHPMD\AbstractNode[]
     */
    protected function collectUnusedPrivateFields(ClassNode $class)
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
    protected function collectPrivateFields(ClassNode $class): void
    {
        foreach ($class->findChildrenOfType('PDepend\Source\AST\ASTFieldDeclaration') as $declaration) {
            if ($declaration->isPrivate()) {
                $this->collectPrivateField($declaration);
            }
        }
    }

    /**
     * This method extracts all variable declarators from the given field
     * declaration and stores them in the <b>$_fields</b> property.
     */
    protected function collectPrivateField(ASTNode $declaration): void
    {
        $fields = $declaration->findChildrenOfType('PDepend\Source\AST\ASTVariableDeclarator');
        foreach ($fields as $field) {
            $this->fields[$field->getImage()] = $field;
        }
    }

    /**
     * This method extracts all property postfix nodes from the given class and
     * removes all fields from the <b>$_fields</b> property that are accessed by
     * one of the postfix nodes.
     */
    protected function removeUsedFields(ClassNode $class): void
    {
        foreach ($class->findChildrenOfType('PDepend\Source\AST\ASTPropertyPostfix') as $postfix) {
            if ($this->isInScopeOfClass($class, $postfix)) {
                $this->removeUsedField($postfix);
            }
        }
    }

    /**
     * This method removes the field from the <b>$_fields</b> property that is
     * accessed through the given property postfix node.
     */
    protected function removeUsedField(ASTNode $postfix): void
    {
        $image = '$';
        $child = $postfix->getFirstChildOfType('PDepend\Source\AST\ASTIdentifier');

        $parent = $postfix->getParent();
        if ($parent->isInstanceOf('PDepend\Source\AST\ASTMemberPrimaryPrefix') && $parent->isStatic()) {
            $image = '';
            $child = $postfix->getFirstChildOfType('PDepend\Source\AST\ASTVariable');
        }

        if ($this->isValidPropertyNode($child)) {
            unset($this->fields[$image . $child->getImage()]);
        }
    }

    /**
     * Checks if the given node is a valid property node.
     *
     * @return bool
     * @since 0.2.6
     */
    protected function isValidPropertyNode(ASTNode $node = null)
    {
        if ($node === null) {
            return false;
        }

        $parent = $node->getParent();
        while (!$parent->isInstanceOf('PDepend\Source\AST\ASTPropertyPostfix')) {
            if ($parent->isInstanceOf('PDepend\Source\AST\ASTCompoundVariable')) {
                return false;
            }
            $parent = $parent->getParent();
            if (is_null($parent)) {
                return false;
            }
        }

        return true;
    }

    /**
     * This method checks that the given property postfix is accessed on an
     * instance or static reference to the given class.
     *
     * @return bool
     */
    protected function isInScopeOfClass(ClassNode $class, ASTNode $postfix)
    {
        $owner = $this->getOwner($postfix);

        return (
            $owner->isInstanceOf('PDepend\Source\AST\ASTSelfReference') ||
            $owner->isInstanceOf('PDepend\Source\AST\ASTStaticReference') ||
            strcasecmp($owner->getImage(), '$this') === 0 ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0
        );
    }

    /**
     * Looks for owner of the given variable.
     *
     * @param \PHPMD\Node\ASTNode<\PDepend\Source\AST\ASTPropertyPostfix> $postfix
     * @return AbstractNode
     */
    protected function getOwner(ASTNode $postfix)
    {
        $owner = $postfix->getParent()->getChild(0);
        if ($owner->isInstanceOf('PDepend\Source\AST\ASTPropertyPostfix')) {
            $owner = $owner->getParent()->getParent()->getChild(0);
        }

        if ($owner->getParent()->isInstanceOf('PDepend\Source\AST\ASTArrayIndexExpression')) {
            $owner = $owner->getParent()->getParent()->getChild(0);
        }

        return $owner;
    }
}
