<?php

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractTest;

class StaticAccessTest extends AbstractTest
{
    public function testRuleNotAppliesToParentStaticCall()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToSelfStaticCall()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToDynamicMethodCall()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToStaticMethodAccessWhenExcluded()
   {
       $rule = new StaticAccess();
       $rule->setReport($this->getReportMock(0));
       $rule->addProperty('exceptions', 'Excluded1,Excluded2');
       $rule->apply($this->getMethod());
   }

    public function testRuleAppliesToStaticMethodAccess()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }


    public function testRuleAppliesToStaticMethodAccessWhenNotAllExcluded()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('exceptions', 'Excluded');
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToConstantAccess()
    {
        $rule = new StaticAccess();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
