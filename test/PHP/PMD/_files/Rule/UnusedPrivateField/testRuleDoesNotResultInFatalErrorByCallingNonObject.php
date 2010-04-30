<?php
class testRuleDoesNotResultInFatalErrorByCallingNonObject
{
    private $_foo = null;

    public function bar()
    {
        return self::${'_bar'};
    }
}