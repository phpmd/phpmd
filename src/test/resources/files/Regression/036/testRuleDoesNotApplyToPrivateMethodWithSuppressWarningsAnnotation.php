<?php
class testRuleDoesNotApplyToPrivateMethodWithSuppressWarningsAnnotation
{
    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function unusedMethod()
    {
        
    }
}