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
use PHPMD\Node\AbstractNode;
use PHPMD\Node\ClassNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Rule\CleanCode\ElseExpression;
use PHPMD\Stubs\RuleStub;
use Throwable;

/**
 * Test case for the {@link \PHPMD\RuleSet} class.
 *
 * @covers \PHPMD\RuleSet
 */
final class RuleSetTest extends AbstractTestCase
{
    /**
     * testGetRuleByNameReturnsNullWhenNoMatchingRuleExists
     * @throws Throwable
     */
    public function testGetRuleByNameThrowsExceptionWhenNoMatchingRuleExists(): void
    {
        self::expectException(RuleByNameNotFoundException::class);

        $ruleSet = $this->createRuleSetFixture();
        self::assertNull($ruleSet->getRuleByName(__FUNCTION__));
    }

    /**
     * testGetRuleByNameReturnsMatchingRuleInstance
     * @throws Throwable
     */
    public function testGetRuleByNameReturnsMatchingRuleInstance(): void
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__, __CLASS__, __METHOD__);
        $rule = $ruleSet->getRuleByName(__CLASS__);

        self::assertEquals(__CLASS__, $rule->getName());
    }

    /**
     * testApplyNotInvokesRuleWhenSuppressAnnotationExists
     * @throws Throwable
     */
    public function testApplyNotInvokesRuleWhenSuppressAnnotationExists(): void
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportWithNoViolation());
        $ruleSet->apply($this->getClass());
        $rule = $ruleSet->getRuleByName(__FUNCTION__);

        self::assertInstanceOf(RuleStub::class, $rule);
        self::assertNull($rule->node);
    }

    /**
     * testApplyInvokesRuleWhenStrictModeIsSet
     * @throws Throwable
     */
    public function testApplyInvokesRuleWhenStrictModeIsSet(): void
    {
        $ruleSet = $this->createRuleSetFixture(__FUNCTION__);
        $ruleSet->setReport($this->getReportWithNoViolation());
        $ruleSet->setStrict();

        $class = $this->getClass();
        $ruleSet->apply($class);
        $rule = $ruleSet->getRuleByName(__FUNCTION__);

        self::assertInstanceOf(RuleStub::class, $rule);
        self::assertSame($class, $rule->node);
    }

    /**
     * @throws Throwable
     */
    public function testDescriptionCanBeChanged(): void
    {
        $ruleSet = new RuleSet();

        self::assertSame('', $ruleSet->getDescription());

        $ruleSet->setDescription('foobar');

        self::assertSame('foobar', $ruleSet->getDescription());
    }

    /**
     * @throws Throwable
     */
    public function testStrictnessCanBeEnabled(): void
    {
        $ruleSet = new RuleSet();

        self::assertFalse($ruleSet->isStrict());

        $ruleSet->setStrict();

        self::assertTrue($ruleSet->isStrict());
    }

    /**
     * @throws Throwable
     */
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

        self::assertSame([$else], $iteration);
        // With a node ElseExpression is not aware (since its implements only MethodAware and FunctionAware)
        $ruleSet->apply(new ClassNode(new ASTClass('FooBar')));

        self::assertCount(0, $ruleSet->getReport()->getRuleViolations());

        // With a node not registered at all
        $ruleSet->apply(new class (new ASTClass('FooBar')) extends AbstractNode {
            public function hasSuppressWarningsAnnotationFor(Rule $rule): bool
            {
                return false;
            }

            public function getFullQualifiedName(): string
            {
                return '';
            }

            public function getParentName(): string
            {
                return '';
            }

            public function getNamespaceName(): string
            {
                return '';
            }
        });

        self::assertCount(0, $ruleSet->getReport()->getRuleViolations());

        $function = new ASTFunction('fooBar');
        $statement = new ASTIfStatement('if');
        $statement->addChild(new ASTExpression());
        $statement->addChild(new ASTScopeStatement());
        $statement->addChild(new ASTScopeStatement());
        $function->addChild($statement);

        // With a node ElseExpression is aware of (thanks to FunctionAware)
        $ruleSet->apply(new FunctionNode($function));

        self::assertCount(1, $ruleSet->getReport()->getRuleViolations());
    }

    /**
     * Creates a rule set instance with a variable amount of appended rule
     * objects.
     *
     * @throws Throwable
     */
    private function createRuleSetFixture(): RuleSet
    {
        $ruleSet = new RuleSet();

        foreach (func_get_args() as $name) {
            self::assertIsString($name);
            $ruleSet->addRule(new RuleStub($name));
        }

        return $ruleSet;
    }
}
