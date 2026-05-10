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
use PHPMD\Stubs\RuleStub;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test case for the SARIF renderer implementation.
 */
#[CoversClass(SARIFRenderer::class)]
class SARIFRendererTest extends AbstractTestCase
{
    /**
     * testRendererCreatesExpectedNumberOfJsonElements
     */
    public function testRendererCreatesExpectedNumberOfJsonElements(): void
    {
        $writer = new BufferedOutput();

        $rule = new RuleStub('AnotherRuleStub');
        $rule->addExample("   class Example\n{\n}\n   ");
        $rule->addExample("\nclass AnotherExample\n{\n    public \$var;\n}\n   ");
        $rule->setSince(null);

        $complexRuleViolationMock = $this->getRuleViolationMock(getcwd() . '/src/foobar.php', 23, 42, $rule);
        $complexRuleViolationMock
            ->method('getArgs')
            ->willReturn([123, 3.2, 'awesomeFunction()']);

        $violations = [
            $this->getRuleViolationMock('/bar.php'),
            $this->getRuleViolationMock('/foo.php'),
            $complexRuleViolationMock,
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator($violations));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator([]));

        $renderer = new SARIFRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();
        $actual = json_decode($writer->fetch(), true);
        static::assertIsArray($actual);
        static::assertIsArray($actual['runs']);
        static::assertIsArray($actual['runs'][0]);
        static::assertIsArray($actual['runs'][0]['tool']);
        static::assertIsArray($actual['runs'][0]['tool']['driver']);
        $actual['runs'][0]['tool']['driver']['version'] = '@package_version@';
        static::assertIsArray($actual['runs'][0]['originalUriBaseIds']);
        static::assertIsArray($actual['runs'][0]['originalUriBaseIds']['WORKINGDIR']);
        $actual['runs'][0]['originalUriBaseIds']['WORKINGDIR']['uri'] = 'file://#{workingDirectory}/';
        $flags = JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR;
        $expected = file_get_contents(__DIR__ . '/../../../resources/files/renderer/sarif_renderer_expected.sarif');

        static::assertNotFalse($expected);
        static::assertSame(
            json_encode(json_decode($expected), $flags),
            json_encode($actual, $flags)
        );
    }

    /**
     * testRendererAddsProcessingErrorsToJsonReport
     */
    public function testRendererAddsProcessingErrorsToJsonReport(): void
    {
        $writer = new BufferedOutput();

        $processingErrors = [
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "' . static::createFileUri('foobar.php') . '".'),
            new ProcessingError('Cannot read file "/tmp/foo.php". Permission denied.'),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator([]));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator($processingErrors));

        $renderer = new SARIFRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $key = substr(json_encode(realpath(__DIR__ . '/../../../resources/files'), JSON_THROW_ON_ERROR), 1, -1);
        $data = strtr($writer->fetch(), [
            $key => '#{rootDirectory}',
            'tests\\\\resources\\\\files' => 'tests/resources/files',
        ]);
        $actual = json_decode($data, true);
        static::assertIsArray($actual);
        static::assertIsArray($actual['runs']);
        static::assertIsArray($actual['runs'][0]);
        static::assertIsArray($actual['runs'][0]['tool']);
        static::assertIsArray($actual['runs'][0]['tool']['driver']);
        $actual['runs'][0]['tool']['driver']['version'] = '@package_version@';
        static::assertIsArray($actual['runs'][0]['originalUriBaseIds']);
        static::assertIsArray($actual['runs'][0]['originalUriBaseIds']['WORKINGDIR']);
        $actual['runs'][0]['originalUriBaseIds']['WORKINGDIR']['uri'] = 'file://#{workingDirectory}/';
        $flags = JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR;
        $expected = file_get_contents(
            __DIR__ . '/../../../resources/files/renderer/sarif_renderer_processing_errors.sarif'
        );

        static::assertNotFalse($expected);
        static::assertSame(
            json_encode(json_decode($expected), $flags),
            json_encode($actual, $flags)
        );
    }
}
