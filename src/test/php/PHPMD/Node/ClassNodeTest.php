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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTest;

/**
 * Test case for the class node implementation.
 *
 * @covers \PHPMD\Node\ClassNode
 * @covers \PHPMD\Node\AbstractTypeNode
 * @group phpmd
 * @group phpmd::node
 * @group unittest
 */
class ClassNodeTest extends AbstractTest
{
    /**
     * testGetMethodNamesReturnsExpectedResult
     *
     * @return void
     */
    public function testGetMethodNamesReturnsExpectedResult()
    {
        $class = new ASTClass(null);
        $class->addMethod(new ASTMethod(__CLASS__));
        $class->addMethod(new ASTMethod(__FUNCTION__));

        $node = new ClassNode($class);
        $this->assertEquals(array(__CLASS__, __FUNCTION__), $node->getMethodNames());
    }

    /**
     * testHasSuppressWarningsAnnotationForReturnsTrue
     *
     * @return void
     */
    public function testHasSuppressWarningsAnnotationForReturnsTrue()
    {
        $class = new ASTClass(null);
        $class->setComment('/** @SuppressWarnings("PMD") */');

        $rule = $this->getMock('PHPMD\\AbstractRule');

        $node = new ClassNode($class);

        $this->assertTrue($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     *
     * @return void
     */
    public function testGetFullQualifiedNameReturnsExpectedValue()
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('Sindelfingen'));

        $node = new ClassNode($class);

        $this->assertSame('Sindelfingen\\MyClass', $node->getFullQualifiedName());
    }

    /**
     * @return void
     */
    public function testGetConstantCountReturnsZeroByDefault()
    {
        $class = new ClassNode(new ASTClass('MyClass'));
        $this->assertSame(0, $class->getConstantCount());
    }

    /**
     * @return void
     */
    public function testGetConstantCount()
    {
        $class = $this->getClass();
        $this->assertSame(3, $class->getConstantCount());
    }

    /**
     * @return void
     */
    public function testGetParentNameReturnsNull()
    {
        $class = new ClassNode(new ASTClass('MyClass'));
        $this->assertNull($class->getParentName());
    }
}
