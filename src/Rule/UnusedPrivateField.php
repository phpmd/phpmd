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
use PDepend\Source\AST\ASTHeredoc;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTString;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Node\ClassNode;
use PHPMD\Rule\Design\CouplingBetweenObjects;
use PHPMD\Utility\ExceptionsList;

/**
 * This rule collects all private fields in a class that aren't used in any
 * method of the analyzed class.
 */
#[SuppressWarnings(CouplingBetweenObjects::class)]
final class UnusedPrivateField extends AbstractRule implements ClassAware
{
    /**
     * Collected private fields/variable declarators in the currently processed
     * class.
     *
     * @var array<string, AbstractNode<ASTVariableDeclarator>>
     */
    private array $fields = [];

    /** Temporary cache of configured exceptions. */
    private ExceptionsList $exceptions;

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
            $fieldName = $field->getImage();

            // Check if field name is in the exceptions list (without the $ prefix)
            if ($this->getExceptionsList()->contains(ltrim($fieldName, '$'))) {
                continue;
            }

            $this->addViolation($field, [$fieldName]);
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

        $this->removeFieldsReadInStrings($class);
    }

    /**
     * This method removes the fields from the <b>$_fields</b> property that are
     * read on <b>$this</b> with the simple string interpolation syntax, like
     * <b>"$this->name"</b> or <b>"$this?->name"</b>.
     *
     * PDepend builds no property postfix for this syntax. The string holds the
     * variable <b>$this</b> followed by the literal <b>-></b> (or <b>?-></b>)
     * and a literal that starts with the property name.
     */
    private function removeFieldsReadInStrings(ClassNode $class): void
    {
        foreach ([ASTString::class, ASTHeredoc::class] as $type) {
            foreach ($class->findChildrenOfType($type) as $string) {
                $this->removeFieldsReadInString($string);
            }
        }
    }

    /**
     * @param AbstractNode<ASTHeredoc|ASTString> $string
     */
    private function removeFieldsReadInString(AbstractNode $string): void
    {
        $parts = $string->getNode()->getChildren();

        foreach ($parts as $index => $part) {
            $arrow = $parts[$index + 1] ?? null;
            $name = $parts[$index + 2] ?? null;

            if (
                $part instanceof ASTVariable &&
                $part->getImage() === '$this' &&
                $arrow instanceof ASTLiteral &&
                in_array($arrow->getImage(), ['->', '?->'], true) &&
                $name instanceof ASTLiteral &&
                preg_match('(^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)', $name->getImage(), $match)
            ) {
                unset($this->fields['$' . $match[0]]);
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

    /**
     * Gets exceptions from property
     */
    private function getExceptionsList(): ExceptionsList
    {
        $this->exceptions ??= new ExceptionsList($this);

        return $this->exceptions;
    }
}
