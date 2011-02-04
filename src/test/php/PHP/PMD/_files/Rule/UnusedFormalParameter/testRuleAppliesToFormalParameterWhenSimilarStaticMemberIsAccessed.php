<?php
class testRuleAppliesToFormalParameterWhenSimilarStaticMemberIsAccessed
{
    public static $foo = array();

    public function testRuleAppliesToFormalParameterWhenSimilarStaticMemberIsAccessed($foo)
    {
        self::$foo[] = 42;
    }
}