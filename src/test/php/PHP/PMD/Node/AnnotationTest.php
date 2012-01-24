<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Node
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/PMD/Node/Annotation.php';

/**
 * Test case for the {@link PHP_PMD_Node_Annotation} class.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Node
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 *
 * @covers PHP_PMD_Node_Annotation
 * @group phpmd
 * @group phpmd::node
 * @group unittest
 */
class PHP_PMD_Node_AnnotationTest extends PHP_PMD_AbstractTest
{
    /**
     * testAnnotationReturnsFalseWhenNoSuppressWarningAnnotationExists
     *
     * @return void
     */
    public function testAnnotationReturnsFalseWhenNoSuppressWarningAnnotationExists()
    {
        $annotation = new PHP_PMD_Node_Annotation('NoSuppressWarning', 'PMD');
        $this->assertFalse($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsFalseWhenSuppressWarningContainsInvalidValue
     *
     * @return void
     */
    public function testAnnotationReturnsFalseWhenSuppressWarningContainsInvalidValue()
    {
        $annotation = new PHP_PMD_Node_Annotation('SuppressWarnings', 'PHP');
        $this->assertFalse($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPMD
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPMD()
    {
        $annotation = new PHP_PMD_Node_Annotation('SuppressWarnings', 'PMD');
        $this->assertTrue($annotation->suppresses($this->getRuleMock()));
    }

    /**
     * testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMD
     *
     * @return void
     */
    public function testAnnotationReturnsTrueWhenSuppressWarningContainsWithPHPMD()
    {
        $annotation = new PHP_PMD_Node_Annotation('SuppressWarnings', 'PHPMD');
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

        $annotation = new PHP_PMD_Node_Annotation('SuppressWarnings', 'PMD.UnusedCodeRule');
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

        $annotation = new PHP_PMD_Node_Annotation('SuppressWarnings', 'PHPMD.UnusedCodeRule');
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

        $annotation = new PHP_PMD_Node_Annotation('SuppressWarnings', 'unused');
        $this->assertTrue($annotation->suppresses($rule));
    }
}
