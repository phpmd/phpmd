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

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractTest;

/**
 * @coversDefaultClass \PHPMD\Rule\CleanCode\BooleanArgumentFlag
 */
class BooleanArgumentFlagTest extends AbstractTest
{
    /**
     * Get the rule under test.
     *
     * @return BooleanArgumentFlag
     */
    public function getRule()
    {
        $rule = new BooleanArgumentFlag();

        $rule->addProperty('exceptions', 'MyClass');
        $rule->addProperty('ignorepattern', '/WhitelistedMethod$/');

        return $rule;
    }

    /**
     * Tests the rule for cases where it should apply.
     *
     * @param string $file The test file to test against.
     * @return void
     * @dataProvider getApplyingCases
     */
    public function testRuleAppliesTo($file)
    {
        $this->expectRuleHasViolationsForFile($this->getRule(), static::ONE_VIOLATION, $file);
    }

    /**
     * Tests the rule for cases where it should not apply.
     *
     * @param string $file The test file to test against.
     * @return void
     * @dataProvider getNotApplyingCases
     */
    public function testRuleDoesNotApplyTo($file)
    {
        $this->expectRuleHasViolationsForFile($this->getRule(), static::NO_VIOLATION, $file);
    }
}
