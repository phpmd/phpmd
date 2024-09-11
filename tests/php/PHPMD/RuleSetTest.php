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
     */
    public function testGetRuleByNameThrowsExceptionWhenNoMatchingRuleExists(): void
    {
        self::expectException(RuleByNameNotFoundException::class);

        $ruleSet = $this->createRuleSetFixture();
        static::assertNull($ruleSet->getRuleByName(__FUNCTION__));
    }

    /**
     * testGetRuleByNameReturnsMatchingRuleInstance
     */
    public function testGetRuleByNameReturnsMatchingRuleInstance(): void
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__, __CLASS__, __METHOD__);
        $rule = $ruleSet->getRuleByName(__CLASS__);

        static::assertEquals(__CLASS__, $rule->getName());
    }

    /**
     * testApplyNotInvokesRuleWhenSuppressAnnotationExists
     */
    public function testApplyNotInvokesRuleWhenSuppressAnnotationExists(): void
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportWithNoViolation());
        $ruleSet->apply($this->getClass());
        $rule = $ruleSet->getRuleByName(__FUNCTION__);

        static::assertInstanceOf(RuleStub::class, $rule);
        static::assertNull($rule->node);
    }

    /**
     * testApplyInvokesRuleWhenStrictModeIsSet
     */
    public function testApplyInvokesRuleWhenStrictModeIsSet(): void
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportWithNoViolation());
        $ruleSet->setStrict();

        $class = $this->getClass();
        $ruleSet->apply($class);
        $rule = $ruleSet->getRuleByName(__FUNCTION__);

        static::assertInstanceOf(RuleStub::class, $rule);
        static::assertSame($class, $rule->node);
    }

    public function testDescriptionCanBeChanged(): void
    {
        $ruleSet = new RuleSet();

        static::assertSame('', $ruleSet->getDescription());

        $ruleSet->setDescription('foobar');

        static::assertSame('foobar', $ruleSet->getDescription());
    }

    public function testStrictnessCanBeEnabled(): void
    {
        $ruleSet = new RuleSet();

        static::assertFalse($ruleSet->isStrict());

        $ruleSet->setStrict();

        static::assertTrue($ruleSet->isStrict());
    }

    public function testReport(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->setReport(new Report());
        $else = new ElseExpression();
        $ruleSet->addRule($else);
        $iteration = [];

        foreach ($ruleSet as $rule) {
            $iteration[] = $rule;
        }

        static::assertSame([$else], $iteration);
        $ruleSet->apply(new ClassNode(new ASTClass('FooBar')));

        static::assertCount(0, $ruleSet->getReport()->getRuleViolations());

        $function = new ASTFunction('fooBar');
        $statement = new ASTIfStatement('if');
        $statement->addChild(new ASTExpression());
        $statement->addChild(new ASTScopeStatement());
        $statement->addChild(new ASTScopeStatement());
        $function->addChild($statement);
        $ruleSet->apply(new FunctionNode($function));

        static::assertCount(1, $ruleSet->getReport()->getRuleViolations());
    }

    /**
     * Creates a rule set instance with a variable amount of appended rule
     * objects.
     */
    private function createRuleSetFixture(): RuleSet
    {
        $ruleSet = new RuleSet();

        foreach (func_get_args() as $name) {
            static::assertIsString($name);
            $ruleSet->addRule(new RuleStub($name));
        }

        return $ruleSet;
    }
}
