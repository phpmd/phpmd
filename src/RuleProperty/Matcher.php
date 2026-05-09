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

use PHPMD\Exception\InvalidArgumentException;

final class Matcher implements RulePropertyType
{
    /** @var ?list<string> */
    private ?array $patternList = null;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        /** @var list<string>|string */
        private array|string $patterns,
        private string $separator = ',',
        private string $trim = '',
    ) {
        if ($separator === '') {
            throw new InvalidArgumentException("Separator can't be empty string");
        }
    }

    /**
     * @param list<string>|string|null $value
     * @throws InvalidArgumentException
     */
    public static function createFromRuleProperty(
        string $ruleClass,
        string $key,
        null|array|string $value,
        MatchList $ruleProperty,
    ): self {
        return new self($value ?? [], $ruleProperty->getSeparator(), $ruleProperty->getTrim());
    }

    public function contains(string $value): bool
    {
        foreach ($this->getPatterns() as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function getPatterns(): array
    {
        assert($this->separator !== '');

        return $this->patternList ??= array_values(
            array_filter(
                array_map(
                    $this->formatPattern(...),
                    explode($this->separator, implode($this->separator, (array) $this->patterns)),
                ),
                static fn($value) => $value !== '//',
            )
        );
    }

    private function formatPattern(string $pattern): string
    {
        return '/' . strtr(preg_quote($this->trim($pattern), '/'), [
            '\\*' => '.*',
        ]) . '/';
    }

    private function trim(string $value): string
    {
        return trim($value, "$this->trim \n\r\t\v\0");
    }
}
