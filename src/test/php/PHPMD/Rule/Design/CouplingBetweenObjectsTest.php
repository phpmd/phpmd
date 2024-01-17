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
 * Test case for the {@link \PHPMD\Rule\Design\CouplingBetweenObjects} class.
 *
 * @link https://www.pivotaltracker.com/story/show/10474987
 * @covers \PHPMD\Rule\Design\CouplingBetweenObjects
 */
class CouplingBetweenObjectsTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToClassWithCboLessThanThreshold
     *
     * @return void
     */
    public function testRuleNotAppliesToClassWithCboLessThanThreshold()
    {
        $rule = new CouplingBetweenObjects();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maximum', '42');
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
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('maximum', '42');
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
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('maximum', '41');
        $rule->apply($this->getClassMock('cbo', 42));
    }
}
