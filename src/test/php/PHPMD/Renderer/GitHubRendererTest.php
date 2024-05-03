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
 * @author Lukas Bestle <project-phpmd@lukasbestle.com>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Renderer;

use ArrayIterator;
use PHPMD\AbstractTestCase;
use PHPMD\ProcessingError;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the GitHub renderer implementation.
 *
 * @covers \PHPMD\Renderer\GitHubRenderer
 */
class GitHubRendererTest extends AbstractTestCase
{
    /**
     * testRendererCreatesExpectedNumberOfTextEntries
     *
     * @return void
     */
    public function testRendererCreatesExpectedNumberOfTextEntries()
    {
        // Create a writer instance.
        $writer = new WriterStub();

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1),
            $this->getRuleViolationMock('/foo.php', 2),
            $this->getRuleViolationMock('/foo.php', 3),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator([])));

        $renderer = new GitHubRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            "::warning file=/bar.php,line=1::Test description" . PHP_EOL .
            "::warning file=/foo.php,line=2::Test description" . PHP_EOL .
            "::warning file=/foo.php,line=3::Test description" . PHP_EOL,
            $writer->getData()
        );
    }

    /**
     * testRendererAddsProcessingErrorsToTextReport
     *
     * @return void
     */
    public function testRendererAddsProcessingErrorsToTextReport()
    {
        // Create a writer instance.
        $writer = new WriterStub();

        $errors = [
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "/tmp/baz.php".'),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator([])));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator($errors)));

        $renderer = new GitHubRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            "::error file=/tmp/foo.php::Failed for file \"/tmp/foo.php\"." . PHP_EOL .
            "::error file=/tmp/bar.php::Failed for file \"/tmp/bar.php\"." . PHP_EOL .
            "::error file=/tmp/baz.php::Failed for file \"/tmp/baz.php\"." . PHP_EOL,
            $writer->getData()
        );
    }
}
