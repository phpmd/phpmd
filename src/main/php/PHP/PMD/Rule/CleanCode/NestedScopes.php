<?php

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * Check if there are nested if or loop scopes in a method.
 *
 * The strict rule of this type would disallow nested scopes
 * of any kind, however this is not always possible for all
 * idioms in a performant way. This is why loop => if or if => loop
 * are allowed, however never two loops or two if statements.
 */
class PHP_PMD_Rule_CleanCode_NestedScopes
    extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
            PHP_PMD_Rule_IFunctionAware
{
    public function apply(PHP_PMD_AbstractNode $node)
    {
        return; // not good enough, if, elseif,... nest each other and get detected by this rule :(

        foreach ($node->findChildrenOfType('ScopeStatement') as $scope) {
            if ( ! $this->isNestedScope($scope)) {
                continue;
            }

            $this->addViolation($scope, array($node->getImage()));
        }
    }

    private function isNestedScope($scope)
    {
        $scopeParent = $scope->getParent();
        $searchNode = $scopeParent;

        while ($searchNode = $searchNode->getParent()) {
            if ($this->isSameScopeType($scopeParent, $searchNode)) {
                return true;
            }
        }

        return false;
    }

    private function isSameScopeType($scopeParent, $searchNode)
    {
        return $scopeParent->getName() === $searchNode->getName();
    }
}
