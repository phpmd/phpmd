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
use Throwable;

/**
 * Test case for the goto statement GotoStatementIntegrationTest.
 *
 * @since 1.1.0
 */
class GotoStatementIntegrationTest extends AbstractTestCase
{
    /**
     * testReportContainsGotoStatementWarning
     *
     * @outputBuffering enabled
     * @throws Throwable
     */
    public function testReportContainsGotoStatementWarning(): void
    {
        $file = self::createTempFileUri();

        Command::main(
            [
                __FILE__,
                $this->createCodeResourceUriForTest(),
                'text',
                'design',
                '--reportfile',
                $file,
            ]
        );
        $content = file_get_contents($file);

        static::assertNotFalse($content);
        static::assertStringContainsString('utilizes a goto statement.', $content);
    }
}
