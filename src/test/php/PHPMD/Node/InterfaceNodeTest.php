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

use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTest;

/**
 * Test case for the interface node implementation.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @covers \PHPMD\Node\InterfaceNode
 * @covers \PHPMD\Node\AbstractTypeNode
 */
class InterfaceNodeTest extends AbstractTest
{
    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     *
     * @return void
     */
    public function testGetFullQualifiedNameReturnsExpectedValue()
    {
        $interface = new ASTInterface('MyInterface');
        $interface->setNamespace(new ASTNamespace('Sindelfingen'));

        $node = new InterfaceNode($interface);

        $this->assertSame('Sindelfingen\\MyInterface', $node->getFullQualifiedName());
    }

    /**
     * @return void
     */
    public function testGetConstantCountReturnsZeroByDefault()
    {
        $interface = new InterfaceNode(new ASTInterface('MyInterface'));
        $this->assertSame(0, $interface->getConstantCount());
    }

    /**
     * @return void
     */
    public function testGetConstantCount()
    {
        $class = $this->getInterface();
        $this->assertSame(3, $class->getConstantCount());
    }

    /**
     * @return void
     */
    public function testGetParentNameReturnsNull()
    {
        $interface = new InterfaceNode(new ASTInterface('MyInterface'));
        $this->assertNull($interface->getParentName());
    }
}
