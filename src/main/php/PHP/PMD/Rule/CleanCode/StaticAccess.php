<?php

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * Check if static access is used in a method.
 *
 * Static access is known to cause hard dependencies between classes
 * and is a bad practice.
 */
class PHP_PMD_Rule_CleanCode_StaticAccess
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $staticReferences = $node->findChildrenOfType('ClassOrInterfaceReference');

        foreach ($staticReferences as $reference) {
            if ($this->isReferenceInParameter($reference)) {
                continue;
            }
            // references to self, parent and static are not considered static calls
            switch ($reference->getImage()) {
                case 'self':
                case 'parent':
                case 'static':
                    continue 2;
            }
            // uses of "new" should be allowed
            if ($reference->getParent()->getImage() == 'new') {
                continue;
            }

            $this->addViolation($reference, array($reference->getImage(), $node->getImage()));
        }
    }

    private function isReferenceInParameter($reference)
    {
        return $reference->getParent()->getNode() instanceof PHP_Depend_Code_ASTFormalParameter;
    }
}
