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
    public function apply(AbstractNode $node)
    {
        foreach ($node->getParameters() as $parameter) {
            if (!$this->isValid($parameter->getName())) {
                $this->addViolation(
                    $node,
                    array(
                        $parameter->getName(),
                    )
                );
            }
        }
    }

    protected function isValid($parameterName)
    {
        if ($this->getBooleanProperty('allow-underscore')) {
            return preg_match('/^\$[_]?[a-z][a-zA-Z0-9]*$/', $parameterName);
        }

        return preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $parameterName);
    }
}
