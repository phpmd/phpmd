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
        $rule->addProperty('ignore-global', 'false');
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
        $expectedInvokes = strpos($file, 'testRuleAppliesTwice') !== false
            ? 2
            : static::ONE_VIOLATION;
        $this->expectRuleHasViolationsForFile($this->getRule(), $expectedInvokes, $file);
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
     * Tests that it does not apply to a class in root namespace when configured.
     *
     * @return void
     * @covers ::apply
     * @covers ::isGlobalNamespace
     */
    public function testRuleDoesNotApplyWhenSuppressed()
    {
        $rule = new MissingImport();
        $rule->addProperty('ignore-global', 'true');
        $files = $this->getFilesForCalledClass('testRuleAppliesTo*');
        foreach ($files as $file) {
            // Covers case when the new property is set and the rule *should* apply.
            if (strpos($file, 'WithNotImportedDeepDependencies')) {
                $this->expectRuleHasViolationsForFile($rule, static::ONE_VIOLATION, $file);
                continue;
            }
            // Covers case when the new property is set and the rule *should not* apply.
            $this->expectRuleHasViolationsForFile($rule, static::NO_VIOLATION, $file);
        }
    }
}
