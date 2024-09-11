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

use PHPMD\Rule\UnusedFormalParameter;
use PHPMD\Rule\UnusedLocalVariable;
use Throwable;

/**
 * Regression test for issue 007.
 */
class InvalidUnusedLocalVariableAndFormalParameterTicket007RegressionTest extends AbstractRegressionTestCase
{
    /**
     * testLocalVariableUsedInDoubleQuoteStringGetsNotReported
     * @throws Throwable
     */
    public function testLocalVariableUsedInDoubleQuoteStringGetsNotReported(): void
    {
        $rule = new UnusedLocalVariable();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testFormalParameterUsedInDoubleQuoteStringGetsNotReported
     * @throws Throwable
     */
    public function testFormalParameterUsedInDoubleQuoteStringGetsNotReported(): void
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }
}
