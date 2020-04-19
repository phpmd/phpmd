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
 * Test case for the undefined variable rule.
 *
 * @covers \PHPMD\Rule\CleanCode\UndefinedVariable
 * @covers \PHPMD\Rule\AbstractLocalVariable
 */
class UndefinedVariableTest extends AbstractTest
{
    /**
     * @return UndefinedVariable
     */
    public function getRule()
    {
        return new UndefinedVariable();
    }

    /**
     * @dataProvider getSuccessCases
     */
    public function testRuleAppliesToSuccessFiles($file)
    {
        $this->expectRuleInvokesForFile($this->getRule(), 1, $file);
    }

    /**
     * @dataProvider getFailureCases
     */
    public function testRuleDoesNotApplyToFailureFiles($file)
    {
        $this->expectRuleInvokesForFile($this->getRule(), 0, $file);
    }
}
