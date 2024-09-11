<?php

/**
 * This file is part of PHP Mess Detector.
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\Rule\Controversial;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTestCase;
use PHPMD\Node\ClassNode;

/**
 * Test case for the camel case class name rule.
 * @covers \PHPMD\Rule\Controversial\CamelCaseClassName
 */
class CamelCaseClassNameTest extends AbstractTestCase
{
    public function testRuleDoesNotApplyForValidClassName(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'false');
        $rule->apply($this->createClassNode('ValidClass'));
    }

    public function testRuleDoesNotApplyForValidClassNameWithUppercaseAbbreviation(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'false');
        $rule->apply($this->createClassNode('ValidURLClass'));
    }

    public function testRuleDoesApplyForClassNameWithUppercaseAbbreviation(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->createClassNode('ValidURLClass'));
    }

    public function testRuleDoesNotApplyForClassNameWithCamelcaseAbbreviation(): void
    {
        $report = $this->getReportWithNoViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->createClassNode('ValidUrlClass'));
    }

    public function testRuleAppliesForClassNameWithLowerCase(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'false');
        $rule->apply($this->createClassNode('invalidClass'));
    }

    public function testRuleAppliesForClassNameWithLowerCaseAndCamelcaseAbbreviation(): void
    {
        $report = $this->getReportWithOneViolation();

        $rule = new CamelCaseClassName();
        $rule->setReport($report);
        $rule->addProperty('camelcase-abbreviations', 'true');
        $rule->apply($this->createClassNode('invalidClass'));
    }

    private function createClassNode(string $className): ClassNode
    {
        $astClass = new ASTClass($className);
        $astClass->setNamespace(new ASTNamespace('phpmd'));

        return new ClassNode($astClass);
    }
}
