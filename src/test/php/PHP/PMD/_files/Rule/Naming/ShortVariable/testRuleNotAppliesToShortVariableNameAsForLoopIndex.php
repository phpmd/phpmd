<?php
function testRuleNotAppliesToShortVariableNameAsForLoopIndex()
{
    for ($i = 0; $i < 42; ++$i) {
        
    }
}