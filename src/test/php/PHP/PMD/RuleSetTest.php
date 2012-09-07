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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://phpmd.org
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once dirname(__FILE__) . '/../../../resources/files/rules/TestRule.php';

require_once 'PHP/PMD/Node/Class.php';
require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/RuleSet.php';

/**
 * Test case for the {@link PHP_PMD_RuleSet} class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 *
 * @covers PHP_PMD_RuleSet
 * @group phpmd
 * @group unittest
 */
class PHP_PMD_RuleSetTest extends PHP_PMD_AbstractTest
{
    /**
     * testGetRuleByNameReturnsNullWhenNoMatchingRuleExists
     *
     * @return void
     */
    public function testGetRuleByNameReturnsNullWhenNoMatchingRuleExists()
    {
        $ruleSet = $this->createRuleSetFixture();
        $this->assertNull($ruleSet->getRuleByName(__FUNCTION__));
    }

    /**
     * testGetRuleByNameReturnsMatchingRuleInstance
     *
     * @return void
     */
    public function testGetRuleByNameReturnsMatchingRuleInstance()
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__, __CLASS__, __METHOD__);
        $rule    = $ruleSet->getRuleByName(__CLASS__);

        $this->assertEquals(__CLASS__, $rule->getName());
    }

    /**
     * testApplyNotInvokesRuleWhenSuppressAnnotationExists
     * 
     * @return void
     */
    public function testApplyNotInvokesRuleWhenSuppressAnnotationExists()
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportMock(0));
        $ruleSet->apply($this->getClass());

        $this->assertNull($ruleSet->getRuleByName(__FUNCTION__)->node);
    }

    /**
     * testApplyInvokesRuleWhenStrictModeIsSet
     * 
     * @return void
     */
    public function testApplyInvokesRuleWhenStrictModeIsSet()
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportMock(0));
        $ruleSet->setStrict();

        $class = $this->getClass();
        $ruleSet->apply($class);

        $this->assertSame($class, $ruleSet->getRuleByName(__FUNCTION__)->node);
    }

    /**
     * Creates a rule set instance with a variable amount of appended rule
     * objects.
     *
     * @param string $name Variable number of rule identifiers.
     *
     * @return PHP_PMD_AbstractRule
     */
    private function createRuleSetFixture($name = null)
    {
        $ruleSet = new PHP_PMD_RuleSet();
        for ($i = 0; $i < func_num_args(); ++$i) {
            $rule = new rules_TestRule();
            $rule->setName(func_get_arg($i));

            $ruleSet->addRule($rule);
        }
        return $ruleSet;
    }
}
