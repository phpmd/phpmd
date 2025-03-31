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
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTStringIndexExpression;
use PDepend\Source\AST\ASTVariable;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use ReflectionException;
use ReflectionFunction;

/**
 * Base class for rules that rely on local variables.
 *
 * @since 0.2.6
 */
abstract class AbstractLocalVariable extends AbstractRule
{
    /** @var list<string> Self reference class names. */
    private array $selfReferences = ['self', 'static'];

    /**
     * PHP super globals that are available in all php scopes, so that they
     * can never be unused local variables.
     *
     * @var array<string, bool>
     * @link http://php.net/manual/en/reserved.variables.php
     */
    private static array $superGlobals = [
        '$argc' => true,
        '$argv' => true,
        '$_COOKIE' => true,
        '$_ENV' => true,
        '$_FILES' => true,
        '$_GET' => true,
        '$_POST' => true,
        '$_REQUEST' => true,
        '$_SERVER' => true,
        '$_SESSION' => true,
        '$GLOBALS' => true,
        '$HTTP_RAW_POST_DATA' => true,
        '$php_errormsg' => true,
        '$http_response_header' => true,
    ];

    /**
     * Tests if the given variable represents one of the PHP super globals
     * that are available in scopes.
     *
     * @param AbstractNode<ASTVariable> $variable
     */
    protected function isSuperGlobal(AbstractNode $variable): bool
    {
        return isset(self::$superGlobals[$variable->getImage()]);
    }

    /**
     * Tests if the given variable node is a regular variable an not property
     * or method postfix.
     *
     * @param AbstractNode<ASTVariable> $variable
     * @throws OutOfBoundsException
     */
    protected function isRegularVariable(AbstractNode $variable): bool
    {
        $node = $this->stripWrappedIndexExpression($variable);
        $parent = $node->getParent();

        if ($parent?->isInstanceOf(ASTPropertyPostfix::class)) {
            $primaryPrefix = $parent->getParent();
            $primaryPrefixParent = $primaryPrefix?->getParent()?->getNode();
            if ($primaryPrefixParent instanceof ASTMemberPrimaryPrefix) {
                return !$primaryPrefixParent->isStatic();
            }

            if (!$primaryPrefix) {
                return false;
            }

            $primaryPrefix = $primaryPrefix->getNode();

            return ($parent->getChild(0)->getNode() !== $node->getNode()
                || ($primaryPrefix instanceof ASTMemberPrimaryPrefix && !$primaryPrefix->isStatic())
            );
        }

        return true;
    }

    /**
     * Removes all index expressions that are wrapped around the given node
     * instance.
     *
     * @param AbstractNode<PDependNode> $node
     * @return AbstractNode<PDependNode>
     * @throws OutOfBoundsException
     */
    private function stripWrappedIndexExpression(AbstractNode $node): AbstractNode
    {
        if (!$this->isWrappedByIndexExpression($node)) {
            return $node;
        }

        $parent = $node->getParent();
        if ($parent?->getChild(0)->getNode() === $node->getNode()) {
            return $this->stripWrappedIndexExpression($parent);
        }

        return $node;
    }

    /**
     * Tests if the given variable node os part of an index expression.
     *
     * @param AbstractNode<PDependNode> $node
     */
    private function isWrappedByIndexExpression(AbstractNode $node): bool
    {
        $parent = $node->getParent();
        if (!$parent) {
            return false;
        }

        return $parent->isInstanceOf(ASTArrayIndexExpression::class)
            || $parent->isInstanceOf(ASTStringIndexExpression::class);
    }

    /**
     * AST puts namespace prefix to global functions called from a namespace.
     * This method checks if the last part of function fully qualified name is equal to $name
     *
     * @param AbstractNode<PDependNode> $node
     */
    protected function isFunctionNameEndingWith(AbstractNode $node, string $name): bool
    {
        $parts = explode('\\', trim($node->getImage(), '\\'));

        return (0 === strcasecmp(array_pop($parts), $name));
    }

    /**
     * Get the image of the given variable node.
     *
     * Prefix self:: and static:: properties with "::".
     *
     * @param AbstractNode<PDependNode>|PDependNode $variable
     * @throws OutOfBoundsException
     */
    protected function getVariableImage(AbstractNode|PDependNode $variable): string
    {
        if ($variable instanceof AbstractNode) {
            $variable = $variable->getNode();
        }
        $image = $variable->getImage();

        if ($this->isFieldDeclaration($variable, $image)) {
            $image = "::$image";
        }

        // If variable name is not in the node, it's in the second child
        if ($image === '::') {
            return $image . $variable->getChild(1)->getImage();
        }

        return $this->prependMemberPrimaryPrefix($image, $variable);
    }

    /**
     * @throws OutOfBoundsException
     */
    private function getParentMemberPrimaryPrefixImage(string $image, ASTPropertyPostfix $postfix): ?string
    {
        do {
            $postfix = $postfix->getParent();
        } while ($postfix?->getChildren() && $postfix->getChild(0)->getImage() === $image);

        $previousChildImage = $postfix?->getChild(0)->getImage();

        if (
            $postfix instanceof ASTMemberPrimaryPrefix &&
            in_array($previousChildImage, $this->selfReferences, true)
        ) {
            return $previousChildImage;
        }

        return null;
    }

    /**
     * Reflect function trying as namespaced function first, then global function.
     *
     * @SuppressWarnings(EmptyCatchBlock)
     */
    private function getReflectionFunctionByName(string $functionName): ?ReflectionFunction
    {
        try {
            return new ReflectionFunction($functionName);
        } catch (ReflectionException) {
            $chunks = explode('\\', $functionName);

            if (count($chunks) > 1) {
                try {
                    return new ReflectionFunction(end($chunks));
                } catch (ReflectionException) {
                }
                // @TODO: Find a way to handle user-land functions
                // @TODO: Find a way to handle methods
            }
        }

        return null;
    }

    /**
     * Return true if the given variable is passed by reference in a native PHP function.
     */
    protected function isPassedByReference(ASTVariable $variable): bool
    {
        $parent = $variable->getParent();

        if (!($parent instanceof ASTArguments)) {
            return false;
        }

        $argumentPosition = array_search($variable, $parent->getChildren(), true);
        $function = $parent->getParent();
        if ($function === null) {
            return false;
        }

        $functionParent = $function->getParent();
        $functionName = $function->getImage();

        if ($functionParent instanceof ASTMemberPrimaryPrefix) {
            // @TODO: Find a way to handle methods
            return false;
        }

        $reflectionFunction = $this->getReflectionFunctionByName($functionName);

        if (!$reflectionFunction) {
            return false;
        }

        $parameters = $reflectionFunction->getParameters();

        return isset($parameters[$argumentPosition]) && $parameters[$argumentPosition]->isPassedByReference();
    }

    /**
     * Prepend "::" if the variable has a ASTMemberPrimaryPrefix.
     *
     * So we can distinguish members from local variable, identify quickly the scope
     * by the image and mostly avoid conflict between a local variable and a property
     * having the same name such as in:
     *
     * ```
     * public function bar()
     * {
     *     self::$foo = 9;
     *     return $foo; // Undefined variable
     * }
     * ```
     *
     * We'll raise the violation because `$foo` and `self::$foo` are not referring the
     * same variable and won't overlap in a storage keyed by image as first one
     * image is "$foo", second one is "::$foo".
     *
     * @throws OutOfBoundsException
     */
    private function prependMemberPrimaryPrefix(string $image, PDependNode $variable): string
    {
        $parent = $variable->getParent();

        while ($parent instanceof ASTArrayIndexExpression && $parent->getChild(0) === $variable) {
            $variable = $parent;
            $parent = $variable->getParent();
        }

        if ($parent instanceof ASTPropertyPostfix) {
            $previousChildImage = $this->getParentMemberPrimaryPrefixImage($image, $parent);

            if (in_array($previousChildImage, $this->selfReferences, true)) {
                return "::$image";
            }
        }

        return $image;
    }

    /**
     * Return true if given node (+ optional image) represent en field declaration:
     *
     * ```
     * class Foo
     * {
     *   public static $field = 9;
     * }
     * ```
     */
    private function isFieldDeclaration(PDependNode $variable, string $image = '$'): bool
    {
        return str_starts_with($image, '$') &&
            $variable->getParent() instanceof ASTFieldDeclaration;
    }
}
