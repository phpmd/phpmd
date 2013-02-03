<?php

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/PMD/Rule/CleanCode/ElseExpression.php';

class PHP_PMD_Rule_CleanCode_ElseExpressionTest extends PHP_PMD_AbstractTest
{
    public function testRuleNotAppliesToMethodWithoutElseExpression()
    {
        $rule = new PHP_PMD_Rule_CleanCode_ElseExpression();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithElseExpression()
    {
        $rule = new PHP_PMD_Rule_CleanCode_ElseExpression();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesMultipleTimesToMethodWithMultipleElseExpressions()
    {
        $rule = new PHP_PMD_Rule_CleanCode_ElseExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }
}
