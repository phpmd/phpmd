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

/**
 * Test case for the {@link \PHPMD\Node\Annotations} class.
 *
 * @covers \PHPMD\Node\Annotations
 */
class AnnotationsTest extends AbstractTestCase
{
    /**
     * testCollectionReturnsFalseWhenNoAnnotationExists
     */
    public function testCollectionReturnsFalseWhenNoAnnotationExists(): void
    {
        $annotations = new Annotations($this->getClassMock());
        $this->assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsFalseWhenNoMatchingAnnotationExists
     */
    public function testCollectionReturnsFalseWhenNoMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('getComment'))
            ->will(
                $this->returnValue(
                    '/**
                      * @SuppressWarnings("Foo")
                      * @SuppressWarnings("Bar")
                      * @SuppressWarnings("Baz")
                      */'
                )
            );

        $annotations = new Annotations($class);
        $this->assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsTrueWhenMatchingAnnotationExists
     */
    public function testCollectionReturnsTrueWhenMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('getComment'))
            ->will($this->returnValue('/** @SuppressWarnings("PMD") */'));

        $annotations = new Annotations($class);
        $this->assertTrue($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsTrueWhenOneMatchingAnnotationExists
     */
    public function testCollectionReturnsTrueWhenOneMatchingAnnotationExists(): void
    {
        $class = $this->getClassMock();
        $class->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('getComment'))
            ->will(
                $this->returnValue(
                    '/**
                      * @SuppressWarnings("FooBar")
                      * @SuppressWarnings("PMD")
                      */'
                )
            );

        $annotations = new Annotations($class);
        $this->assertTrue($annotations->suppresses($this->getRuleMock()));
    }
}
