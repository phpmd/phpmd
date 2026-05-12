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

/**
 * Internal rule used to report unused or invalid @SuppressWarnings annotations.
 *
 * This rule is not meant to be configured in rulesets. It is automatically
 * used by the RuleSet when it detects suppress-warning annotations that
 * don't match any active rule.
 */
final class UnusedSuppressWarnings extends AbstractRule implements
    ClassAware,
    EnumAware,
    FunctionAware,
    InterfaceAware,
    MethodAware,
    TraitAware
{
    public function apply(AbstractNode $node): void
    {
        // This rule is not applied directly. It is used as a rule reference
        // when reporting unused suppression violations from RuleSet::apply().
    }
}
