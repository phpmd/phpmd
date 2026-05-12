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
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use PHPMD\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the method node implementation.
 */
#[CoversClass(AbstractCallableNode::class)]
#[CoversClass(MethodNode::class)]
class MethodNodeTest extends AbstractTestCase
{
    public function testMagicCallDelegatesToWrappedPHPDependMethod(): void
    {
        $method = $this->getMockBuilder(ASTMethod::class)->setConstructorArgs([null])->getMock();
        $method->expects(static::once())
            ->method('getStartLine');

        $node = new MethodNode($method);
        $node->getStartLine();
    }

    public function testGetParentTypeReturnsInterfaceForInterfaceMethod(): void
    {
        static::assertInstanceOf(
            InterfaceNode::class,
            $this->getMethod()->getParentType()
        );
    }

    public function testGetParentTypeReturnsClassForClassMethod(): void
    {
        static::assertInstanceOf(
            ClassNode::class,
            $this->getMethod()->getParentType()
        );
    }

    public function testGetParentTypeReturnsTrait(): void
    {
        static::assertInstanceOf(
            TraitNode::class,
            $this->getMethod()->getParentType()
        );
    }

    public function testHasSuppressWarningsExecutesDefaultImplementation(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsFor($rule));
    }

    public function testHasSuppressWarningsDelegatesToParentClassMethod(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsFor($rule));
    }

    public function testHasSuppressWarningsDelegatesToParentInterfaceMethod(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsFor($rule));
    }

    public function testHasSuppressWarningsIgnoresCaseFirstLetter(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('FooBar');

        $method = $this->getMethod();
        static::assertTrue($method->hasSuppressWarningsFor($rule));
    }

    /**
     * testIsDeclarationReturnsTrueForMethodDeclaration
     *
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
     * @since 1.2.1
     */
    public function testIsDeclarationReturnsFalseForImplementedInterfaceMethod(): void
    {
        $method = $this->getMethod();
        static::assertFalse($method->isDeclaration());
    }

    public function testIsDeclarationReturnsTrueForPrivateMethod(): void
    {
        $method = $this->getMethod();
        static::assertTrue($method->isDeclaration());
    }

    public function testGetFullQualifiedNameReturnsExpectedValue(): void
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('Sindelfingen'));

        $method = new ASTMethod('beer');
        $method->setParent($class);

        $node = new MethodNode($method);

        static::assertSame('Sindelfingen\\MyClass::beer()', $node->getFullQualifiedName());
    }

    public function testIsDeclarationReturnsFalseForInheritedDeclaration(): void
    {
        $dir = __DIR__ . '/../../../resources/files/classes/inheritance';
        $builder = new PHPBuilder();

        foreach (['Foo.php', 'Bar.php', 'Baz.php'] as $file) {
            $tokenizer = new PHPTokenizerInternal();
            $tokenizer->setSourceFile($dir . '/' . $file);
            $parser = new PHPParserGeneric($tokenizer, $builder, new MemoryCacheDriver());
            $parser->parse();
        }

        $namespace = $builder->getNamespaces()->current();
        static::assertNotFalse($namespace);

        $bazClass = null;
        foreach ($namespace->getTypes() as $type) {
            if ($type instanceof ASTClass && $type->getImage() === 'Baz') {
                $bazClass = $type;
            }
        }
        static::assertNotNull($bazClass);

        $bazMethod = null;
        foreach ($bazClass->getMethods() as $m) {
            if (strtolower($m->getImage()) === 'baz') {
                $bazMethod = $m;
            }
        }
        static::assertNotNull($bazMethod);

        $method = new MethodNode($bazMethod);
        static::assertFalse($method->isDeclaration());
    }
}
