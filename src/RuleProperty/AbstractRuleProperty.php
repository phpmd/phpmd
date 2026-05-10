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

namespace PHPMD\RuleProperty;

abstract class AbstractRuleProperty implements RuleProperty
{
    /** @var list<string>|string */
    protected const DEFAULT_KEYS = [];

    /** @var ?list<string> */
    private ?array $keys;

    /**
     * @param list<string>|string|null $keys
     */
    public function __construct(null|array|string $keys = null)
    {
        $this->keys = $keys === null ? null : (array) $keys;
    }

    /**
     * @param list<string>|string|null $defaultKeys
     * @return list<string>
     */
    public function getKeys(null|array|string $defaultKeys = null): array
    {
        return $this->keys ?? (array) ($defaultKeys ?? static::DEFAULT_KEYS);
    }
}
