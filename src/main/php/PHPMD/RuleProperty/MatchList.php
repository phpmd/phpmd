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

use Attribute;

/**
 * Option set by a list of patterns (as array or comma-separator string).
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class MatchList extends AbstractRuleProperty
{
    protected const DEFAULT_KEYS = 'exceptions';

    public function __construct(
        array|string|null $keys = null,
        private string $separator = ',',
        private string $trim = '',
    ) {
        parent::__construct($keys);
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function getTrim(): string
    {
        return $this->trim;
    }
}
