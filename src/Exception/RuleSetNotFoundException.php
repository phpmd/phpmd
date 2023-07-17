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
 * This type of exception is thrown when a not existing rule-set was specified.
 */
class RuleSetNotFoundException extends \RuntimeException
{
    /**
     * Constructs a new exception for the given rule-set identifier or file name.
     *
     * @param string $ruleSet The rule-set identifier or file name.
     */
    public function __construct($ruleSet)
    {
        parent::__construct('Cannot find specified rule-set "' . $ruleSet . '".');
    }
}
