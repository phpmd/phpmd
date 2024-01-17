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
    protected $fields = array();

    /**
     * This method checks that all private class properties are at least accessed
     * by one method.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        /** @var ClassNode $field */
        foreach ($this->collectUnusedPrivateFields($node) as $field) {
            $this->addViolation($field, array($field->getImage()));
        }
    }

    /**
     * This method collects all private fields that aren't used by any class
     * method.
     *
     * @param \PHPMD\Node\ClassNode $class
     * @return \PHPMD\AbstractNode[]
     */
    protected function collectUnusedPrivateFields(ClassNode $class)
    {
        $this->fields = array();

        $this->collectPrivateFields($class);
        $this->removeUsedFields($class);

        return $this->fields;
    }

    /**
     * This method collects all private fields in the given class and stores
     * them in the <b>$_fields</b> property.
     *
     * @param \PHPMD\Node\ClassNode $class
     * @return void
     */
    protected function collectPrivateFields(ClassNode $class)
    {
        foreach ($class->findChildrenOfType('FieldDeclaration') as $declaration) {
            /** @var ASTNode $declaration */
            if ($declaration->isPrivate()) {
                $this->collectPrivateField($declaration);
            }
        }
    }

    /**
     * This method extracts all variable declarators from the given field
     * declaration and stores them in the <b>$_fields</b> property.
     *
     * @param \PHPMD\Node\ASTNode $declaration
     * @return void
     */
    protected function collectPrivateField(ASTNode $declaration)
    {
        $fields = $declaration->findChildrenOfType('VariableDeclarator');
        foreach ($fields as $field) {
            $this->fields[$field->getImage()] = $field;
        }
    }

    /**
     * This method extracts all property postfix nodes from the given class and
     * removes all fields from the <b>$_fields</b> property that are accessed by
     * one of the postfix nodes.
     *
     * @param \PHPMD\Node\ClassNode $class
     * @return void
     */
    protected function removeUsedFields(ClassNode $class)
    {
        foreach ($class->findChildrenOfType('PropertyPostfix') as $postfix) {
            /** @var $postfix ASTNode */
            if ($this->isInScopeOfClass($class, $postfix)) {
                $this->removeUsedField($postfix);
            }
        }
    }

    /**
     * This method removes the field from the <b>$_fields</b> property that is
     * accessed through the given property postfix node.
     *
     * @param \PHPMD\Node\ASTNode $postfix
     * @return void
     */
    protected function removeUsedField(ASTNode $postfix)
    {
        $image = '$';
        $child = $postfix->getFirstChildOfType('Identifier');

        if ($postfix->getParent()->isStatic()) {
            $image = '';
            $child = $postfix->getFirstChildOfType('Variable');
        }

        if ($this->isValidPropertyNode($child)) {
            unset($this->fields[$image . $child->getImage()]);
        }
    }

    /**
     * Checks if the given node is a valid property node.
     *
     * @param \PHPMD\Node\ASTNode $node
     * @return boolean
     * @since 0.2.6
     */
    protected function isValidPropertyNode(ASTNode $node = null)
    {
        if ($node === null) {
            return false;
        }

        $parent = $node->getParent();
        while (!$parent->isInstanceOf('PropertyPostfix')) {
            if ($parent->isInstanceOf('CompoundVariable')) {
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
     * @param \PHPMD\Node\ClassNode $class
     * @param \PHPMD\Node\ASTNode $postfix
     * @return boolean
     */
    protected function isInScopeOfClass(ClassNode $class, ASTNode $postfix)
    {
        $owner = $this->getOwner($postfix);

        return (
            $owner->isInstanceOf('SelfReference') ||
            $owner->isInstanceOf('StaticReference') ||
            strcasecmp($owner->getImage(), '$this') === 0 ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0
        );
    }

    /**
     * Looks for owner of the given variable.
     *
     * @param \PHPMD\Node\ASTNode $postfix
     * @return \PHPMD\Node\ASTNode
     */
    protected function getOwner(ASTNode $postfix)
    {
        $owner = $postfix->getParent()->getChild(0);
        if ($owner->isInstanceOf('PropertyPostfix')) {
            $owner = $owner->getParent()->getParent()->getChild(0);
        }

        if ($owner->getParent()->isInstanceOf('ArrayIndexExpression')) {
            $owner = $owner->getParent()->getParent()->getChild(0);
        }

        return $owner;
    }
}
