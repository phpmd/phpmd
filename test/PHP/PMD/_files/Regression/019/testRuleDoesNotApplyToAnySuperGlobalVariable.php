<?php
class testRuleDoesNotApplyToAnySuperGlobalVariable
{
    function testRuleDoesNotApplyToAnySuperGlobalVariable()
    {
        $GLOBALS = 42;
        $HTTP_RAW_POST_DATA = 42;
        $_COOKIE = 42;
        $_ENV = 42;
        $_FILES = 42;
        $_GET = 42;
        $_POST = 42;
        $_REQUEST = 42;
        $_SERVER = 42;
        $_SESSION = 42;
        $argc = 42;
        $argv = 42;
    }
}