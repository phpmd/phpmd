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

use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\ASTVisitor\ASTVisitor;
use PHPMD\AbstractNode;
use PHPMD\AbstractTestCase;
use PHPMD\Rule;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the {@link \PHPMD\Node\ASTNode} class.
 */
#[CoversClass(ASTNode::class)]
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

        static::assertFalse($node->hasSuppressWarningsFor($rule));
    }

    public function testGetParentReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getParent());
        static::assertNull($node->getParentOfType(PDependNode::class));
    }

    public function testGetFirstChildOfTypeReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getFirstChildOfType(PDependNode::class));
    }

    public function testGetParentNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getParentName());
    }

    public function testGetFileNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, null);

        static::assertNull($node->getFileName());

        $node = new class (new ASTVariable('$a')) extends AbstractNode {
            /**
             * @param mixed[] $data
             */
            public function accept(ASTVisitor $visitor, $data = []): mixed
            {
                return null;
            }

            public function getStartLine(): int
            {
                return 0;
            }

            public function getStartColumn(): int
            {
                return 0;
            }

            public function getEndColumn(): int
            {
                return 0;
            }

            /**
             * @return list<AbstractNode<PDependNode>>
             */
            public function getChildren(): array
            {
                return [];
            }

            public function setParent(?PDependNode $node): void
            {
            }

            /**
             * @param class-string $parentType
             * @templte T of ASTNode
             * @return list<AbstractNode<PDependNode>>
             */
            public function getParentsOfType($parentType): array
            {
                return [];
            }

            public function getComment(): string
            {
                return '';
            }

            /**
             * @param string $comment
             */
            public function setComment($comment): void
            {
            }

            /**
             * @param int $startLine
             * @param int $endLine
             * @param int $startColumn
             * @param int $endColumn
             */
            public function configureLinesAndColumns($startLine, $endLine, $startColumn, $endColumn): void
            {
            }

            public function hasSuppressWarningsFor(Rule $rule): bool
            {
                return false;
            }

            public function getFullQualifiedName(): ?string
            {
                return null;
            }

            public function getParentName(): ?string
            {
                return null;
            }

            public function getNamespaceName(): ?string
            {
                return null;
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
