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
use Throwable;

/**
 * Test case for the html renderer implementation.
 *
 * @covers \PHPMD\Renderer\HTMLRenderer
 */
class HTMLRendererTest extends AbstractTestCase
{
    /**
     * testRendererCreatesExpectedNumberOfTextEntries
     * @throws Throwable
     */
    public function testRendererCreatesExpectedHtmlTableRow(): void
    {
        // Create a writer instance.
        $writer = new WriterStub();

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1),
            $this->getRuleViolationMock('/foo.php', 2),
            $this->getRuleViolationMock('/foo.php', 3),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->will(static::returnValue(new ArrayIterator($violations)));

        $extraLineInExcerpt = 2;
        $renderer = new HTMLRenderer($extraLineInExcerpt);
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertMatchesRegularExpression(
            "~.*<section class='prb' id='p-(\d+)'> <header> <h3> <a href='#p-\d+' class='indx'>.*~",
            $writer->getData()
        );
    }
}
