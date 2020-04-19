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

namespace PHPMD;

abstract class SimpleRuleTestCase extends AbstractTest
{
    /**
     * @return AbstractRule
     */
    abstract public function getRule();

    /**
     * @return string[]
     */
    public function getSuccessFiles()
    {
        return $this->getFilesForCalledClass('testRuleAppliesTo*');
    }

    /**
     * @return string[]
     */
    public function getFailureFiles()
    {
        return $this->getFilesForCalledClass('testRuleDoesNotApplyTo*');
    }

    public function getSuccessCases()
    {
        return array_map(function ($file) {
            return array($file);
        }, $this->getSuccessFiles());
    }

    public function getFailureCases()
    {
        return array_map(function ($file) {
            return array($file);
        }, $this->getFailureFiles());
    }

    /**
     * @dataProvider getSuccessCases
     */
    public function testRuleAppliesToSuccessFiles($file)
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethodNodeForTestFile($file));
    }

    /**
     * @dataProvider getFailureCases
     */
    public function testRuleDoesNotApplyToFailureFiles($file)
    {
        $rule = $this->getRule();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethodNodeForTestFile($file));
    }
}
