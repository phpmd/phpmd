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
 *
 * @link http://phpmd.org/
 */

namespace PHPMD\Regression;

use PHPMD\PHPMD;
use PHPMD\Renderer\TextRenderer;
use PHPMD\Report;
use PHPMD\RuleSetFactory;
use PHPMD\Writer\StreamWriter;

/**
 * Regression test for issue 14990109.
 *
 * @link https://www.pivotaltracker.com/story/show/24975295
 * @since 1.3.1
 */
class MaximumNestingLevelTicket24975295RegressionTest extends AbstractRegressionTestCase
{
    /**
     * testLocalVariableUsedInDoubleQuoteStringGetsNotReported
     *
     * @return void
     *
     * @outputBuffering enabled
     */
    public function testLocalVariableUsedInDoubleQuoteStringGetsNotReported()
    {
        $renderer = new TextRenderer();
        $renderer->setWriter(new StreamWriter(self::createTempFileUri()));

        $inputs = self::createCodeResourceUriForTest();
        $rules = 'unusedcode';
        $renderers = [$renderer];
        $factory = new RuleSetFactory();


        $phpmd = new PHPMD();
        $phpmd->processFiles(
            $inputs,
            $factory->getIgnorePattern($rules),
            $renderers,
            $factory->createRuleSets($rules),
            new Report()
        );
    }
}
