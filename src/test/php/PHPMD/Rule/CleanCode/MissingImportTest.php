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
 * MissingImport Tests
 *
 * @coversDefaultClass \PHPMD\Rule\CleanCode\MissingImport
 */
class MissingImportTest extends AbstractTest
{
    /**
     * Get the rule under test.
     *
     * @return MissingImport
     */
    public function getRule()
    {
        $rule = new MissingImport();
        $rule->addProperty('ignore-global', false);
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

    /**
     * Tests that it applies to a class that has fully qualified class names
     *
     * @return void
     * @covers ::apply
     * @covers ::isSelfReference
     */
    public function testRuleAppliesTwiceToClassWithNotImportedDependencies()
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(2));
        $rule->apply($this->getMethod());
    }

    /**
     * Tests the rule ignores classes in global namespace with `ignore-global`.
     *
     * @param string $file The test file to test against.
     * @return void
     * @dataProvider getApplyingCases
     */
    public function testRuleDoesNotApplyWithIgnoreGlobalProperty($file)
    {
        $rule = $this->getRule();
        $rule->addProperty('ignore-global', true);
        $this->expectRuleHasViolationsForFile($rule, static::NO_VIOLATION, $file);
    }
}
