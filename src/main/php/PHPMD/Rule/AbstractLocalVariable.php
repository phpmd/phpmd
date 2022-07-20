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

use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use ReflectionException;
use ReflectionFunction;

/**
 * Base class for rules that rely on local variables.
 *
 * @since 0.2.6
 */
abstract class AbstractLocalVariable extends AbstractRule
{
    /**
     * @var array Self reference class names.
     */
    protected $selfReferences = array('self', 'static');

    /**
     * PHP super globals that are available in all php scopes, so that they
     * can never be unused local variables.
     *
     * @var array(string=>boolean)
     * @link http://php.net/manual/en/reserved.variables.php
     */
    protected static $superGlobals = array(
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
    );

    /**
     * Tests if the given variable node represents a local variable or if it is
     * a static object property or something similar.
     *
     * @param \PHPMD\Node\ASTNode $variable The variable to check.
     * @return boolean
     */
    protected function isLocal(ASTNode $variable)
    {
        return (false === $variable->isThis()
            && $this->isNotSuperGlobal($variable)
            && $this->isRegularVariable($variable)
        );
    }

    /**
     * Tests if the given variable represents one of the PHP super globals
     * that are available in scopes.
     *
     * @param \PHPMD\AbstractNode $variable
     * @return boolean
     */
    protected function isSuperGlobal(AbstractNode $variable)
    {
        return isset(self::$superGlobals[$variable->getImage()]);
    }

    /**
     * Tests if the given variable does not represent one of the PHP super globals
     * that are available in scopes.
     *
     * @param \PHPMD\AbstractNode $variable
     * @return boolean
     */
    protected function isNotSuperGlobal(AbstractNode $variable)
    {
        return !$this->isSuperGlobal($variable);
    }

    /**
     * Tests if the given variable node is a regular variable an not property
     * or method postfix.
     *
     * @param \PHPMD\Node\ASTNode $variable
     * @return boolean
     */
    protected function isRegularVariable(ASTNode $variable)
    {
        $node = $this->stripWrappedIndexExpression($variable);
        $parent = $node->getParent();

        if ($parent->isInstanceOf('PropertyPostfix')) {
            $primaryPrefix = $parent->getParent();
            if ($primaryPrefix->getParent()->isInstanceOf('MemberPrimaryPrefix')) {
                return !$primaryPrefix->getParent()->isStatic();
            }

            return ($parent->getChild(0)->getNode() !== $node->getNode()
                || !$primaryPrefix->isStatic()
            );
        }

        return true;
    }

    /**
     * Removes all index expressions that are wrapped around the given node
     * instance.
     *
     * @param \PHPMD\Node\ASTNode $node
     * @return \PHPMD\Node\ASTNode
     */
    protected function stripWrappedIndexExpression(ASTNode $node)
    {
        if (false === $this->isWrappedByIndexExpression($node)) {
            return $node;
        }

        $parent = $node->getParent();
        if ($parent->getChild(0)->getNode() === $node->getNode()) {
            return $this->stripWrappedIndexExpression($parent);
        }

        return $node;
    }

    /**
     * Tests if the given variable node os part of an index expression.
     *
     * @param \PHPMD\Node\ASTNode $node
     * @return boolean
     */
    protected function isWrappedByIndexExpression(ASTNode $node)
    {
        return ($node->getParent()->isInstanceOf('ArrayIndexExpression')
            || $node->getParent()->isInstanceOf('StringIndexExpression')
        );
    }

    /**
     * PHP is case insensitive so we should compare function names case
     * insensitive.
     *
     * @param \PHPMD\AbstractNode $node
     * @param string $name
     * @return boolean
     */
    protected function isFunctionNameEqual(AbstractNode $node, $name)
    {
        return (0 === strcasecmp(trim($node->getImage(), '\\'), $name));
    }

    /**
     * AST puts namespace prefix to global functions called from a namespace.
     * This method checks if the last part of function fully qualified name is equal to $name
     *
     * @param \PHPMD\AbstractNode $node
     * @param string $name
     * @return boolean
     */
    protected function isFunctionNameEndingWith(AbstractNode $node, $name)
    {
        $parts = explode('\\', trim($node->getImage(), '\\'));

        return (0 === strcasecmp(array_pop($parts), $name));
    }

    /**
     * Get the image of the given variable node.
     *
     * Prefix self:: and static:: properties with "::".
     *
     * @param ASTVariable|ASTPropertyPostfix|ASTVariableDeclarator $variable
     * @return string
     */
    protected function getVariableImage($variable)
    {
        $image = $variable->getImage();

        if ($this->isFieldDeclaration($variable, $image)) {
            $image = "::$image";
        }

        // If variable name is not in the node, it's in the second child
        if ($image === '::') {
            return $image.$variable->getChild(1)->getImage();
        }

        return $this->prependMemberPrimaryPrefix($image, $variable);
    }

    protected function getParentMemberPrimaryPrefixImage($image, ASTPropertyPostfix $postfix)
    {
        do {
            $postfix = $postfix->getParent();
        } while ($postfix && $postfix->getChild(0) && $postfix->getChild(0)->getImage() === $image);

        $previousChildImage = $postfix->getChild(0)->getImage();

        if ($postfix instanceof ASTMemberPrimaryPrefix &&
            in_array($previousChildImage, $this->selfReferences)
        ) {
            return $previousChildImage;
        }

        return null;
    }

    /**
     * Return the PDepend node of ASTNode PHPMD node.
     *
     * Or return the input as is if it's not an ASTNode PHPMD node.
     *
     * @param mixed $node
     * @return \PDepend\Source\AST\ASTArtifact|\PDepend\Source\AST\ASTNode
     */
    protected function getNode($node)
    {
        if ($node instanceof ASTNode) {
            return $node->getNode();
        }

        return $node;
    }

    /**
     * Reflect function trying as namespaced function first, then global function.
     *
     * @SuppressWarnings(PHPMD.EmptyCatchBlock)
     * @param string $functionName
     * @return ReflectionFunction|null
     */
    private function getReflectionFunctionByName($functionName)
    {
        try {
            return new ReflectionFunction($functionName);
        } catch (ReflectionException $exception) {
            $chunks = explode('\\', $functionName);

            if (count($chunks) > 1) {
                try {
                    return new ReflectionFunction(end($chunks));
                } catch (ReflectionException $exception) {
                }
                // @TODO: Find a way to handle user-land functions
                // @TODO: Find a way to handle methods
            }
        }

        return null;
    }

    /**
     * Return true if the given variable is passed by reference in a native PHP function.
     *
     * @param ASTVariable|ASTPropertyPostfix|ASTVariableDeclarator $variable
     * @return bool
     */
    protected function isPassedByReference($variable)
    {
        $parent = $this->getNode($variable->getParent());

        if (!($parent && $parent instanceof ASTArguments)) {
            return false;
        }

        $argumentPosition = array_search($this->getNode($variable), $parent->getChildren());
        $function = $this->getNode($parent->getParent());
        $functionParent = $this->getNode($function->getParent());
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
     * @param ASTVariable|ASTPropertyPostfix|ASTVariableDeclarator $variable
     * @return string
     */
    private function prependMemberPrimaryPrefix($image, $variable)
    {
        $base = $this->getNode($variable);
        $parent = $this->getNode($base->getParent());

        while ($parent && $parent instanceof ASTArrayIndexExpression && $parent->getChild(0) === $base) {
            $base = $parent;
            $parent = $this->getNode($base->getParent());
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
     *
     * @param ASTVariable|ASTPropertyPostfix|ASTVariableDeclarator $variable
     * @param string $image
     * @return bool
     */
    private function isFieldDeclaration($variable, $image = '$')
    {
        return substr($image, 0, 1) === '$' &&
            $this->getNode($variable->getParent()) instanceof ASTFieldDeclaration;
    }
}
