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
 * This rule class detects the usage of superglobals.
 *
 * @author     Francis Besset <francis.besset@gmail.com>
 * @since      1.1.0
 */
class Superglobals extends AbstractRule implements MethodAware, FunctionAware
{
    protected $superglobals = array(
        '$GLOBALS',
        '$_SERVER',  '$HTTP_SERVER_VARS',
        '$_GET',     '$HTTP_GET_VARS',
        '$_POST',    '$HTTP_POST_VARS',
        '$_FILES',   '$HTTP_POST_FILES',
        '$_COOKIE',  '$HTTP_COOKIE_VARS',
        '$_SESSION', '$HTTP_SESSION_VARS',
        '$_REQUEST',
        '$_ENV',     '$HTTP_ENV_VARS',
    );

    /**
     * This method checks if a superglobal is used
     * and emits a rule violation.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('Variable') as $variable) {
            if (in_array($variable->getImage(), $this->superglobals)) {
                $this->addViolation(
                    $node,
                    array(
                        $node->getName(),
                        $variable->getImage()
                    )
                );
            }
        }
    }
}
