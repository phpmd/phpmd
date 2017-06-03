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
 * Test case for the {@link \PHPMD\Rule\Design\CouplingBetweenObjects} class.
 *
 * @link https://www.pivotaltracker.com/story/show/10474987
 * @covers \PHPMD\Rule\Design\CouplingBetweenObjects
 */
class CouplingBetweenObjectsTest extends AbstractTest
{
    /**
     * testRuleNotAppliesToClassWithCboLessThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToClassWithCboLessThanThreshold()
    {
        $rule = new CouplingBetweenObjects();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('cbo', 41));
    }

    /**
     * testRuleAppliesToClassWithCboEqualToThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassWithCboEqualToThreshold()
    {
        $rule = new CouplingBetweenObjects();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('cbo', 42));
    }

    /**
     * testRuleAppliesToClassWithCboGreaterThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassWithCboGreaterThanThreshold()
    {
        $rule = new CouplingBetweenObjects();
        $rule->setReport($this->getReportMock(1));
        $rule->addProperty('minimum', '41');
        $rule->apply($this->getClassMock('cbo', 42));
    }
}
