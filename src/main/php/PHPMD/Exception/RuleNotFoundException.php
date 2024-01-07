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

namespace PHPMD\Exception;

/**
 * This type of exception is thrown when a not existing rule was specified.
 */
class RuleNotFoundException extends \RuntimeException
{
    /**
     * Constructs a new exception for the given rule identifier or name.
     *
     * @param string $rule The rule identifier or name.
     */
    public function __construct(string $rule)
    {
        parent::__construct('Cannot find specified rule "' . $rule . '".');
    }
}
