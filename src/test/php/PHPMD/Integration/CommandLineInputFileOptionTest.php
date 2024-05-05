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

namespace PHPMD\Integration;

use PHPMD\AbstractTestCase;
use PHPMD\TextUI\Command;

/**
 * Integration tests for the command line option <em>--inputfile</em>.
 *
 * @since 1.1.0
 */
class CommandLineInputFileOptionTest extends AbstractTestCase
{
    /**
     * testReportContainsExpectedRuleViolationWarning
     *
     * @outputBuffering enabled
     */
    public function testReportContainsExpectedRuleViolationWarning(): void
    {
        self::assertStringContainsString(
            "Avoid unused local variables such as '\$foo'.",
            self::runCommandLine()
        );
    }

    /**
     * testReportNotContainsRuleViolationWarningForFileNotInList
     *
     * @outputBuffering enabled
     */
    public function testReportNotContainsRuleViolationWarningForFileNotInList(): void
    {
        self::assertStringNotContainsString(
            "Avoid unused local variables such as '\$bar'.",
            self::runCommandLine()
        );
    }

    /**
     * Runs the PHPMD command line interface and returns the report content.
     *
     * @return string
     */
    protected static function runCommandLine()
    {
        $inputfile = self::createResourceUriForTest('inputfile.txt');
        $reportfile = self::createTempFileUri();

        self::changeWorkingDirectory(dirname($inputfile));

        Command::main(
            [
                __FILE__,
                'text',
                'unusedcode',
                '--reportfile',
                $reportfile,
                '--inputfile',
                $inputfile,
            ]
        );

        return file_get_contents($reportfile);
    }
}
