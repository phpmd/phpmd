<?php

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * Check for a boolean flag in the method/function signature.
 *
 * Boolean flags are signs for single responsibility principle violations.
 */
class PHP_PMD_Rule_CleanCode_BooleanArgumentFlag
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    public function apply(PHP_PMD_AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('FormalParameter') as $param) {
            $declarator = $param->getFirstChildOfType('VariableDeclarator');
            $value = $declarator->getValue();

            if ( ! $this->isBooleanValue($value)) {
                continue;
            }

            $this->addViolation($param, array($node->getImage(), $declarator->getImage()));
        }
    }

    private function isBooleanValue(PHP_Depend_Code_Value $value = null)
    {
        return $value && $value->isValueAvailable() && ($value->getValue() === true || $value->getValue() === false);
    }
}
