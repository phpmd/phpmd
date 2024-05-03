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
 *
 * @link http://phpmd.org/
 */

namespace PHPMD\Node;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractRule;
use PHPMD\AbstractTestCase;
use PHPMD\Rule\Design\CouplingBetweenObjects;
use Sindelfingen\MyClass;

/**
 * Test case for the class node implementation.
 *
 * @covers \PHPMD\Node\ClassNode
 * @covers \PHPMD\Node\AbstractTypeNode
 */
class ClassNodeTest extends AbstractTestCase
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
        $this->assertEquals([__CLASS__, __FUNCTION__], $node->getMethodNames());
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

        $rule = $this->getMockFromBuilder($this->getMockBuilder(AbstractRule::class));

        $node = new ClassNode($class);

        $this->assertTrue($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsWithRuleNameContainingSlashes
     *
     * @return void
     */
    public function testHasSuppressWarningsWithRuleNameContainingSlashes()
    {
        $class = new ASTClass(null);
        $class->setComment('/** @SuppressWarnings(PMD.CouplingBetweenObjects) */');

        $rule = new CouplingBetweenObjects();
        $rule->setName('rulesets/design.xml/CouplingBetweenObjects');

        $node = new ClassNode($class);

        $this->assertTrue($node->hasSuppressWarningsAnnotationFor($rule));

        $class = new ASTClass(null);
        $class->setComment('/** @SuppressWarnings(PMD.TooManyFields) */');

        $rule = new CouplingBetweenObjects();
        $rule->setName('rulesets/design.xml/CouplingBetweenObjects');

        $node = new ClassNode($class);

        $this->assertFalse($node->hasSuppressWarningsAnnotationFor($rule));
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

        $this->assertSame(MyClass::class, $node->getFullQualifiedName());
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
