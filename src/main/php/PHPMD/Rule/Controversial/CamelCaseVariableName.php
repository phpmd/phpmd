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
 * This rule class detects variables not named in camelCase.
 *
 * @author     Francis Besset <francis.besset@gmail.com>
 * @since      1.1.0
 */
class CamelCaseVariableName extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * @var array
     */
    private $exceptions = array(
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
    );

    /**
     * This method checks if a variable is not named in camelCase
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('Variable') as $variable) {
            $image = $variable->getImage();

            if (in_array($image, $this->exceptions)) {
                continue;
            }

            if (preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $image)) {
                continue;
            }

            if ($variable->getParent()->isInstanceOf('PropertyPostfix')) {
                continue;
            }

            $this->addViolation($node, array($image));
        }
    }
}
