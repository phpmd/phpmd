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
use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Test case for the method node implementation.
 *
 * @covers \PHPMD\Node\AbstractCallableNode
 * @covers \PHPMD\Node\MethodNode
 */
class MethodNodeTest extends AbstractTestCase
{
    /**
     * testMagicCallDelegatesToWrappedPHPDependMethod
     * @throws Throwable
     */
    public function testMagicCallDelegatesToWrappedPHPDependMethod(): void
    {
        $method = $this->getMockBuilder(ASTMethod::class)->setConstructorArgs([null])->getMock();
        $method->expects(static::once())
            ->method('getStartLine');

        $node = new MethodNode($method);
        $node->getStartLine();
    }

    /**
     * testGetParentTypeReturnsInterfaceForInterfaceMethod
     * @throws Throwable
     */
    public function testGetParentTypeReturnsInterfaceForInterfaceMethod(): void
    {
        static::assertInstanceOf(
            InterfaceNode::class,
            $this->getMethod()->getParentType()
        );
    }

    /**
     * testGetParentTypeReturnsClassForClassMethod
     * @throws Throwable
     */
    public function testGetParentTypeReturnsClassForClassMethod(): void
    {
        static::assertInstanceOf(
            ClassNode::class,
            $this->getMethod()->getParentType()
        );
    }

    /**
     * @throws Throwable
     */
    public function testGetParentTypeReturnsTrait(): void
    {
        static::assertInstanceOf(
            TraitNode::class,
            $this->getMethod()->getParentType()
        );
    }

    /**
     * testHasSuppressWarningsExecutesDefaultImplementation
     * @throws Throwable
     */
    public function testHasSuppressWarningsExecutesDefaultImplementation(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsDelegatesToParentClassMethod
     * @throws Throwable
     */
    public function testHasSuppressWarningsDelegatesToParentClassMethod(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsDelegatesToParentInterfaceMethod
     * @throws Throwable
     */
    public function testHasSuppressWarningsDelegatesToParentInterfaceMethod(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testHasSuppressWarningsIgnoresCaseFirstLetter
     * @throws Throwable
     */
    public function testHasSuppressWarningsIgnoresCaseFirstLetter(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsAnnotationFor($rule));
    }

    /**
     * testIsDeclarationReturnsTrueForMethodDeclaration
     *
     * @throws Throwable
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsTrueForMethodDeclaration(): void
    {
        $method = $this->getMethod();
        static::assertTrue($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsTrueForMethodDeclarationWithParent
     *
     * @throws Throwable
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsTrueForMethodDeclarationWithParent(): void
    {
        $method = $this->getMethod();
        static::assertTrue($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsFalseForInheritMethodDeclaration
     *
     * @throws Throwable
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForInheritMethodDeclaration(): void
    {
        $method = $this->getMethod();
        static::assertFalse($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsFalseForImplementedAbstractMethod
     *
     * @throws Throwable
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForImplementedAbstractMethod(): void
    {
        $method = $this->getMethod();
        static::assertFalse($method->isDeclaration());
    }

    /**
     * testIsDeclarationReturnsFalseForImplementedInterfaceMethod
     *
     * @throws Throwable
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForImplementedInterfaceMethod(): void
    {
        $method = $this->getMethod();
        static::assertFalse($method->isDeclaration());
    }

    /**
     * @throws Throwable
     */
    public function testIsDeclarationReturnsTrueForPrivateMethod(): void
    {
        $method = $this->getMethod();
        static::assertTrue($method->isDeclaration());
    }

    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     * @throws Throwable
     */
    public function testGetFullQualifiedNameReturnsExpectedValue(): void
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('Sindelfingen'));

        $method = new ASTMethod('beer');
        $method->setParent($class);

        $node = new MethodNode($method);

        static::assertSame('Sindelfingen\\MyClass::beer()', $node->getFullQualifiedName());
    }
}
