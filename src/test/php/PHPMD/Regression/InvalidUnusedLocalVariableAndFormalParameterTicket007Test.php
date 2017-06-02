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

/**
 * Regression test for issue 007.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers stdClass
 * @group phpmd
 * @group regression
 */
class InvalidUnusedLocalVariableAndFormalParameterTicket007Test extends AbstractTest
{
    /**
     * testLocalVariableUsedInDoubleQuoteStringGetsNotReported
     *
     * @return void
     */
    public function testLocalVariableUsedInDoubleQuoteStringGetsNotReported()
    {
        $rule = new UnusedLocalVariable();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }

    /**
     * testFormalParameterUsedInDoubleQuoteStringGetsNotReported
     *
     * @return void
     */
    public function testFormalParameterUsedInDoubleQuoteStringGetsNotReported()
    {
        $rule = new UnusedFormalParameter();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}
