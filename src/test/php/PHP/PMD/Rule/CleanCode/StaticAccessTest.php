<?php

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/PMD/Rule/CleanCode/StaticAccess.php';

class PHP_PMD_Rule_CleanCode_StaticAccessTest extends PHP_PMD_AbstractTest
{
    public function testRuleNotAppliesToParentStaticCall()
    {
        $rule = new PHP_PMD_Rule_CleanCode_StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToSelfStaticCall()
    {
        $rule = new PHP_PMD_Rule_CleanCode_StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToDynamicMethodCall()
    {
        $rule = new PHP_PMD_Rule_CleanCode_StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToStaticMethodAccess()
    {
        $rule = new PHP_PMD_Rule_CleanCode_StaticAccess();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToConstantAccess()
    {
        $rule = new PHP_PMD_Rule_CleanCode_StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
