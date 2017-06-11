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

use PHPMD\AbstractTest;

/**
 * Count In Loop Expression Test
 *
 * @author Kamil Szymanski <kamilszymanski@gmail.com>
 */
class CountInLoopExpressionTest extends AbstractTest
{
    /**
     * testRuleAppliesToAllTypesOfLoops
     *
     * @return void
     */
    public function testRuleAppliesToAllTypesOfLoops()
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }
    /**
     * testRuleNotApplyToExpressionElsewhere
     *
     * @return void
     */
    public function testRuleNotApplyToExpressionElsewhere()
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
    /**
     * testRuleApplyToNestedLoops
     *
     * @return void
     */
    public function testRuleApplyToNestedLoops()
    {
        $rule = new CountInLoopExpression();
        $rule->setReport($this->getReportMock(8));
        $rule->apply($this->getFunction());
    }
}
