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
     *
     * @return void
     */
    public function testGetImageDelegatesToGetImageMethodOfWrappedNode()
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $mock->expects($this->once())
            ->method('getImage');

        $node = new ASTNode($mock, __FILE__);
        $node->getImage();
    }

    /**
     * testGetNameDelegatesToGetImageMethodOfWrappedNode
     *
     * @return void
     */
    public function testGetNameDelegatesToGetImageMethodOfWrappedNode()
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $mock->expects($this->once())
            ->method('getImage');

        $node = new ASTNode($mock, __FILE__);
        $node->getName();
    }

    /**
     * testHasSuppressWarningsAnnotationForAlwaysReturnsFalse
     *
     * @return void
     */
    public function testHasSuppressWarningsAnnotationForAlwaysReturnsFalse()
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));

        $node = new ASTNode($mock, __FILE__);
        $rule = $this->getRuleMock();

        $this->assertFalse($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testGetParentNameReturnsNull
     *
     * @return void
     */
    public function testGetParentNameReturnsNull()
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $node = new ASTNode($mock, __FILE__);

        $this->assertNull($node->getParentName());
    }

    /**
     * testGetNamespaceNameReturnsNull
     *
     * @return void
     */
    public function testGetNamespaceNameReturnsNull()
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $node = new ASTNode($mock, __FILE__);

        $this->assertNull($node->getNamespaceName());
    }

    /**
     * testGetFullQualifiedNameReturnsNull
     *
     * @return void
     */
    public function testGetFullQualifiedNameReturnsNull()
    {
        $mock = $this->getMockFromBuilder($this->getMockBuilder(PDependNode::class));
        $node = new ASTNode($mock, __FILE__);

        $this->assertNull($node->getFullQualifiedName());
    }
}
