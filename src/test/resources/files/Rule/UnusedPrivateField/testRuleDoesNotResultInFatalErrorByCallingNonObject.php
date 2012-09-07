<?php
class testRuleDoesNotResultInFatalErrorByCallingNonObject
{
    private $foo = null;

    public function bar()
    {
        return self::${'_bar'};
    }
}
