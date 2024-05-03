<?php
/**
 * This file is part of PHP Mess Detector.
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @link      http://phpmd.org/
 */

namespace PHPMD\Rule\Controversial;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTestCase;
use PHPMD\Node\ClassNode;

/**
 * Test case for the camel case class name rule.
 *
 * @covers \PHPMD\Rule\Controversial\CamelCaseClassName
 */
class CamelCaseClassNameTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testRuleDoesNotApplyForValidClassName()
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'false');
        $rule->apply($this->createClassNode('ValidClass'));
    }

    /**
     * @return void
     */
    public function testRuleDoesNotApplyForValidClassNameWithUppercaseAbbreviation()
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'false');
        $rule->apply($this->createClassNode('ValidURLClass'));
    }

    /**
     * @return void
     */
    public function testRuleDoesApplyForClassNameWithUppercaseAbbreviation()
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->createClassNode('ValidURLClass'));
    }

    /**
     * @return void
     */
    public function testRuleDoesNotApplyForClassNameWithCamelcaseAbbreviation()
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->createClassNode('ValidUrlClass'));
    }

    /**
     * @return void
     */
    public function testRuleAppliesForClassNameWithLowerCase()
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'false');
        $rule->apply($this->createClassNode('invalidClass'));
    }

    /**
     * @return void
     */
    public function testRuleAppliesForClassNameWithLowerCaseAndCamelcaseAbbreviation()
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->createClassNode('invalidClass'));
    }

    /**
     * @param string $className
     *
     * @return ClassNode
     */
    private function createClassNode($className)
    {
        $astClass = new ASTClass($className);
        $astClass->setNamespace(new ASTNamespace('phpmd'));

        return new ClassNode($astClass);
    }
}
