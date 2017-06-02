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

use PHPMD\AbstractTest;
use PHPMD\TextUI\Command;

/**
 * Test case for the goto statement GotoStatementIntegrationTest.
 *
 * @since      1.1.0
 *
 * @group phpmd
 * @group phpmd::integration
 * @group integrationtest
 */
class GotoStatementIntegrationTest extends AbstractTest
{
    /**
     * testReportContainsGotoStatementWarning
     *
     * @return void
     * @outputBuffering enabled
     */
    public function testReportContainsGotoStatementWarning()
    {
        $file = self::createTempFileUri();

        Command::main(
            array(
                __FILE__,
                $this->createCodeResourceUriForTest(),
                'text',
                'design',
                '--reportfile',
                $file
            )
        );

        self::assertContains('utilizes a goto statement.', file_get_contents($file));
    }
}
