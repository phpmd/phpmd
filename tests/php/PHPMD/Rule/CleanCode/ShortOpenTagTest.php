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

use PDepend\Source\AST\ASTClass;
use PHPMD\AbstractTestCase;
use PHPMD\Node\ClassNode;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use RuntimeException;

/**
 * Short Open Tag Test
 *
 * @coversDefaultClass \PHPMD\Rule\CleanCode\ShortOpenTag
 */
class ShortOpenTagTest extends AbstractTestCase
{
    /**
     * Tests that the rule applies to a short open tag preceding a function.
     *
     * @covers ::apply
     */
    public function testAppliesToShortOpenTagInFunction(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that the rule applies once per short open tag found in a file
     * that declares a class, deduplicating repeated visits to the same file.
     *
     * @covers ::apply
     */
    public function testAppliesToShortOpenTagInClass(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getClass());
    }

    /**
     * Tests that the rule does not apply to files only using the full '<?php' tag.
     *
     * @covers ::apply
     */
    public function testDoesNotApplyToFullOpenTag(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that the rule does not apply to a '<?' that only appears as text
     * inside a string literal, not as an actual open tag.
     *
     * @covers ::apply
     */
    public function testDoesNotApplyToShortTagInsideString(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that the rule does not apply to the always-available '<?=' short echo tag.
     *
     * @covers ::apply
     */
    public function testDoesNotApplyToShortEchoTag(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that the rule does not apply to a leading '<?xml ...?>' declaration.
     *
     * @covers ::apply
     */
    public function testDoesNotApplyToXmlDeclaration(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that a real short open tag is still flagged even when the file
     * also contains an '<?xml ...?>' declaration.
     *
     * @covers ::apply
     */
    public function testAppliesToShortOpenTagAfterXmlDeclaration(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that the rule does not apply to a leading '<?XML ...?>' declaration.
     *
     * @covers ::apply
     */
    public function testDoesNotApplyToUppercaseXmlDeclaration(): void
    {
        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * Tests that visiting several nodes backed by the same file (e.g. several
     * classes declared in one file) only scans and reports that file once.
     *
     * @covers ::apply
     */
    public function testAppliesOnlyOncePerFileAcrossMultipleNodes(): void
    {
        $fileName = static::createResourceUriForTest('testAppliesToShortOpenTagInFunction.php');

        $rule = new ShortOpenTag();
        $rule->setReport($this->getReportWithOneViolation());

        $rule->apply($this->getNodeMockWithFileName($fileName));
        $rule->apply($this->getNodeMockWithFileName($fileName));
    }

    /**
     * @return ClassNode&MockObject
     */
    private function getNodeMockWithFileName(string $fileName): ClassNode
    {
        $node = $this->getMockBuilder(ClassNode::class)
            ->setConstructorArgs([new ASTClass('FooBar')])
            ->getMock();
        $node->method('getFileName')->willReturn($fileName);

        return $node;
    }

    /**
     * Tests that a bare '<?' tokenized as T_OPEN_TAG (what real PHP produces
     * when "short_open_tag" is enabled, which can't be forced at runtime) is
     * flagged, using a hand-built token array to exercise that branch
     * regardless of the local ini value.
     *
     * @covers ::scanTokens
     */
    public function testScanTokensFlagsShortOpenTagToken(): void
    {
        $lines = $this->invokeScanTokens([
            [T_INLINE_HTML, '<html>', 1],
            [T_OPEN_TAG, '<?', 10],
            [T_ECHO, 'echo', 10],
            [T_WHITESPACE, ' ', 10],
            [T_CONSTANT_ENCAPSED_STRING, "'foo'", 10],
            ';',
        ]);

        static::assertSame([10], $lines);
    }

    /**
     * Tests that an XML declaration tokenized as T_OPEN_TAG followed by a
     * separate T_STRING('xml') token (real PHP's shape when "short_open_tag"
     * is enabled) is not flagged.
     *
     * @covers ::scanTokens
     */
    public function testScanTokensIgnoresXmlDeclarationToken(): void
    {
        $lines = $this->invokeScanTokens([
            [T_OPEN_TAG, '<?', 1],
            [T_STRING, 'xml', 1],
            [T_WHITESPACE, ' ', 1],
            [T_STRING, 'version', 1],
            [T_CONSTANT_ENCAPSED_STRING, '"1.0"', 1],
            [T_CLOSE_TAG, '?>', 1],
        ]);

        static::assertSame([], $lines);
    }

    /**
     * Tests that the XML declaration exclusion is case-insensitive when
     * matched via the T_OPEN_TAG + T_STRING token shape.
     *
     * @covers ::scanTokens
     */
    public function testScanTokensIgnoresUppercaseXmlDeclarationToken(): void
    {
        $lines = $this->invokeScanTokens([
            [T_OPEN_TAG, '<?', 1],
            [T_STRING, 'XML', 1],
            [T_WHITESPACE, ' ', 1],
            [T_STRING, 'version', 1],
            [T_CONSTANT_ENCAPSED_STRING, '"1.0"', 1],
            [T_CLOSE_TAG, '?>', 1],
        ]);

        static::assertSame([], $lines);
    }

    /**
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return int[]
     */
    private function invokeScanTokens(array $tokens): array
    {
        $rule = new ShortOpenTag();
        $method = new ReflectionMethod(ShortOpenTag::class, 'scanTokens');
        $method->setAccessible(true);

        $lines = $method->invoke($rule, $tokens);
        if (!is_array($lines)) {
            throw new RuntimeException('ShortOpenTag::scanTokens() did not return an array.');
        }

        $result = [];
        foreach ($lines as $line) {
            if (!is_int($line)) {
                throw new RuntimeException('ShortOpenTag::scanTokens() did not return an array of integers.');
            }

            $result[] = $line;
        }

        return $result;
    }
}
