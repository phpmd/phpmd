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

namespace PHPMD\Regression;

use PHPMD\PHPMD;
use PHPMD\Renderer\XMLRenderer;
use PHPMD\Report;
use PHPMD\RuleSetFactory;
use PHPMD\Stubs\WriterStub;

/**
 * Regression test for issue 001.
 *
 * @covers \stdClass
 */
class AcceptsFilesAndDirectoriesAsInputTicket001Test extends AbstractTest
{
    /**
     * testCliAcceptsDirectoryAsInput
     *
     * @return void
     */
    public function testCliAcceptsDirectoryAsInput()
    {
        self::changeWorkingDirectory();

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $ruleSetFactory = new RuleSetFactory();

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source'),
            $ruleSetFactory->getIgnorePattern('pmd-refset1'),
            array($renderer),
            $ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );
    }

    /**
     * testCliAcceptsSingleFileAsInput
     *
     * @return void
     */
    public function testCliAcceptsSingleFileAsInput()
    {
        self::changeWorkingDirectory();

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $ruleSetFactory = new RuleSetFactory();

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/FooBar.php'),
            $ruleSetFactory->getIgnorePattern('pmd-refset1'),
            array($renderer),
            $ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );
    }
}
