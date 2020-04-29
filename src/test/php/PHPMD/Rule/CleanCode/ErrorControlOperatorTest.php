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
 * Error Control Operator Test
 *
 * @coversDefaultClass \PHPMD\Rule\CleanCode\ErrorControlOperator
 */
class ErrorControlOperatorTest extends AbstractTest
{
    /**
     * testAppliedToFunctions
     *
     * @return void
     * @covers ::apply
     */
    public function testDoesNotApplyToOtherUnaryOperatorsInFunction()
    {
        $rule = new ErrorControlOperator();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testAppliedToFunctions
     *
     * @return void
     * @covers ::apply
     */
    public function testAppliesToErrorControlOperatorInFunction()
    {
        $rule = new ErrorControlOperator();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }

    /**
     * testAppliedToClassesAndMethods
     *
     * @return void
     * @covers ::apply
     */
    public function testAppliedToClassesAndMethods()
    {
        $rule = new ErrorControlOperator();
        $rule->setReport($this->getReportMock(6));
        $rule->apply($this->getClass());
    }
}
