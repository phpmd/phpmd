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

namespace PHPMD\Rule\Controversial;

use OutOfBoundsException;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTVariable;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule class detects variables not named in camelCase.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @since 1.1.0
 */
final class CamelCaseVariableName extends AbstractRule implements FunctionAware, MethodAware
{
    /** @var list<string> */
    private array $exceptions = [
        '$php_errormsg',
        '$http_response_header',
        '$GLOBALS',
        '$_SERVER',
        '$_GET',
        '$_POST',
        '$_FILES',
        '$_COOKIE',
        '$_SESSION',
        '$_REQUEST',
        '$_ENV',
    ];

    /**
     * This method checks if a variable is not named in camelCase
     * and emits a rule violation.
     */
    public function apply(AbstractNode $node): void
    {
        $variables = [];

        foreach ($node->findChildrenOfTypeVariable() as $variable) {
            if (!$this->isValid($variable)) {
                $variableName = $variable->getImage();

                if (!isset($variables[$variableName])) {
                    $variables[$variableName] = true;
                    $this->addViolation($variable, [$variableName]);
                }
            }
        }
    }

    /**
     * @param AbstractNode<ASTVariable> $variable
     * @throws OutOfBoundsException
     */
    private function isValid(AbstractNode $variable): bool
    {
        $image = $variable->getImage();

        if (in_array($image, $this->exceptions, true)) {
            return true;
        }

        // disallow any consecutive uppercase letters
        if (
            $this->getBooleanProperty('camelcase-abbreviations', false)
            && preg_match('/[A-Z]{2}/', $image) === 1
        ) {
            return false;
        }

        if ($this->getBooleanProperty('allow-underscore')) {
            if (preg_match('/^\$[_]?[a-z][a-zA-Z0-9]*$/', $image)) {
                return true;
            }
        }

        if (preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $image)) {
            return true;
        }

        if ($variable->getParent()?->isInstanceOf(ASTPropertyPostfix::class)) {
            return true;
        }

        return false;
    }
}
