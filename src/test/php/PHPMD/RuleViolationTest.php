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

/**
 * Test case for the {@link \PHPMD\RuleViolation} class.
 *
 * @since     0.2.5
 *
 * @covers \PHPMD\RuleViolation
 */
class RuleViolationTest extends AbstractTest
{
    /**
     * testConstructorExtractsClassNameFromGivenType
     *
     * @return void
     */
    public function testConstructorExtractsClassNameFromGivenType()
    {
        $rule = $this->getRuleMock();

        $node = $this->getClassMock();
        $node->expects($this->once())
            ->method('getName');

        new RuleViolation($rule, $node, 'foo');
    }

    /**
     * testConstructorExtractsClassNameFromGivenMethod
     *
     * @return void
     */
    public function testConstructorExtractsClassNameFromGivenMethod()
    {
        $rule = $this->getRuleMock();

        $node = $this->getMethodMock();
        $node->expects($this->once())
            ->method('getParentName');

        new RuleViolation($rule, $node, 'foo');
    }

    /**
     * testConstructorExtractsMethodNameFromGivenMethod
     *
     * @return void
     */
    public function testConstructorExtractsMethodNameFromGivenMethod()
    {
        $rule = $this->getRuleMock();

        $node = $this->getMethodMock();
        $node->expects($this->once())
            ->method('getName');

        new RuleViolation($rule, $node, 'foo');
    }
}
