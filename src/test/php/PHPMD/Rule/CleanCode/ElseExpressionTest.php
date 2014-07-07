<?php

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractTest;

class ElseExpressionTest extends AbstractTest
{
    public function testRuleNotAppliesToMethodWithoutElseExpression()
    {
        $rule = new ElseExpression();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToMethodWithElseExpression()
    {
        $rule = new ElseExpression();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesMultipleTimesToMethodWithMultipleElseExpressions()
    {
        $rule = new ElseExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }
}
