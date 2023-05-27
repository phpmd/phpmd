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

use PHPMD\AbstractTest;
use PHPMD\ProcessingError;
use PHPMD\Stubs\RuleStub;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the SARIF renderer implementation.
 *
 * @covers \PHPMD\Renderer\SARIFRenderer
 */
class SARIFRendererTest extends AbstractTest
{
    /**
     * testRendererCreatesExpectedNumberOfJsonElements
     *
     * @return void
     */
    public function testRendererCreatesExpectedNumberOfJsonElements()
    {
        $writer = new WriterStub();

        $rule = new RuleStub('AnotherRuleStub');
        $rule->addExample('   class Example'. PHP_EOL . '{'.PHP_EOL . '}'. PHP_EOL . '   ');
        $rule->addExample(PHP_EOL . 'class AnotherExample'. PHP_EOL .
            '{' . PHP_EOL . '    public $var;' . PHP_EOL . '}' . PHP_EOL . '   ');
        $rule->setSince(null);

        $complexRuleViolationMock = $this->getRuleViolationMock(getcwd() . '/src/foobar.php', 23, 42, $rule);
        $complexRuleViolationMock
            ->method('getArgs')
            ->willReturn(array(123, 3.2, 'awesomeFunction()'));

        $violations = array(
            $this->getRuleViolationMock('/bar.php'),
            $this->getRuleViolationMock('/foo.php'),
            $complexRuleViolationMock,
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $renderer = new SARIFRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertJsonEquals(
            $writer->getData(),
            'renderer/sarif_renderer_expected.sarif',
            function ($actual) {
                $actual['runs'][0]['tool']['driver']['version'] = '@package_version@';
                return $actual;
            }
        );
    }

    /**
     * testRendererAddsProcessingErrorsToJsonReport
     *
     * @return void
     */
    public function testRendererAddsProcessingErrorsToJsonReport()
    {
        $writer = new WriterStub();

        $processingErrors = array(
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "' . static::createFileUri('foobar.php') . '".'),
            new ProcessingError('Cannot read file "/tmp/foo.php". Permission denied.'),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator(array())));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator($processingErrors)));

        $renderer = new SARIFRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertJsonEquals(
            $writer->getData(),
            'renderer/sarif_renderer_processing_errors.sarif',
            function ($actual) {
                $actual['runs'][0]['tool']['driver']['version'] = '@package_version@';
                return $actual;
            }
        );
    }
}
