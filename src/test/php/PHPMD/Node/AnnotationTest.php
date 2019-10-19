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
 * Test case for the {@link \PHPMD\Node\Annotation} class.
 *
 * @covers \PHPMD\Node\Annotation
 */
class AnnotationTest extends AbstractTest
{
    /**
     * testAnnotationReturnsFalseWhenNoSuppressWarningAnnotationExists
     *
     * @return void
     */
    public function testAnnotationReturnsFalseWhenNoSuppressWarningAnnotationExists()
    {
        $annotation = new Annotation('NoSuppressWarning', 'PMD');
        $this->assertFalse($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsFalseWhenSuppressWarningContainsInvalidValue
     *
     * @return void
     */
    public function testAnnotationReturnsFalseWhenSuppressWarningContainsInvalidValue()
    {
        $annotation = new Annotation('SuppressWarnings', 'PHP');
        $this->assertFalse($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPMD
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPMD()
    {
        $annotation = new Annotation('SuppressWarnings', 'PMD');
        $this->assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMD
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMD()
    {
        $annotation = new Annotation('SuppressWarnings', 'PHPMD');
        $this->assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMDLCFirst
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMDLCFirst()
    {
        $annotation = new Annotation('suppressWarnings', 'PHPMD');
        $this->assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsPMDPlusRuleName
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsPMDPlusRuleName()
    {
        $rule = $this->getRuleMock();
        $rule->setName('UnusedCodeRule');

        $annotation = new Annotation('SuppressWarnings', 'PMD.UnusedCodeRule');
        $this->assertTrue($annotation->suppresses($rule));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsPHPMDPlusRuleName
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsPHPMDPlusRuleName()
    {
        $rule = $this->getRuleMock();
        $rule->setName('UnusedCodeRule');

        $annotation = new Annotation('SuppressWarnings', 'PHPMD.UnusedCodeRule');
        $this->assertTrue($annotation->suppresses($rule));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsPartialRuleName
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsPartialRuleName()
    {
        $rule = $this->getRuleMock();
        $rule->setName('UnusedCodeRule');

        $annotation = new Annotation('SuppressWarnings', 'unused');
        $this->assertTrue($annotation->suppresses($rule));
    }
}
