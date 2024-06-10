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
 * Test case for the {@link \PHPMD\Node\Annotation} class.
 *
 * @covers \PHPMD\Node\Annotation
 */
class AnnotationTest extends AbstractTestCase
{
    /**
     * testAnnotationReturnsFalseWhenNoSuppressWarningAnnotationExists
     * @throws Throwable
     */
    public function testAnnotationReturnsFalseWhenNoSuppressWarningAnnotationExists(): void
    {
        $annotation = new Annotation('NoSuppressWarning', 'PMD');
        static::assertFalse($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsFalseWhenSuppressWarningContainsInvalidValue
     * @throws Throwable
     */
    public function testAnnotationReturnsFalseWhenSuppressWarningContainsInvalidValue(): void
    {
        $annotation = new Annotation('SuppressWarnings', 'PHP');
        static::assertFalse($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPMD
     * @throws Throwable
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPMD(): void
    {
        $annotation = new Annotation('SuppressWarnings', 'PMD');
        static::assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMD
     * @throws Throwable
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMD(): void
    {
        $annotation = new Annotation('SuppressWarnings', 'PHPMD');
        static::assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMDLCFirst
     * @throws Throwable
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMDLCFirst(): void
    {
        $annotation = new Annotation('suppressWarnings', 'PHPMD');
        static::assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsPMDPlusRuleName
     * @throws Throwable
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsPMDPlusRuleName(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('UnusedCodeRule');

        $annotation = new Annotation('SuppressWarnings', 'PMD.UnusedCodeRule');
        static::assertTrue($annotation->suppresses($rule));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsPHPMDPlusRuleName
     * @throws Throwable
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsPHPMDPlusRuleName(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('UnusedCodeRule');

        $annotation = new Annotation('SuppressWarnings', 'PHPMD.UnusedCodeRule');
        static::assertTrue($annotation->suppresses($rule));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsPartialRuleName
     * @throws Throwable
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsPartialRuleName(): void
    {
        $rule = $this->getRuleMock();
        $rule->setName('UnusedCodeRule');

        $annotation = new Annotation('SuppressWarnings', 'unused');
        static::assertTrue($annotation->suppresses($rule));
    }
}
