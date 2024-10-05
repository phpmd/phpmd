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

use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTTrait;
use PHPMD\AbstractTestCase;

/**
 * Test case for the trait node implementation.
 *
 * @covers \PHPMD\Node\AbstractTypeNode
 * @covers \PHPMD\Node\TraitNode
 */
class TraitNodeTest extends AbstractTestCase
{
    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     */
    public function testGetFullQualifiedNameReturnsExpectedValue(): void
    {
        $trait = new ASTTrait('MyTrait');
        $trait->setNamespace(new ASTNamespace('Sindelfingen'));

        $node = new TraitNode($trait);

        static::assertSame('Sindelfingen\\MyTrait', $node->getFullQualifiedName());
    }

    public function testGetConstantCountReturnsZeroByDefault(): void
    {
        $trait = new TraitNode(new ASTTrait('MyTrait'));
        static::assertSame(0, $trait->getConstantCount());
    }

    public function testGetParentNameReturnsNull(): void
    {
        $trait = new TraitNode(new ASTTrait('MyTrait'));
        static::assertNull($trait->getParentName());
    }
}
