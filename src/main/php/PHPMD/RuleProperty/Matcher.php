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

use InvalidArgumentException;
use PHPMD\Exception\InvalidRulePropertyTypeException;

final class Matcher implements RulePropertyType
{
    private ?array $patternList = null;

    public function __construct(
        private array|string $patterns,
        private string $separator = ',',
        private string $trim = '',
    ) {
        if ($separator === '') {
            throw new InvalidArgumentException("Separator can't be empty string");
        }
    }

    public static function createFromRuleProperty(
        string $ruleClass,
        string $key,
        mixed $value,
        RuleProperty $ruleProperty,
    ): self {
        if (!($ruleProperty instanceof MatchList)) {
            throw new InvalidRulePropertyTypeException(
                $ruleClass,
                $key,
                self::class . ' expects ' . MatchList::class . ' attribute',
            );
        }

        $value ??= [];

        if (!\is_string($value) && !\is_array($value)) {
            $valueType = \is_object($value) ? $value::class : \gettype($value);

            throw new InvalidRulePropertyTypeException(
                $ruleClass,
                $key,
                "expected string|array patterns, $valueType given",
            );
        }

        return new self($value, $ruleProperty->getSeparator(), $ruleProperty->getTrim());
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

    private function getPatterns(): array
    {
        return $this->patternList ??= array_filter(
            array_map(
                [$this, 'formatPattern'], // Use $this->formatPattern(...) when dropping PHP < 8.1
                explode($this->separator, implode($this->separator, (array)$this->patterns)),
            ),
            static fn ($value) => $value !== '//',
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
