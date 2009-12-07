<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/pmd
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/PMD/RuleSetFactory.php';

/**
 * Test case for the rule set factory class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
 */
class PHP_PMD_RuleSetFactoryTest extends PHP_PMD_AbstractTest
{
    /**
     * Tests that the factory creates a rule-set instance for qualified filename.
     *
     * @return void
     */
    public function testCreateSingleRuleSetForQualifiedFileName()
    {
        $fileName = self::createFileUri('rulesets/set1.xml');
        $factory  = new PHP_PMD_RuleSetFactory();

        $ruleSets = $factory->createRuleSets($fileName);
        $this->assertType('array', $ruleSets);
        $this->assertSame(1, count($ruleSets));

        $ruleSet = $ruleSets[0];
        $this->assertType('PHP_PMD_RuleSet', $ruleSet);
        $this->assertSame('First Test RuleSet', $ruleSet->getName());
        $this->assertSame('First description...', $ruleSet->getDescription());
    }

    /**
     * Tests that the factory creates two rule-sets for qualfieid filenames.
     *
     * @return void
     */
    public function testCreateTwoRuleSetsForQualifiedFileNames()
    {
        $fileName1 = self::createFileUri('rulesets/set1.xml');
        $fileName2 = self::createFileUri('rulesets/set2.xml');
        $fileNames = $fileName1 . ',' . $fileName2;
        
        $factory  = new PHP_PMD_RuleSetFactory();

        $ruleSets = $factory->createRuleSets($fileNames);
        $this->assertType('array', $ruleSets);
        $this->assertSame(2, count($ruleSets));

        $ruleSet1 = $ruleSets[0];
        $this->assertType('PHP_PMD_RuleSet', $ruleSet1);
        $this->assertSame('First Test RuleSet', $ruleSet1->getName());
        $this->assertSame('First description...', $ruleSet1->getDescription());

        $ruleSet2 = $ruleSets[1];
        $this->assertType('PHP_PMD_RuleSet', $ruleSet2);
        $this->assertSame('Second Test RuleSet', $ruleSet2->getName());
        $this->assertSame('Second description...', $ruleSet2->getDescription());
    }

    /**
     * Tests that the factory detects rule set file in the current working
     * directory.
     *
     * @return void
     */
    public function testCreateRuleSetForCustomFileName()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('rulesets/set1.xml');

        $this->assertType('array', $ruleSets);
        $this->assertSame(1, count($ruleSets));
        $this->assertSame('First Test RuleSet', $ruleSets[0]->getName());
    }

    /**
     * Tests that the factory handles a referenced rule@ref attribute correct.
     *
     * @return void
     */
    public function testCreateRuleSetWithRuleSetReferenceNodes()
    {
        self::changeWorkingDirectory();
        $fileName = self::createFileUri('rulesets/refset1.xml');

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets($fileName);

        $this->assertType('array', $ruleSets);
        $this->assertSame(1, count($ruleSets));
        $this->assertSame('First Test RuleSet', $ruleSets[0]->getName());

        $count = 0;
        foreach ($ruleSets[0]->getRules() as $rule) {
            $this->assertType('PHP_PMD_AbstractRule', $rule);
            ++$count;
        }
        $this->assertSame(4, $count);
    }

    /**
     * Tests that the factory handles a referenced rule@ref attribute correct.
     *
     * @return void
     */
    public function testCreateRuleSetWithRuleReferenceNodes()
    {
        self::changeWorkingDirectory();
        $fileName = self::createFileUri('rulesets/refset2.xml');

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets($fileName);

        $this->assertType('array', $ruleSets);
        $this->assertSame(1, count($ruleSets));
        $this->assertSame('Second Test RuleSet', $ruleSets[0]->getName());

        $expectedRules = array(
            'RuleTwoInFirstRuleSet'   =>  true,
            'RuleOneInSecondRuleSet'  =>  true
        );
        foreach ($ruleSets[0]->getRules() as $rule) {
            $this->assertType('PHP_PMD_AbstractRule', $rule);
            $this->assertArrayHasKey($rule->getName(), $expectedRules);

            unset($expectedRules[$rule->getName()]);
        }
        $this->assertSame(0, count($expectedRules));
    }

    /**
     * Tests that the rule-set factory applies a set priority filter correct.
     *
     * @return void
     */
    public function testCreateRuleSetWithSpecifiedPriority()
    {
        self::changeWorkingDirectory();

        $factory = new PHP_PMD_RuleSetFactory();
        $factory->setMinimumPriority(2);

        $ruleSet = $factory->createSingleRuleSet('set1');
        $this->assertType('PHP_PMD_RuleSet', $ruleSet);

        $count = 0;
        foreach ($ruleSet as $rule) {
            ++$count;
        }
        $this->assertSame(1, $count);
    }

    /**
     * Tests that you can overwrite settings like description, priority etc. for
     * included rules.
     *
     * @return void
     */
    public function testCreateRuleSetWithRuleReferenceThatOverwritesSettings()
    {
        self::changeWorkingDirectory();

        $factory  = new PHP_PMD_RuleSetFactory();
        $ruleSets = $factory->createRuleSets('refset3');

        $this->assertType('array', $ruleSets);
        $this->assertSame(1, count($ruleSets));
        $this->assertSame('Third Test RuleSet', $ruleSets[0]->getName());

        $rule = $ruleSets[0]->getRules()->current();
        $this->assertType('PHP_PMD_AbstractRule', $rule);
        $this->assertSame(-42, $rule->getPriority());
        $this->assertSame('description 42', $rule->getDescription());
        $this->assertSame(42, $rule->getIntProperty('foo'));

        $examples = $rule->getExamples();
        $this->assertSame(1, count($examples));
        $this->assertSame('foreach ($foo as $bar) { echo $bar; }', $examples[0]);
    }

    /**
     * Tests that the factory throws the expected exception for an invalid ruleset
     * identifier.
     *
     * @return void
     */
    public function testCreateRuleSetsThrowsExceptionForInvalidIdentifier()
    {
        $factory = new PHP_PMD_RuleSetFactory();

        $this->setExpectedException(
            'PHP_PMD_RuleSetNotFoundException',
            'Cannot find specified rule-set "foo-bar-ruleset-23".'
        );

        $factory->createRuleSets('foo-bar-ruleset-23');
    }

    /**
     * Tests that the factory throws an exception when the source code filename
     * for the configured rule does not exist.
     *
     * @return void
     */
    public function testCreateRuleSetThrowsExceptionWhenClassFileNotInIncludePath()
    {
        $fileName = self::createFileUri('rulesets/set-class-file-not-found.xml');
        $factory  = new PHP_PMD_RuleSetFactory();

        $this->setExpectedException(
            'PHP_PMD_RuleClassFileNotFoundException',
            'Cannot load source file for class: rules_ClassFileNotFoundRule'
        );

        $factory->createRuleSets($fileName);
    }

    /**
     * Tests that the factory throws the expected exception when a rule class
     * cannot be found.
     *
     * @return void
     */
    public function testCreateRuleSetThrowsExceptionWhenFileNotContainsClass()
    {
        $fileName = self::createFileUri('rulesets/set-class-not-found.xml');
        $factory  = new PHP_PMD_RuleSetFactory();

        $this->setExpectedException(
            'PHP_PMD_RuleClassNotFoundException',
            'Cannot find rule class: rules_ClassNotFoundRule'
        );

        $factory->createRuleSets($fileName);
    }
}