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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTScopeStatement;
use PHPMD\Node\ClassNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Rule\CleanCode\ElseExpression;
use PHPMD\Stubs\RuleStub;

/**
 * Test case for the {@link \PHPMD\RuleSet} class.
 *
 * @covers \PHPMD\RuleSet
 */
class RuleSetTest extends AbstractTestCase
{
    /**
     * testGetRuleByNameReturnsNullWhenNoMatchingRuleExists
     *
     * @return void
     * @expectedException \PHPMD\RuleByNameNotFoundException
     */
    public function testGetRuleByNameThrowsExceptionWhenNoMatchingRuleExists()
    {
        $ruleSet = $this->createRuleSetFixture();
        $this->assertNull($ruleSet->getRuleByName(__FUNCTION__));
    }

    /**
     * testGetRuleByNameReturnsMatchingRuleInstance
     *
     * @return void
     */
    public function testGetRuleByNameReturnsMatchingRuleInstance()
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__, __CLASS__, __METHOD__);
        $rule = $ruleSet->getRuleByName(__CLASS__);

        $this->assertEquals(__CLASS__, $rule->getName());
    }

    /**
     * testApplyNotInvokesRuleWhenSuppressAnnotationExists
     *
     * @return void
     */
    public function testApplyNotInvokesRuleWhenSuppressAnnotationExists()
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportWithNoViolation());
        $ruleSet->apply($this->getClass());

        $this->assertNull($ruleSet->getRuleByName(__FUNCTION__)->node);
    }

    /**
     * testApplyInvokesRuleWhenStrictModeIsSet
     *
     * @return void
     */
    public function testApplyInvokesRuleWhenStrictModeIsSet()
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportWithNoViolation());
        $ruleSet->setStrict();

        $class = $this->getClass();
        $ruleSet->apply($class);

        $this->assertSame($class, $ruleSet->getRuleByName(__FUNCTION__)->node);
    }

    public function testDescriptionCanBeChanged()
    {
        $ruleSet = new RuleSet();

        $this->assertSame('', $ruleSet->getDescription());

        $ruleSet->setDescription('foobar');

        $this->assertSame('foobar', $ruleSet->getDescription());
    }

    public function testStrictnessCanBeEnabled()
    {
        $ruleSet = new RuleSet();

        $this->assertFalse($ruleSet->isStrict());

        $ruleSet->setStrict();

        $this->assertTrue($ruleSet->isStrict());
    }

    public function testReport()
    {
        $ruleSet = new RuleSet();
        $ruleSet->setReport(new Report());
        $else = new ElseExpression();
        $ruleSet->addRule($else);
        $iteration = [];

        foreach ($ruleSet as $rule) {
            $iteration[] = $rule;
        }

        $this->assertSame([$else], $iteration);
        $ruleSet->apply(new ClassNode(new ASTClass('FooBar')));

        $this->assertCount(0, $ruleSet->getReport()->getRuleViolations());

        $function = new ASTFunction('fooBar');
        $statement = new ASTIfStatement('if');
        $statement->addChild(new ASTExpression());
        $statement->addChild(new ASTScopeStatement());
        $statement->addChild(new ASTScopeStatement());
        $function->addChild($statement);
        $ruleSet->apply(new FunctionNode($function));

        $this->assertCount(1, $ruleSet->getReport()->getRuleViolations());
    }

    /**
     * Creates a rule set instance with a variable amount of appended rule
     * objects.
     *
     * @return RuleSet
     */
    private function createRuleSetFixture()
    {
        $ruleSet = new RuleSet();

        foreach (func_get_args() as $name) {
            $ruleSet->addRule(new RuleStub($name));
        }

        return $ruleSet;
    }
}
