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
 * Integration tests for the coupling between objects rule class.
 *
 * @since 1.1.0
 */
class CouplingBetweenObjectsIntegrationTest extends AbstractTestCase
{
    /**
     * testReportContainsCouplingBetweenObjectsWarning
     *
     * @outputBuffering enabled
     * @throws Throwable
     */
    public function testReportContainsCouplingBetweenObjectsWarning(): void
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
        static::assertStringContainsString(
            'has a coupling between objects value of 14. ' .
            'Consider to reduce the number of dependencies under 13.',
            $content
        );
    }
}
