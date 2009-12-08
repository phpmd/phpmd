<?php
require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IClassAware.php';

/**
 * Simple text rule implementation.
 */
class rules_TestRule
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IClassAware
{
    public $node = null;

    public function apply(PHP_PMD_AbstractNode $node)
    {
        $this->node = $node;
    }
}
