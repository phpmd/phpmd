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

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTAnonymousClass;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTScope;
use PDepend\Source\AST\ASTStatement;
use PHPMD\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test case for the {@link \PHPMD\Node\Annotations} class.
 */
#[CoversClass(Annotations::class)]
class AnnotationsTest extends AbstractTestCase
{
    public function testCollectionReturnsFalseWhenNoAnnotationExists(): void
    {
        $annotations = new Annotations($this->getClassMock());
        static::assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    public function testCollectionReturnsFalseWhenNoMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects(static::once())
            ->method('__call')
            ->with(static::equalTo('getComment'))
            ->willReturn(
                '/**
                  * @SuppressWarnings("Foo")
                  * @SuppressWarnings("Bar")
                  * @SuppressWarnings("Baz")
                  */'
            );

        $annotations = new Annotations($class);
        static::assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    public function testCollectionReturnsTrueWhenMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects(static::once())
            ->method('__call')
            ->with(static::equalTo('getComment'))
            ->willReturn('/** @SuppressWarnings("PMD") */');

        $annotations = new Annotations($class);
        static::assertTrue($annotations->suppresses($this->getRuleMock()));
    }

    public function testCollectionReturnsTrueWhenOneMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects(static::once())
            ->method('__call')
            ->with(static::equalTo('getComment'))
            ->willReturn(
                '/**
                  * @SuppressWarnings("FooBar")
                  * @SuppressWarnings("PMD")
                  */'
            );

        $annotations = new Annotations($class);
        static::assertTrue($annotations->suppresses($this->getRuleMock()));
    }

    public function testAnonymousClassReadsPrecedingDocCommentOnEnclosingStatement(): void
    {
        $anonymousClass = $this->createAnonymousClassInScope('/** @SuppressWarnings("PMD") */');

        $annotations = new Annotations(new ClassNode($anonymousClass));
        static::assertTrue($annotations->suppresses($this->getRuleMock()));
    }

    public function testAnonymousClassIgnoresPrecedingNonDocComment(): void
    {
        $anonymousClass = $this->createAnonymousClassInScope('// @SuppressWarnings("PMD")');

        $annotations = new Annotations(new ClassNode($anonymousClass));
        static::assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    private function createAnonymousClassInScope(string $commentText): ASTAnonymousClass
    {
        $comment = new ASTComment($commentText);
        $comment->configureLinesAndColumns(9, 9, 9, 40);

        $anonymousClass = new ASTAnonymousClass('class@anonymous');
        $anonymousClass->configureLinesAndColumns(10, 14, 15, 10);

        $allocation = new ASTAllocationExpression('new');
        $allocation->configureLinesAndColumns(10, 14, 9, 10);
        $allocation->addChild($anonymousClass);

        $statement = new ASTStatement();
        $statement->configureLinesAndColumns(10, 14, 9, 11);
        $statement->addChild($allocation);

        $scope = new ASTScope();
        $scope->configureLinesAndColumns(8, 15, 5, 5);
        $scope->addChild($comment);
        $scope->addChild($statement);

        return $anonymousClass;
    }
}
