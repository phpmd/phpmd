<?php
class testRuleDoesNotApplyToClassNameAccessedPrivateField
{
    private static $_foo = 42;

    public function __construct()
    {
        testRuleDoesNotApplyToClassNameAccessedPrivateField::$_foo = 23;
    }
}