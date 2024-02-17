<?php

declare(strict_types=1);

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

namespace PHPMD\RuleProperty;

use PHPMD\Rule;

interface RulePropertyType
{
    /**
     * @param class-string<Rule> $ruleClass
     */
    public static function createFromRuleProperty(
        string $ruleClass,
        string $key,
        mixed $value,
        RuleProperty $ruleProperty,
    ): self;
}
