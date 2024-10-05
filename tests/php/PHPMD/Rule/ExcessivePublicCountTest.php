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

namespace PHPMD\Rule;

use PHPMD\AbstractTestCase;

/**
 * Test case for the excessive use of public members rule.
 *
 * @covers \PHPMD\Rule\ExcessivePublicCount
 */
class ExcessivePublicCountTest extends AbstractTestCase
{
    /**
     * testRuleDoesNotApplyToClassesWithLessPublicMembersThanThreshold
     */
    public function testRuleDoesNotApplyToClassesWithLessPublicMembersThanThreshold(): void
    {
        $rule = new ExcessivePublicCount();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('cis', 23));
    }

    /**
     * testRuleAppliesToClassesWithSameNumberOfPublicMembersAsThreshold
     */
    public function testRuleAppliesToClassesWithSameNumberOfPublicMembersAsThreshold(): void
    {
        $rule = new ExcessivePublicCount();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '42');
        $rule->apply($this->getClassMock('cis', 42));
    }

    /**
     * testRuleAppliesToClassesWithMorePublicMembersThanThreshold
     */
    public function testRuleAppliesToClassesWithMorePublicMembersThanThreshold(): void
    {
        $rule = new ExcessivePublicCount();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('minimum', '23');
        $rule->apply($this->getClassMock('cis', 42));
    }
}
