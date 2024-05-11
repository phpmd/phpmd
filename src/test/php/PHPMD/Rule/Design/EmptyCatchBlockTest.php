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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;

/**
 * Empty Catch Block Test
 *
 * @author Grégoire Paris <postmaster@greg0ire.fr>
 * @author Kamil Szymanski <kamilszymanski@gmail.com>
 */
class EmptyCatchBlockTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutTryCatchBlock
     */
    public function testRuleNotAppliesToMethodWithoutTryCatchBlock(): void
    {
        $rule = new EmptyCatchBlock();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToFunctionWithEmptyCatchBlock
     */
    public function testRuleAppliesToFunctionWithEmptyCatchBlock(): void
    {
        $rule = new EmptyCatchBlock();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToFunctionWithNonEmptyCatchBlock
     */
    public function testRuleNotAppliesToFunctionWithNonEmptyCatchBlock(): void
    {
        $rule = new EmptyCatchBlock();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleNotAppliesToCatchBlockWithComments
     */
    public function testRuleNotAppliesToCatchBlockWithComments(): void
    {
        $rule = new EmptyCatchBlock();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleWorksWithNestedTryCatchBlocksAndNonSPLExceptions
     */
    public function testRuleWorksWithNestedTryCatchBlocksAndNonSPLExceptions(): void
    {
        $rule = new EmptyCatchBlock();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }
}
