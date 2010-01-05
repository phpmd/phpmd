<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/pmd
 */

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IClassAware.php';

/**
 * This rule collects all private fields in a class that aren't used in any
 * method of the analyzed class.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/pmd
 */
class PHP_PMD_Rule_UnusedPrivateField
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IClassAware
{
    /**
     * Collected private fields/variable declarators in the currently processed
     * class.
     *
     * @var array(string=>PHP_PMD_Node_ASTNode)
     */
    private $_fields = array();

    /**
     * This method checks that all private class properties are at least accessed
     * by one method.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        foreach ($this->_collectUnusedPrivateFields($node) as $field) {
            $this->addViolation($field, array($field->getImage()));
        }
    }

    /**
     * This method collects all private fields that aren't used by any class
     * method.
     *
     * @param PHP_PMD_Node_Class $class The context class node.
     *
     * @return array(PHP_PMD_AbstractNode)
     */
    private function _collectUnusedPrivateFields(PHP_PMD_Node_Class $class)
    {
        $this->_fields = array();

        $this->_collectPrivateFields($class);
        $this->_removeUsedFields($class);

        return $this->_fields;
    }

    /**
     * This method collects all private fields in the given class and stores
     * them in the <b>$_fields</b> property.
     *
     * @param PHP_PMD_Node_Class $class The context class instance.
     *
     * @return void
     */
    private function _collectPrivateFields(PHP_PMD_Node_Class $class)
    {
        foreach ($class->findChildrenOfType('FieldDeclaration') as $declaration) {
            if ($declaration->isPrivate()) {
                $this->_collectPrivateField($declaration);
            }
        }
    }

    /**
     * This method extracts all variable declarators from the given field
     * declaration and stores them in the <b>$_fields</b> property.
     *
     * @param PHP_PMD_Node_ASTNode $declaration The context field declaration.
     *
     * @return void
     */
    private function _collectPrivateField(PHP_PMD_Node_ASTNode $declaration)
    {
        $fields = $declaration->findChildrenOfType('VariableDeclarator');
        foreach ($fields as $field) {
            $this->_fields[$field->getImage()] = $field;
        }
    }

    /**
     * This method extracts all property postfix nodes from the given class and
     * removes all fields from the <b>$_fields</b> property that are accessed by
     * one of the postfix nodes.
     *
     * @param PHP_PMD_Node_Class $class The context class instance.
     *
     * @return void
     */
    private function _removeUsedFields(PHP_PMD_Node_Class $class)
    {
        foreach ($class->findChildrenOfType('PropertyPostfix') as $postfix) {
            if ($this->_isClassScope($class, $postfix)) {
                $this->_removeUsedField($postfix);
            }
        }
    }

    /**
     * This method removes the field from the <b>$_fields</b> property that is
     * accessed through the given property postfix node.
     *
     * @param PHP_PMD_Node_ASTNode $postfix The context postfix node.
     *
     * @return void
     */
    private function _removeUsedField(PHP_PMD_Node_ASTNode $postfix)
    {
        // TODO: Change this to isStatic() when PHP_Depend 0.9.9 is available
        if ($postfix->getParent()->getImage() === '::') {
            $image = $postfix->getImage();
        } else {
            $image = '$' . $postfix->getImage();
        }

        unset($this->_fields[$image]);
    }

    /**
     * This method checks that the given property postfix is accessed on an
     * instance or static reference to the given class.
     *
     * @param PHP_PMD_Node_Class   $class   The context class node instance.
     * @param PHP_PMD_Node_ASTNode $postfix The context property postfix node.
     *
     * @return boolean
     */
    private function _isClassScope(
        PHP_PMD_Node_Class $class,
        PHP_PMD_Node_ASTNode $postfix
    ) {
        $prefix = $postfix->getParent()->getChild(0);
        return (
            $prefix->isInstanceOf('SelfReference') ||
            $prefix->isInstanceOf('StaticReference') ||
            // TODO: Replace this with ThisVariable check when this AST node is
            //       ported back from the design disharmony branch
            strcasecmp($prefix->getImage(), '$this') === 0 ||
            strcasecmp($prefix->getImage(), $class->getImage()) === 0
        );
    }
}