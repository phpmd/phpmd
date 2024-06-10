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
use Throwable;

/**
 * Test case for the unused local variable rule.
 *
 * @covers \PHPMD\Rule\AbstractLocalVariable
 * @covers \PHPMD\Rule\UnusedLocalVariable
 */
class UnusedLocalVariableTest extends AbstractTestCase
{
    /**
     * Get the rule under test.
     *
     * @param string $file
     * @return UnusedLocalVariable
     */
    public function getRule($file)
    {
        $rule = new UnusedLocalVariable();

        // In this test suite, we'll set allow-unused-foreach-variables to true when the test
        // file name mention Foreach*WhenIgnored.
        $rule->addProperty(
            'allow-unused-foreach-variables',
            preg_match('/Foreach.*WhenIgnored/', $file) ? 'true' : 'false'
        );

        // In the file names of this test suite, $_ is called "Whitelisted", so we add it
        // to exceptions when it's in the name.
        if (preg_match('/Whitelisted/', $file)) {
            $rule->addProperty('exceptions', '_');
        }

        return $rule;
    }

    /**
     * Tests the rule for cases where it should apply.
     *
     * @param string $file The test file to test against.
     * @throws Throwable
     * @dataProvider getApplyingCases
     */
    public function testRuleAppliesTo($file): void
    {
        $this->expectRuleHasViolationsForFile($this->getRule($file), static::ONE_VIOLATION, $file);
    }

    /**
     * Tests the rule for cases where it should not apply.
     *
     * @param string $file The test file to test against.
     * @throws Throwable
     * @dataProvider getNotApplyingCases
     */
    public function testRuleDoesNotApplyTo($file): void
    {
        $this->expectRuleHasViolationsForFile($this->getRule($file), static::NO_VIOLATION, $file);
    }
}
