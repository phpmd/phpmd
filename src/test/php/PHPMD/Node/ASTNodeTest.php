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
use PHPMD\AbstractTestCase;

/**
 * Test case for the {@link \PHPMD\Node\ASTNode} class.
 *
 * @covers \PHPMD\Node\ASTNode
 */
class ASTNodeTest extends AbstractTestCase
{
    /**
     * testGetImageDelegatesToGetImageMethodOfWrappedNode
     */
    public function testGetImageDelegatesToGetImageMethodOfWrappedNode(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $mock->expects(static::once())
            ->method('getImage');

        $node = new ASTNode($mock, __FILE__);
        $node->getImage();
    }

    /**
     * testGetNameDelegatesToGetImageMethodOfWrappedNode
     */
    public function testGetNameDelegatesToGetImageMethodOfWrappedNode(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $mock->expects(static::once())
            ->method('getImage');

        $node = new ASTNode($mock, __FILE__);
        $node->getName();
    }

    /**
     * testHasSuppressWarningsAnnotationForAlwaysReturnsFalse
     */
    public function testHasSuppressWarningsAnnotationForAlwaysReturnsFalse(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();

        $node = new ASTNode($mock, __FILE__);
        $rule = $this->getRuleMock();

        static::assertFalse($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testGetParentNameReturnsNull
     */
    public function testGetParentNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getParentName());
    }

    /**
     * testGetNamespaceNameReturnsNull
     */
    public function testGetNamespaceNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getNamespaceName());
    }

    /**
     * testGetFullQualifiedNameReturnsNull
     */
    public function testGetFullQualifiedNameReturnsNull(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $node = new ASTNode($mock, __FILE__);

        static::assertNull($node->getFullQualifiedName());
    }
}
