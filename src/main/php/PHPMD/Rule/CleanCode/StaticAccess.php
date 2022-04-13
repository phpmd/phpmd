<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Rule\CleanCode;

use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTSelfReference;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Check if static access is used in a method.
 *
 * Static access is known to cause hard dependencies between classes
 * and is a bad practice.
 */
class StaticAccess extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * Method checks for use of static access and warns about it.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $exceptions = $this->getExceptionsList();
        $nodes = $node->findChildrenOfType('MemberPrimaryPrefix');

        foreach ($nodes as $methodCall) {
            if (!$this->isStaticMethodCall($methodCall)) {
                continue;
            }

            $className = $methodCall->getChild(0)->getNode()->getImage();
            if ($this->isExcludedFromAnalysis($className, $exceptions)) {
                continue;
            }

            $this->addViolation($methodCall, array($className, $node->getName()));
        }
    }

    protected function isExcludedFromAnalysis($className, $exceptions)
    {
        return in_array(trim($className, " \t\n\r\0\x0B\\"), $exceptions);
    }

    protected function isStaticMethodCall(AbstractNode $methodCall)
    {
        return $methodCall->getChild(0)->getNode() instanceof ASTClassOrInterfaceReference &&
            $methodCall->getChild(1)->getNode() instanceof ASTMethodPostfix &&
            !$this->isCallingParent($methodCall) &&
            !$this->isCallingSelf($methodCall);
    }

    protected function isCallingParent(AbstractNode $methodCall)
    {
        return $methodCall->getChild(0)->getNode() instanceof ASTParentReference;
    }

    protected function isCallingSelf(AbstractNode $methodCall)
    {
        return $methodCall->getChild(0)->getNode() instanceof ASTSelfReference;
    }

    /**
     * Gets array of exceptions from property
     *
     * @return array
     */
    protected function getExceptionsList()
    {
        try {
            $exceptions = $this->getStringProperty('exceptions');
        } catch (\OutOfBoundsException $e) {
            $exceptions = '';
        }

        return array_map(
            function ($className) {
                return trim($className, " \t\n\r\0\x0B\\");
            },
            explode(',', $exceptions)
        );
    }
}
