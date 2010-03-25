<?php
class testRuleDoesNotApplyToHttpRawPostDataSuperGlobal
{
    function testRuleDoesNotApplyToHttpRawPostDataSuperGlobal()
    {
        return $HTTP_RAW_POST_DATA;
    }
}