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

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\EnumAware;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\InterfaceAware;
use PHPMD\Rule\TraitAware;

/**
 * Short Open Tag Rule
 *
 * This rule detects usage of the short PHP open tag '<?' instead of '<?php'.
 *
 * @link https://www.php.net/manual/en/ini.core.php#ini.short-open-tag
 */
final class ShortOpenTag extends AbstractRule implements ClassAware, EnumAware, FunctionAware, InterfaceAware, TraitAware
{
    /**
     * Files that have already been scanned, so each file is only reported once
     * no matter how many class/trait/enum/function/interface nodes it contains.
     *
     * @var array<string, bool>
     */
    private array $processedFiles = [];

    public function apply(AbstractNode $node): void
    {
        $fileName = $node->getFileName();
        if ($fileName === null) {
            return;
        }

        if (isset($this->processedFiles[$fileName])) {
            return;
        }

        $this->processedFiles[$fileName] = true;

        foreach ($this->findShortOpenTagLines($fileName) as $line) {
            $this->addViolation($node, [(string) $line]);
        }
    }

    /**
     * Finds every line where a bare '<?' short open tag is used.
     *
     * '<?php' and '<?=' are always tokenized correctly by token_get_all()
     * regardless of the "short_open_tag" ini setting, but a bare '<?' is only
     * recognized as T_OPEN_TAG when "short_open_tag" is enabled. When it is
     * disabled (the common default), the "<?" ends up merged into the
     * surrounding T_INLINE_HTML token instead, so that case is also scanned
     * for explicitly to keep detection independent of the local ini value.
     *
     * @return int[]
     */
    private function findShortOpenTagLines(string $fileName): array
    {
        $source = file_get_contents($fileName);
        if ($source === false) {
            return [];
        }

        return $this->scanTokens(token_get_all($source));
    }

    /**
     * Walks a token_get_all() result and returns every line where a bare
     * '<?' short open tag is used. Split out from findShortOpenTagLines() so
     * it can be exercised directly with hand-built token arrays, since real
     * tokenization of a bare '<?' is gated by the "short_open_tag" ini
     * setting and can't be forced at runtime.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return int[]
     */
    private function scanTokens(array $tokens): array
    {
        $lines = [];

        foreach ($tokens as $index => $token) {
            if (!is_array($token)) {
                continue;
            }

            [$type, $image, $line] = $token;

            if ($type === T_INLINE_HTML) {
                foreach ($this->findShortOpenTagLinesInHtml($image, $line) as $htmlLine) {
                    $lines[] = $htmlLine;
                }

                continue;
            }

            if ($type === T_OPEN_TAG && stripos($image, '<?php') !== 0 && !$this->isXmlProlog($tokens, $index)) {
                $lines[] = $line;
            }
        }

        $lines = array_unique($lines);
        sort($lines);

        return $lines;
    }

    /**
     * @return int[]
     */
    private function findShortOpenTagLinesInHtml(string $html, int $startLine): array
    {
        $lines = [];
        if (!preg_match_all('/<\?(?!=|xml\b)/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
            return $lines;
        }

        foreach ($matches[0] as $match) {
            $offset = $match[1];
            $lines[] = $startLine + substr_count($html, "\n", 0, $offset);
        }

        return $lines;
    }

    /**
     * Checks whether a short open tag is immediately followed by "xml", i.e.
     * it is an XML declaration such as '<?xml version="1.0"?>' rather than a
     * short open tag mistake.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private function isXmlProlog(array $tokens, int $openTagIndex): bool
    {
        for ($i = $openTagIndex + 1, $count = count($tokens); $i < $count; ++$i) {
            $token = $tokens[$i];
            if (!is_array($token)) {
                return false;
            }

            if ($token[0] === T_WHITESPACE) {
                continue;
            }

            return $token[0] === T_STRING && strcasecmp($token[1], 'xml') === 0;
        }

        return false;
    }
}
