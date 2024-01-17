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

use PDepend\Source\AST\ASTTrait;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTestCase;

/**
 * Test case for the trait node implementation.
 *
 * @covers \PHPMD\Node\TraitNode
 * @covers \PHPMD\Node\AbstractTypeNode
 */
class TraitNodeTest extends AbstractTestCase
{
    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     *
     * @return void
     */
    public function testGetFullQualifiedNameReturnsExpectedValue()
    {
        $trait = new ASTTrait('MyTrait');
        $trait->setNamespace(new ASTNamespace('Sindelfingen'));

        $node = new TraitNode($trait);

        $this->assertSame('Sindelfingen\\MyTrait', $node->getFullQualifiedName());
    }

    /**
     * @return void
     */
    public function testGetConstantCountReturnsZeroByDefault()
    {
        $trait = new TraitNode(new ASTTrait('MyTrait'));
        $this->assertSame(0, $trait->getConstantCount());
    }

    /**
     * @return void
     */
    public function testGetParentNameReturnsNull()
    {
        $trait = new TraitNode(new ASTTrait('MyTrait'));
        $this->assertNull($trait->getParentName());
    }
}
