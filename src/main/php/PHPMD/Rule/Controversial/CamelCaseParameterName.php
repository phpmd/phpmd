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

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\MethodNode;

/**
 * This rule class detects parameters not named in camelCase.
 *
 * @author Francis Besset <francis.besset@gmail.com>
 * @since 1.1.0
 */
class CamelCaseParameterName extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a parameter is not named in camelCase
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof FunctionNode && !$node instanceof MethodNode) {
            return;
        }

        foreach ($node->getParameters() as $parameter) {
            if (!$this->isValid($parameter->getName())) {
                $this->addViolation(
                    $node,
                    [
                        $parameter->getName(),
                    ]
                );
            }
        }
    }

    protected function isValid($parameterName)
    {
        // disallow any consecutive uppercase letters
        if ($this->getBooleanProperty('camelcase-abbreviations', false)
            && preg_match('/[A-Z]{2}/', $parameterName) === 1) {
            return false;
        }

        if ($this->getBooleanProperty('allow-underscore')) {
            return preg_match('/^\$[_]?[a-z][a-zA-Z0-9]*$/', $parameterName);
        }

        return preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $parameterName);
    }
}
