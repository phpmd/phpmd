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

namespace PHPMD;

use BadMethodCallException;
use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTNode as PDependNode;
use PHPMD\Node\ASTNode;

/**
 * This is an abstract base class for PHPMD code nodes, it is just a wrapper
 * around PDepend's object model.
 *
 * @template TNode of \PDepend\Source\AST\ASTArtifact|PDependNode
 *
 * @mixin TNode
 */
abstract class AbstractNode
{
    /**
     * @var TNode $node
     */
    private $node = null;

    /**
     * The collected metrics for this node.
     *
     * @var array<string, mixed>
     */
    private $metrics = null;

    /**
     * Constructs a new PHPMD node.
     *
     * @param TNode $node
     */
    public function __construct($node)
    {
        $this->node = $node;
    }

    /**
     * The magic call method is used to pipe requests from rules direct
     * to the underlying PDepend AST node.
     *
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws BadMethodCallException When the underlying PDepend node
     *         does not contain a method named <b>$name</b>.
     */
    public function __call($name, array $args)
    {
        $node = $this->getNode();
        if (!method_exists($node, $name)) {
            throw new BadMethodCallException(
                sprintf('Invalid method %s() called.', $name)
            );
        }

        return $node->$name(...$args);
    }

    /**
     * Returns the parent of this node or <b>null</b> when no parent node
     * exists.
     *
     * @return AbstractNode|null
     */
    public function getParent()
    {
        $node = $this->node->getParent();
        if ($node === null) {
            return null;
        }

        return new ASTNode($node, $this->getFileName());
    }

    /**
     * Returns a child node at the given index.
     *
     * @param integer $index The child offset.
     * @return AbstractNode
     */
    public function getChild($index)
    {
        return new ASTNode(
            $this->node->getChild($index),
            $this->getFileName()
        );
    }

    /**
     * Returns the first child of the given type or <b>null</b> when this node
     * has no child of the given type.
     *
     * @template T of PDependNode
     *
     * @param class-string<T> $type The searched child type.
     * @return ASTNode<T>|null
     */
    public function getFirstChildOfType($type)
    {
        $node = $this->node->getFirstChildOfType($type);

        if ($node === null) {
            return null;
        }

        return new ASTNode($node, $this->getFileName());
    }

    /**
     * Searches recursive for all children of this node that are of the given
     * type.
     *
     * @template T of PDependNode
     *
     * @param class-string<T> $type The searched child type.
     *
     * @return array<int, ASTNode<T>>
     */
    public function findChildrenOfType($type)
    {
        $children = $this->node->findChildrenOfType($type);

        $nodes = [];

        foreach ($children as $child) {
            $nodes[] = new ASTNode($child, $this->getFileName());
        }

        return $nodes;
    }

    /**
     * List all first-level children of the nodes of the given type found in any depth of
     * the current node.
     *
     * @param class-string<PDependNode> $type The searched child type.
     * @return array<int, \PDepend\Source\AST\AbstractASTNode|\PDepend\Source\AST\ASTArtifact>
     */
    public function findChildrenWithParentType($type)
    {
        $children = $this->node->findChildrenOfType($type);

        $nodes = [];

        foreach ($children as $child) {
            foreach ($child->getChildren() as $subChild) {
                $nodes[] = $subChild;
            }
        }

        return $nodes;
    }

    /**
     * Searches recursive for all children of this node that are of variable.
     *
     * @return array<int, ASTNode<ASTVariable>>
     * @todo Cover by a test.
     */
    public function findChildrenOfTypeVariable()
    {
        return $this->findChildrenOfType('PDepend\Source\AST\ASTVariable');
    }

    /**
     * Tests if this node represents the the given type.
     *
     * @template T of PDependNode
     *
     * @param class-string<T> $class The expected node type.
     *
     * @phpstan-assert-if-true static<T> $this
     */
    public function isInstanceOf($class): bool
    {
        return $this->node instanceof $class;
    }

    /**
     * Returns the image of the underlying node.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->node->getName();
    }

    /**
     * Returns the source name for this node, maybe a class or interface name,
     * or a package, method, function name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->node->getName();
    }

    /**
     * Returns the begin line for this node in the php source code file.
     *
     * @return integer
     */
    public function getBeginLine()
    {
        return $this->node->getStartLine();
    }

    /**
     * Returns the end line for this node in the php source code file.
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->node->getEndLine();
    }

    /**
     * Returns the name of the declaring source file.
     *
     * @return string|null
     */
    public function getFileName()
    {
        $compilationUnit = $this->node instanceof AbstractASTArtifact
            ? $this->node->getCompilationUnit()
            : null;

        return $compilationUnit
            ? (string) $compilationUnit->getFileName()
            : null; // @TODO: Find the name from some parent node https://github.com/phpmd/phpmd/issues/837
    }

    /**
     * Returns the wrapped PDepend node instance.
     *
     * @return TNode
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Returns a textual representation/name for the concrete node type.
     *
     * @return string
     */
    public function getType()
    {
        $type = explode('\\', get_class($this));

        return preg_replace('(node$)', '', strtolower(array_pop($type)));
    }

    /**
     * This method will return the metric value for the given identifier or
     * <b>null</b> when no such metric exists.
     *
     * @param string $name The metric name or abbreviation.
     * @return mixed
     */
    public function getMetric($name)
    {
        if (isset($this->metrics[$name])) {
            return $this->metrics[$name];
        }

        return null;
    }

    /**
     * This method will set the metrics for this node.
     *
     * @param array<string, mixed> $metrics The collected node metrics.
     * @return void
     */
    public function setMetrics(array $metrics): void
    {
        if ($this->metrics === null) {
            $this->metrics = $metrics;
        }
    }

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    abstract public function hasSuppressWarningsAnnotationFor(Rule $rule);

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     *
     * @return string
     */
    abstract public function getFullQualifiedName();

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     *
     * @return string|null
     */
    abstract public function getParentName();

    /**
     * Returns the name of the parent package.
     *
     * @return string
     */
    abstract public function getNamespaceName();
}
