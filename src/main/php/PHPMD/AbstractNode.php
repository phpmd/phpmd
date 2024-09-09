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

use LogicException;
use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTVariable;
use PHPMD\Node\ASTNode;

/**
 * This is an abstract base class for PHPMD code nodes, it is just a wrapper
 * around PDepend's object model.
 *
 * @template-covariant TNode of PDependNode
 *
 * @mixin TNode
 */
abstract class AbstractNode
{
    /**
     * The collected metrics for this node.
     *
     * @var array<string, numeric>
     */
    private array $metrics;

    /**
     * Constructs a new PHPMD node.
     *
     * @param TNode $node
     */
    public function __construct(
        private readonly PDependNode $node,
    ) {
    }

    /**
     * The magic call method is used to pipe requests from rules direct
     * to the underlying PDepend AST node.
     *
     * @param array<mixed> $args
     */
    public function __call(string $name, array $args): mixed
    {
        return $this->node->{$name}(...$args);
    }

    /**
     * Returns the parent of this node or <b>null</b> when no parent node
     * exists.
     *
     * @return AbstractNode<PDependNode>|null
     */
    public function getParent(): ?self
    {
        $node = $this->node->getParent();
        if ($node === null) {
            return null;
        }

        return new ASTNode($node, $this->getFileName());
    }

    /**
     * Returns the first parent node of the specified type
     *
     * @template T of PDependNode
     *
     * @param class-string<T> $type The searched parent type.
     * @return AbstractNode<T>|null
     */
    public function getParentOfType($type): ?self
    {
        $parent = $this->node->getParent();

        while ($parent) {
            if ($parent instanceof $type) {
                return new ASTNode($parent, $this->getFileName());
            }
            $parent = $parent->getParent();
        }

        return null;
    }

    /**
     * Returns a child node at the given index.
     *
     * @param int $index The child offset.
     * @return AbstractNode<PDependNode>
     * @throws OutOfBoundsException
     */
    public function getChild(int $index): self
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
     * @return AbstractNode<T>|null
     */
    public function getFirstChildOfType($type): ?self
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
     * @param class-string<T> $type The searched child type.
     * @return list<AbstractNode<T>>
     */
    public function findChildrenOfType($type): array
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
     * @return list<PDependNode>
     */
    public function findChildrenWithParentType($type): array
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
     * @return array<int, AbstractNode<ASTVariable>>
     * @todo Cover by a test.
     */
    public function findChildrenOfTypeVariable(): array
    {
        return $this->findChildrenOfType(ASTVariable::class);
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
     */
    public function getImage(): string
    {
        return $this->node->getImage();
    }

    /**
     * Returns the source name for this node, maybe a class or interface name,
     * or a package, method, function name.
     */
    public function getName(): string
    {
        return $this->node->getImage();
    }

    /**
     * Returns the begin line for this node in the php source code file.
     */
    public function getBeginLine(): int
    {
        return $this->node->getStartLine();
    }

    /**
     * Returns the end line for this node in the php source code file.
     */
    public function getEndLine(): int
    {
        return $this->node->getEndLine();
    }

    /**
     * Returns the name of the declaring source file.
     */
    public function getFileName(): ?string
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
    public function getNode(): PDependNode
    {
        return $this->node;
    }

    /**
     * Returns a textual representation/name for the concrete node type.
     */
    public function getType(): string
    {
        $type = explode('\\', static::class);

        $type = strtolower(array_pop($type));

        return preg_replace('(node$)', '', $type) ?? $type;
    }

    /**
     * This method will return the metric value for the given identifier or
     * <b>null</b> when no such metric exists.
     *
     * @param string $name The metric name or abbreviation.
     * @return ?numeric $name
     */
    public function getMetric(string $name): mixed
    {
        return $this->metrics[$name] ?? null;
    }

    /**
     * This method will set the metrics for this node.
     *
     * @param array<string, numeric> $metrics The collected node metrics.
     * @throws LogicException
     */
    public function setMetrics(array $metrics): void
    {
        if (isset($this->metrics)) {
            throw new LogicException('Metrics cannot be overridden');
        }

        $this->metrics = $metrics;
    }

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     */
    abstract public function hasSuppressWarningsAnnotationFor(Rule $rule): bool;

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     */
    abstract public function getFullQualifiedName(): ?string;

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     */
    abstract public function getParentName(): ?string;

    /**
     * Returns the name of the parent package.
     */
    abstract public function getNamespaceName(): ?string;
}
