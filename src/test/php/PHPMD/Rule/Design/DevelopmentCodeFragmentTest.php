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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTest;

/**
 * Test case for the {@link \PHPMD\Rule\Design\DevelopmentCodeFragment} class.
 *
 * @link https://github.com/phpmd/phpmd/issues/265
 * @since 2.3.0
 *
 * @covers \PHPMD\Rule\Design\DevelopmentCodeFragment
 */
class DevelopmentCodeFragmentTest extends AbstractTest
{

    private function _getRule() {
        $rule = new DevelopmentCodeFragment();
        $rule->addProperty('unwanted-functions', 'var_dump,print_r,debug_zval_dump,debug_print_backtrace');
        $rule->addProperty('ignore-namespaces', 'false');
        return $rule;
    }

    /**
     * testRuleNotAppliesToMethodWithoutSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutSuspectFunctionCall()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithSuspectFunctionCall()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithMultipleSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithMultipleSuspectFunctionCall()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithoutSuspectFunctionCall()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithSuspectFunctionCall()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithMultipleSuspectFunctionCall
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithMultipleSuspectFunctionCall()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToMethodWithinNamespace()
    {
        $rule = $this->_getRule();
        $rule->addProperty('ignore-namespaces', 'true');
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getClass());
    }

    public function testRuleNotAppliesToMethodWithinNamespaceByDefault()
    {
        $rule = $this->_getRule();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getClass());
    }
}
