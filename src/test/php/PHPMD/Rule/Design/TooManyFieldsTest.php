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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;

/**
 * Test case for the too many methods rule.
 *
 * @covers \PHPMD\Rule\Design\TooManyFields
 */
class TooManyFieldsTest extends AbstractTestCase
{
    /**
     * testRuleDoesNotApplyToClassesWithLessFieldsThanThreshold
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithLessFieldsThanThreshold()
    {
        $rule = new TooManyFields();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxfields', '42');
        $rule->apply($this->getClassMock('vars', 23));
    }

    /**
     * testRuleDoesNotApplyToClassesWithSameNumberOfFieldsAsThreshold
     *
     * @return void
     */
    public function testRuleDoesNotApplyToClassesWithSameNumberOfFieldsAsThreshold()
    {
        $rule = new TooManyFields();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->addProperty('maxfields', '42');
        $rule->apply($this->getClassMock('vars', 42));
    }

    /**
     * testRuleAppliesToClassesWithMoreFieldsThanThreshold
     *
     * @return void
     */
    public function testRuleAppliesToClassesWithMoreFieldsThanThreshold()
    {
        $rule = new TooManyFields();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->addProperty('maxfields', '23');
        $rule->apply($this->getClassMock('vars', 42));
    }
}
