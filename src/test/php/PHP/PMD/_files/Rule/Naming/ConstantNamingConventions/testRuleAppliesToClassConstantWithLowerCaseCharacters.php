<?php
class testRuleAppliesToClassConstantWithLowerCaseCharacters
{
    const T_FOO = 42;
    const T_Bar = 23,
          t_baz = 17;
}