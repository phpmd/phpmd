<?php
class testRuleNotAppliesToClassConstantWithUpperCaseCharacters
{
    const T_FOO =42,
          T_BAR = 23,
          T_BAZ = 17;
}