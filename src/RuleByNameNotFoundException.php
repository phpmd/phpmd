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

namespace PHPMD;

use RuntimeException;

/**
 * When a configured rule was not found by name
 */
final class RuleByNameNotFoundException extends RuntimeException
{
    /**
     * Constructs a new RuleByNameNotFoundException.
     *
     * @param string $ruleName The name of the rule that was not found.
     */
    public function __construct(string $ruleName)
    {
        parent::__construct('Cannot find rule by name: ' . $ruleName);
    }
}
