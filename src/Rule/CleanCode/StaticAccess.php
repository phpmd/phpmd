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

use OutOfBoundsException;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTSelfReference;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Utility\ExceptionsList;

/**
 * Check if static access is used in a method.
 *
 * Static access is known to cause hard dependencies between classes
 * and is a bad practice.
 */
final class StaticAccess extends AbstractRule implements FunctionAware, MethodAware
{
    /** Temporary cache of configured exceptions. */
    private ExceptionsList $exceptions;

    /**
     * Method checks for use of static access and warns about it.
     */
    public function apply(AbstractNode $node): void
    {
        $ignoreRegexp = trim($this->getStringProperty('ignorepattern', ''));
        $exceptions = $this->getExceptionsList();
        $nodes = $node->findChildrenOfType(ASTMemberPrimaryPrefix::class);

        foreach ($nodes as $methodCall) {
            if ($this->isMethodIgnored($methodCall, $ignoreRegexp)) {
                continue;
            }

            if (!$this->isStaticMethodCall($methodCall)) {
                continue;
            }

            $className = $methodCall->getChild(0)->getNode()->getImage();
            if ($exceptions->contains($className)) {
                continue;
            }

            $this->addViolation($methodCall, [$className, $node->getName()]);
        }
    }

    /**
     * @param AbstractNode<ASTMemberPrimaryPrefix> $methodCall
     * @throws OutOfBoundsException
     */
    private function isStaticMethodCall(AbstractNode $methodCall): bool
    {
        return $methodCall->getChild(0)->getNode() instanceof ASTClassOrInterfaceReference &&
            $methodCall->getChild(1)->getNode() instanceof ASTMethodPostfix &&
            !$this->isCallingParent($methodCall) &&
            !$this->isCallingSelf($methodCall) &&
            !$this->isCallingEnumTranslator($methodCall);
    }

    /**
     * @param AbstractNode<ASTMemberPrimaryPrefix> $methodCall
     * @throws OutOfBoundsException
     */
    private function isCallingParent(AbstractNode $methodCall): bool
    {
        return $methodCall->getChild(0)->getNode() instanceof ASTParentReference;
    }

    /**
     * @param AbstractNode<ASTMemberPrimaryPrefix> $methodCall
     * @throws OutOfBoundsException
     */
    private function isCallingSelf(AbstractNode $methodCall): bool
    {
        return $methodCall->getChild(0)->getNode() instanceof ASTSelfReference;
    }

    /**
     * @param AbstractNode<ASTMemberPrimaryPrefix> $methodCall
     * @throws OutOfBoundsException
     */
    private function isCallingEnumTranslator(AbstractNode $methodCall): bool
    {
        $enumName = $methodCall->getChild(0)->getName();

        if (!enum_exists($enumName)) {
            return false;
        }

        $methodName = $methodCall->getChild(1)->getName();

        return in_array($methodName, ['cases', 'from', 'tryFrom'], true);
    }

    /**
     * @param AbstractNode<ASTMemberPrimaryPrefix> $methodCall
     */
    private function isMethodIgnored(AbstractNode $methodCall, string $ignorePattern): bool
    {
        if ($ignorePattern === '') {
            return false;
        }

        $methodName = $methodCall->getFirstChildOfType(ASTMethodPostfix::class);

        return $methodName !== null && preg_match($ignorePattern, $methodName->getName()) === 1;
    }

    /**
     * Gets exceptions from property
     */
    private function getExceptionsList(): ExceptionsList
    {
        $this->exceptions ??= new ExceptionsList($this, '\\');

        return $this->exceptions;
    }
}
