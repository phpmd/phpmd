<?php

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractTest;

class IfStatementAssignmentTest extends AbstractTest
{
    public function testRuleNotAppliesToIfsWithoutAssignment()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToIfsWithConditionsOnly()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToLogicalOperators()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleWorksCorrectlyWhenExpressionContainsMath()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToFunctions()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleIfConditions()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToMultilevelIfConditions()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(6));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleTimesInOneIfCondition()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
