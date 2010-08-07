<?php
class testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex
{
    private static $staticAttributes = array();

    public function testRuleDoesNotApplyToMethodArgumentUsedAsArrayIndex( array $declaredClasses )
    {
        self::$staticAttributes[$declaredClasses[42]] = true;
    }
}
