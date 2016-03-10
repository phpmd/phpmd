<?php
class testRuleDoesNotApplyForStaticVariableAccess
{
    public function validVariableName()
    {
        if (!in_array('foo', self::$invalid_variable_name)) {

        }
    }
}
