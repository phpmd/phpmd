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

use PHPMD\AbstractTestCase;

/**
 * Test case for the undefined variable rule.
 *
 * @coversDefaultClass \PHPMD\Rule\CleanCode\UndefinedVariable
 */
class UndefinedVariableTest extends AbstractTestCase
{
    /**
     * Get the rule under test.
     */
    public function getRule(): UndefinedVariable
    {
        return new UndefinedVariable();
    }

    /**
     * Tests the rule for cases where it should apply.
     *
     * @param string $file The test file to test against.
     *
     * @dataProvider getApplyingCases
     */
    public function testRuleAppliesTo(string $file): void
    {
        $this->expectRuleHasViolationsForFile($this->getRule(), static::ONE_VIOLATION, $file);
    }

    /**
     * Tests the rule for cases where it should not apply.
     *
     * @param string $file The test file to test against.
     *
     * @dataProvider getNotApplyingCases
     */
    public function testRuleDoesNotApplyTo(string $file): void
    {
        $this->expectRuleHasViolationsForFile($this->getRule(), static::NO_VIOLATION, $file);
    }
}
