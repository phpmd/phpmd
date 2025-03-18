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

namespace PHPMD\Renderer;

use ArrayIterator;
use PHPMD\AbstractTestCase;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the ansi renderer implementation.
 *
 * @covers \PHPMD\Renderer\AnsiRendererTest
 */
class AnsiRendererTest extends AbstractTestCase
{
    /**
     * testRendererOutputsForReportWithContents
     */
    public function testRendererOutputsForReportWithContents(): void
    {
        $writer = new WriterStub();

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1),
            $this->getRuleViolationMock('/foo.php', 2),
            $this->getRuleViolationMock('/foo.php', 3),
        ];

        $errors = [
            $this->getErrorMock(),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::atLeastOnce())
            ->method('getRuleViolations')
            ->will(static::returnValue(new ArrayIterator($violations)));
        $report->expects(static::atLeastOnce())
            ->method('isEmpty')
            ->will(static::returnValue(false));
        $report->expects(static::atLeastOnce())
            ->method('hasErrors')
            ->will(static::returnValue(true));
        $report->expects(static::atLeastOnce())
            ->method('getErrors')
            ->will(static::returnValue(new ArrayIterator($errors)));
        $report->expects(static::once())
            ->method('getElapsedTimeInMillis')
            ->will(static::returnValue(200));

        $renderer = new AnsiRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $expectedChunks = [
            PHP_EOL . 'FILE: /bar.php' . PHP_EOL . '--------------' . PHP_EOL,
            " 1 | \e[31mVIOLATION\e[0m | Test description" . PHP_EOL,
            PHP_EOL,
            PHP_EOL . 'FILE: /foo.php' . PHP_EOL . '--------------' . PHP_EOL,
            " 2 | \e[31mVIOLATION\e[0m | Test description" . PHP_EOL,
            " 3 | \e[31mVIOLATION\e[0m | Test description" . PHP_EOL,
            PHP_EOL . "\e[33mERROR\e[0m while parsing /foo/baz.php" . PHP_EOL . '--------------------------------' . PHP_EOL,
            'Error in file "/foo/baz.php"' . PHP_EOL,
            PHP_EOL . 'Found 3 violations and 1 error in 200ms' . PHP_EOL,
        ];

        foreach ($writer->getChunks() as $i => $chunk) {
            static::assertEquals(
                $expectedChunks[$i],
                $chunk,
                sprintf('Chunk %s did not match expected string', $i)
            );
        }
    }

    /**
     * testRendererOutputsForReportWithoutContents
     */
    public function testRendererOutputsForReportWithoutContents(): void
    {
        $writer = new WriterStub();

        $report = $this->getReportWithNoViolation();
        $report->expects(static::atLeastOnce())
            ->method('getRuleViolations')
            ->will(static::returnValue(new ArrayIterator([])));
        $report->expects(static::atLeastOnce())
            ->method('isEmpty')
            ->will(static::returnValue(true));
        $report->expects(static::atLeastOnce())
            ->method('hasErrors')
            ->will(static::returnValue(false));
        $report->expects(static::atLeastOnce())
            ->method('getErrors')
            ->will(static::returnValue(new ArrayIterator([])));
        $report->expects(static::once())
            ->method('getElapsedTimeInMillis')
            ->will(static::returnValue(200));

        $renderer = new AnsiRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $expectedChunks = [
            PHP_EOL . 'Found 0 violations and 0 errors in 200ms' . PHP_EOL,
            PHP_EOL . "\e[32mNo mess detected\e[0m" . PHP_EOL,
        ];

        foreach ($writer->getChunks() as $i => $chunk) {
            static::assertEquals(
                $expectedChunks[$i],
                $chunk,
                sprintf('Chunk %s did not match expected string', $i)
            );
        }
    }
}
