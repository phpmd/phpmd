<?php

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * Check if there is an else expression somewhere in the method/function and
 * warn about it.
 *
 * Object Calisthenics teaches us, that an else expression can always be
 * avoided by simple guard clause or return statements.
 */
class PHP_PMD_Rule_CleanCode_ElseExpression
    extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
            PHP_PMD_Rule_IFunctionAware
{
    public function apply(PHP_PMD_AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('ScopeStatement') as $scope) {
            $parent = $scope->getParent();

            if ( ! $this->isIfOrElseIfStatement($parent)) {
                continue;
            }

            if ( ! $this->isElseScope($scope, $parent)) {
                continue;
            }

            $this->addViolation($scope, array($node->getImage()));
        }
    }

    private function isElseScope($scope, $parent)
    {
        return (
            count($parent->getChildren()) === 3 &&
            $scope->getNode() === $parent->getChild(2)->getNode()
        );
    }

    private function isIfOrElseIfStatement($parent)
    {
        return ($parent->getName() === "if" || $parent->getName() === "elseif");
    }
}
