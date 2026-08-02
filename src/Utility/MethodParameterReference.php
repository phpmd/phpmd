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

namespace PHPMD\Utility;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTMethodPostfix;

/**
 * Resolves whether a called method's formal parameter at a given position is passed by reference.
 */
final class MethodParameterReference
{
    /**
     * Check if a method parameter at the given position is passed by reference.
     *
     * Resolves the called method from the AST (e.g. $this->foo()) and checks
     * its formal parameter definitions.
     */
    public static function isPassedByReference(
        ASTMemberPrimaryPrefix $memberPrefix,
        int|string $argumentPosition
    ): bool {
        $methodPostfix = self::findMethodPostfix($memberPrefix);
        if ($methodPostfix === null) {
            return false;
        }

        $classNode = self::findContainingClass($memberPrefix);
        if ($classNode === null) {
            return false;
        }

        $method = self::findMethodByName($classNode, $methodPostfix->getImage());
        if ($method === null) {
            return false;
        }

        return self::isParameterAtPositionByReference($method, $argumentPosition);
    }

    /**
     * Finds the method postfix node (contains the method name) among the children.
     */
    private static function findMethodPostfix(ASTMemberPrimaryPrefix $memberPrefix): ?ASTMethodPostfix
    {
        foreach ($memberPrefix->getChildren() as $child) {
            if ($child instanceof ASTMethodPostfix) {
                return $child;
            }
        }

        return null;
    }

    /**
     * Walks up the AST to find the containing class.
     */
    private static function findContainingClass(ASTMemberPrimaryPrefix $memberPrefix): ?ASTClass
    {
        $node = $memberPrefix;
        while ($node = $node->getParent()) {
            if ($node instanceof ASTClass) {
                return $node;
            }
        }

        return null;
    }

    private static function findMethodByName(ASTClass $classNode, string $methodName): ?ASTMethod
    {
        foreach ($classNode->getMethods() as $method) {
            if (strcasecmp($method->getImage(), $methodName) === 0) {
                return $method;
            }
        }

        return null;
    }

    private static function isParameterAtPositionByReference(ASTMethod $method, int|string $argumentPosition): bool
    {
        $formalParameters = $method->getFirstChildOfType(ASTFormalParameters::class);
        if ($formalParameters === null) {
            return false;
        }

        $params = $formalParameters->getChildren();
        $paramCount = count($params);

        // Handle variadic: clamp position to last parameter
        if ($paramCount > 0) {
            $lastParam = $params[$paramCount - 1];
            if ($lastParam instanceof ASTFormalParameter && $lastParam->isVariableArgList()) {
                $argumentPosition = min($argumentPosition, $paramCount - 1);
            }
        }

        if (!isset($params[$argumentPosition])) {
            return false;
        }

        $param = $params[$argumentPosition];

        return $param instanceof ASTFormalParameter && $param->isPassedByReference();
    }
}
