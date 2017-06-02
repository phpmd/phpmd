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

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\Node\ASTNode} class.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Node\ASTNode
 * @group phpmd
 * @group phpmd::node
 * @group unittest
 */
class ASTNodeTest extends AbstractTest
{
    /**
     * testGetImageDelegatesToGetImageMethodOfWrappedNode
     *
     * @return void
     */
    public function testGetImageDelegatesToGetImageMethodOfWrappedNode()
    {
        $mock = $this->getMock('PDepend\Source\AST\ASTNode');
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
        $mock = $this->getMock('PDepend\Source\AST\ASTNode');
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
        $mock = $this->getMock('PDepend\Source\AST\ASTNode');

        $node = new ASTNode($mock, __FILE__);
        $rule = $this->getMockForAbstractClass('PHPMD\\AbstractRule');

        $this->assertFalse($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testGetParentNameReturnsNull
     *
     * @return void
     */
    public function testGetParentNameReturnsNull()
    {
        $mock = $this->getMock('PDepend\Source\AST\ASTNode');
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
        $mock = $this->getMock('PDepend\Source\AST\ASTNode');
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
        $mock = $this->getMock('PDepend\Source\AST\ASTNode');
        $node = new ASTNode($mock, __FILE__);

        $this->assertNull($node->getFullQualifiedName());
    }
}
