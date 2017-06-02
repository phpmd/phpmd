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

use PHPMD\Rule\Naming\LongVariable;

/**
 * Regression test for issue 10096717.
 *
 * @link       https://www.pivotaltracker.com/story/show/10096717
 * @since      1.1.0
 *
 * @ticket 10096717
 * @covers stdClass
 * @group phpmd
 * @group phpmd::integration
 * @group integrationtest
 */
class LongVariablePrivatePropertiesTicket010096717Test extends AbstractTest
{
    /**
     * testRuleNotAppliesForLongPrivateProperty
     *
     * @return void
     */
    public function testRuleNotAppliesForLongPrivateProperty()
    {
        $rule = new LongVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maximum', 17);
        $rule->apply($this->getClass());
    }

    /**
     * testRuleNotAppliesForLongPrivateStaticProperty
     *
     * @return void
     */
    public function testRuleNotAppliesForLongPrivateStaticProperty()
    {
        $rule = new LongVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->addProperty('maximum', 17);
        $rule->apply($this->getClass());
    }
}
