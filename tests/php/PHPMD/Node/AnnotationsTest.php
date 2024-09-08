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

use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Test case for the {@link \PHPMD\Node\Annotations} class.
 *
 * @covers \PHPMD\Node\Annotations
 */
class AnnotationsTest extends AbstractTestCase
{
    /**
     * testCollectionReturnsFalseWhenNoAnnotationExists
     * @throws Throwable
     */
    public function testCollectionReturnsFalseWhenNoAnnotationExists(): void
    {
        $annotations = new Annotations($this->getClassMock());
        static::assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsFalseWhenNoMatchingAnnotationExists
     * @throws Throwable
     */
    public function testCollectionReturnsFalseWhenNoMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects(static::once())
            ->method('__call')
            ->with(static::equalTo('getComment'))
            ->will(
                static::returnValue(
                    '/**
                      * @SuppressWarnings("Foo")
                      * @SuppressWarnings("Bar")
                      * @SuppressWarnings("Baz")
                      */'
                )
            );

        $annotations = new Annotations($class);
        static::assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsTrueWhenMatchingAnnotationExists
     * @throws Throwable
     */
    public function testCollectionReturnsTrueWhenMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects(static::once())
            ->method('__call')
            ->with(static::equalTo('getComment'))
            ->will(static::returnValue('/** @SuppressWarnings("PMD") */'));

        $annotations = new Annotations($class);
        static::assertTrue($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsTrueWhenOneMatchingAnnotationExists
     * @throws Throwable
     */
    public function testCollectionReturnsTrueWhenOneMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects(static::once())
            ->method('__call')
            ->with(static::equalTo('getComment'))
            ->will(
                static::returnValue(
                    '/**
                      * @SuppressWarnings("FooBar")
                      * @SuppressWarnings("PMD")
                      */'
                )
            );

        $annotations = new Annotations($class);
        static::assertTrue($annotations->suppresses($this->getRuleMock()));
    }
}
