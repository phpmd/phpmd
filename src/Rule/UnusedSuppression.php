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

namespace PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Suppressions;

/**
 * Reports SuppressWarnings attributes that have no warning to suppress.
 *
 * Whether a suppression is used can only be told once every other rule has
 * been applied, so the work is done by {@see Suppressions} whenever this rule
 * is active, and applying the rule itself does nothing.
 */
final class UnusedSuppression extends AbstractRule implements ClassAware
{
    public function apply(AbstractNode $node): void
    {
    }
}
