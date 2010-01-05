<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/pmd
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/PMDTest.php';
require_once dirname(__FILE__) . '/ParserFactoryTest.php';
require_once dirname(__FILE__) . '/ParserTest.php';
require_once dirname(__FILE__) . '/ReportTest.php';
require_once dirname(__FILE__) . '/RuleSetFactoryTest.php';
require_once dirname(__FILE__) . '/RuleSetTest.php';
require_once dirname(__FILE__) . '/Node/AllTests.php';
require_once dirname(__FILE__) . '/Regression/AllTests.php';
require_once dirname(__FILE__) . '/Renderer/AllTests.php';
require_once dirname(__FILE__) . '/Rule/AllTests.php';

/**
 * Main test suite for the complete PHP_PMD application.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
 */
class PHP_PMD_AllTests
{
    /**
     * Creates a phpunit test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP_PMD - Tests');

        $suite->addTestSuite('PHP_PMD_PMDTest');
        $suite->addTestSuite('PHP_PMD_ParserFactoryTest');
        $suite->addTestSuite('PHP_PMD_ParserTest');
        $suite->addTestSuite('PHP_PMD_ReportTest');
        $suite->addTestSuite('PHP_PMD_RuleSetFactoryTest');
        $suite->addTestSuite('PHP_PMD_RuleSetTest');

        $suite->addTest(PHP_PMD_Node_AllTests::suite());
        $suite->addTest(PHP_PMD_Regression_AllTests::suite());
        $suite->addTest(PHP_PMD_Renderer_AllTests::suite());
        $suite->addTest(PHP_PMD_Rule_AllTests::suite());

        return $suite;
    }
}