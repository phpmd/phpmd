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

namespace PHPMD\Node;

use BadMethodCallException;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\ASTVisitor\ASTVisitor;
use PHPMD\AbstractNode;
use PHPMD\AbstractTestCase;
use PHPMD\Rule;

/**
 * Test case for the {@link \PHPMD\Node\ASTNode} class.
 *
 * @covers \PHPMD\Node\ASTNode
 */
class ASTNodeTest extends AbstractTestCase
{
    public function testGetImageDelegatesToGetImageMethodOfWrappedNode(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $mock->expects(static::once())
            ->method('getImage')
            ->willReturn('');

        $node = new ASTNode($mock, __FILE__);
        $node->getImage();
    }

    public function testCallInvalidMethodOfWrappedNode(): void
    {
        self::expectExceptionObject(new BadMethodCallException('Invalid method doesNotExists() called.'));

        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));

        $node = new ASTNode($mock, __FILE__);
        $node->doesNotExists();
    }

    public function testGetNameDelegatesToGetImageMethodOfWrappedNode(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $mock->expects(static::once())
            ->method('getImage')
            ->willReturn('');

        $node = new ASTNode($mock, __FILE__);
        $node->getName();
    }

    public function testHasSuppressWarningsAnnotationForAlwaysReturnsFalse(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();

        $node = new ASTNode($mock, __FILE__);
        $rule = $this->getRuleMock();

        static::assertFalse($node->hasSuppressWarningsAnnotationFor($rule));
    }

    public function testGetParentReturnsNull(): void
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getParent());
        static::assertNull($node->getParentOfType('FooBar'));
    }

    public function testGetFirstChildOfTypeReturnsNull(): void
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getFirstChildOfType('FooBar'));
    }

    public function testGetParentNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getParentName());
    }

    public function testGetFileNameReturnsNull(): void
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $node = new ASTNode($mock, null);

        static::assertNull($node->getFileName());

        $node = new class (new ASTVariable('$a')) extends AbstractNode {
            public function accept(ASTVisitor $visitor, $data = []): mixed
            {
            }

            public function getStartLine(): int
            {
            }

            public function getStartColumn(): int
            {
            }

            public function getEndColumn(): int
            {
            }

            public function getChildren(): array
            {
            }

            public function setParent(?PDependNode $node): void
            {
            }

            public function getParentsOfType($parentType): array
            {
            }

            public function getComment(): string
            {
            }

            public function setComment($comment): void
            {
            }

            public function configureLinesAndColumns($startLine, $endLine, $startColumn, $endColumn): void
            {
            }

            public function hasSuppressWarningsAnnotationFor(Rule $rule): bool
            {
            }

            public function getFullQualifiedName(): ?string
            {
            }

            public function getParentName(): ?string
            {
            }

            public function getNamespaceName(): ?string
            {
            }
        };

        static::assertNull($node->getFileName());
    }

    public function testGetNamespaceNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getNamespaceName());
    }

    public function testGetFullQualifiedNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getFullQualifiedName());
    }
}
