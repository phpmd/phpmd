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

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\Node\Annotations} class.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\Node\Annotations
 * @group phpmd
 * @group phpmd::node
 * @group unittest
 */
class AnnotationsTest extends AbstractTest
{
    /**
     * testCollectionReturnsFalseWhenNoAnnotationExists
     *
     * @return void
     */
    public function testCollectionReturnsFalseWhenNoAnnotationExists()
    {
        $annotations = new Annotations($this->getClassMock());
        $this->assertFalse($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsFalseWhenNoMatchingAnnotationExists
     *
     * @return void
     */
    public function testCollectionReturnsFalseWhenNoMatchingAnnotationExists()
    {
        $class = $this->getClassMock();
        $class->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('getDocComment'))
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
     *
     * @return void
     */
    public function testCollectionReturnsTrueWhenMatchingAnnotationExists()
    {
        $class = $this->getClassMock();
        $class->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('getDocComment'))
            ->will($this->returnValue('/** @SuppressWarnings("PMD") */'));

        $annotations = new Annotations($class);
        $this->assertTrue($annotations->suppresses($this->getRuleMock()));
    }

    /**
     * testCollectionReturnsTrueWhenOneMatchingAnnotationExists
     *
     * @return void
     */
    public function testCollectionReturnsTrueWhenOneMatchingAnnotationExists()
    {
        $class = $this->getClassMock();
        $class->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('getDocComment'))
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
