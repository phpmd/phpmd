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
use PHPMD\AbstractRule;
use PHPMD\AbstractTestCase;
use PHPMD\Rule\Design\CouplingBetweenObjects;
use Sindelfingen\MyClass;

/**
 * Test case for the class node implementation.
 *
 * @covers \PHPMD\Node\AbstractTypeNode
 * @covers \PHPMD\Node\ClassNode
 */
class ClassNodeTest extends AbstractTestCase
{
    /**
     * testGetMethodNamesReturnsExpectedResult
     */
    public function testGetMethodNamesReturnsExpectedResult(): void
    {
        $class = new ASTClass(null);
        $class->addMethod(new ASTMethod(__CLASS__));
        $class->addMethod(new ASTMethod(__FUNCTION__));

        $node = new ClassNode($class);
        static::assertEquals([__CLASS__, __FUNCTION__], $node->getMethodNames());
    }

    /**
     * testHasSuppressWarningsAnnotationForReturnsTrue
     */
    public function testHasSuppressWarningsAnnotationForReturnsTrue(): void
    {
        $class = new ASTClass(null);
        $class->setComment('/** @SuppressWarnings("PMD") */');

        $rule = $this->getMockFromBuilder($this->getMockBuilder(AbstractRule::class));

        $node = new ClassNode($class);

        static::assertTrue($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsWithRuleNameContainingSlashes
     */
    public function testHasSuppressWarningsWithRuleNameContainingSlashes(): void
    {
        $class = new ASTClass(null);
        $class->setComment('/** @SuppressWarnings(PMD.CouplingBetweenObjects) */');

        $rule = new CouplingBetweenObjects();
        $rule->setName('rulesets/design.xml/CouplingBetweenObjects');

        $node = new ClassNode($class);

        static::assertTrue($node->hasSuppressWarningsAnnotationFor($rule));

        $class = new ASTClass(null);
        $class->setComment('/** @SuppressWarnings(PMD.TooManyFields) */');

        $rule = new CouplingBetweenObjects();
        $rule->setName('rulesets/design.xml/CouplingBetweenObjects');

        $node = new ClassNode($class);

        static::assertFalse($node->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     */
    public function testGetFullQualifiedNameReturnsExpectedValue(): void
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('Sindelfingen'));

        $node = new ClassNode($class);

        static::assertSame(MyClass::class, $node->getFullQualifiedName());
    }

    public function testGetConstantCountReturnsZeroByDefault(): void
    {
        $class = new ClassNode(new ASTClass('MyClass'));
        static::assertSame(0, $class->getConstantCount());
    }

    public function testGetConstantCount(): void
    {
        $class = $this->getClass();
        static::assertSame(3, $class->getConstantCount());
    }

    public function testGetParentNameReturnsNull(): void
    {
        $class = new ClassNode(new ASTClass('MyClass'));
        static::assertNull($class->getParentName());
    }
}
