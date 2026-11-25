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

namespace PHPMD\Integration;

use PHPMD\AbstractTestCase;
use PHPMD\PHPMD;
use PHPMD\Report;
use PHPMD\Rule\UnusedSuppression;
use PHPMD\RuleSetFactory;
use PHPMD\Suppressions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;

#[CoversClass(Suppressions::class)]
#[CoversClass(UnusedSuppression::class)]
class UnusedSuppressionIntegrationTest extends AbstractTestCase
{
    #[TestWith([1])]
    #[TestWith([2])]
    public function testSuppressedViolationsAreRemovedAndUnusedSuppressionsReported(int $threads): void
    {
        static::assertSame(
            [
                'Unused.php:9 UnusedSuppression #[SuppressWarnings(UnusedPrivateMethod::class)]',
                'Unused.php:12 UnusedSuppression #[SuppressWarnings(UnusedLocalVariable::class)]',
                'Unused.php:20 UnusedSuppression #[SuppressWarnings(CouplingBetweenObjects::class)]',
                'Unused.php:25 UnusedSuppression #[SuppressWarnings]',
                'Unused.php:30 UnusedSuppression #[SuppressWarnings(NoSuchRule::class)]',
                'Unused.php:45 UnusedSuppression #[SuppressWarnings(UnusedSuppression::class)]',
                'Used.php:31 UnusedFormalParameter $unusedParameter',
            ],
            $this->analyse('ruleset.yml', $threads, false)
        );
    }

    public function testStrictModeKeepsSuppressedViolationsAndReportsUnusedSuppressions(): void
    {
        static::assertSame(
            [
                'SelfReference.php:12 LongVariable $thisVariableNameIsFarTooLong',
                'Unused.php:9 UnusedSuppression #[SuppressWarnings(UnusedPrivateMethod::class)]',
                'Unused.php:12 UnusedSuppression #[SuppressWarnings(UnusedLocalVariable::class)]',
                'Unused.php:20 UnusedSuppression #[SuppressWarnings(CouplingBetweenObjects::class)]',
                'Unused.php:25 UnusedSuppression #[SuppressWarnings]',
                'Unused.php:30 UnusedSuppression #[SuppressWarnings(NoSuchRule::class)]',
                'Unused.php:39 UnusedSuppression #[SuppressWarnings(UnusedLocalVariable::class)]',
                'Unused.php:45 UnusedSuppression #[SuppressWarnings(UnusedSuppression::class)]',
                'Unused.php:52 UnusedSuppression #[SuppressWarnings(UnusedLocalVariable::class)]',
                'Used.php:10 LongVariable $suppressedByDocComment',
                'Used.php:15 UnusedLocalVariable $unused',
                'Used.php:21 UnusedLocalVariable $unused',
                'Used.php:27 UnusedLocalVariable $unused',
                'Used.php:31 UnusedFormalParameter $unusedParameter',
                'Used.php:33 UnusedLocalVariable $unused',
                'Used.php:43 UnusedLocalVariable $unused',
                'Used.php:46 UnusedPrivateMethod unused',
                'Used.php:56 UnusedLocalVariable $unused',
            ],
            $this->analyse('ruleset.yml', 1, true)
        );
    }

    public function testUnusedSuppressionsAreNotReportedWithoutTheRule(): void
    {
        static::assertSame(
            ['Used.php:31 UnusedFormalParameter $unusedParameter'],
            $this->analyse('without-rule.yml', 1, false)
        );
    }

    #[TestWith([1])]
    #[TestWith([2])]
    public function testAnonymousClassSuppressions(int $threads): void
    {
        static::assertSame(
            [
                'AnonymousClasses.php:48 LongVariable $unsuppressedVariableNameIsFarTooLong',
                'AnonymousClasses.php:53 UnusedSuppression #[SuppressWarnings(LongVariable::class)]',
                'AnonymousClasses.php:77 UnusedSuppression #[SuppressWarnings]',
                'AnonymousClasses.php:101 LongVariable $afterAssignedNameIsFarTooLong',
                'AnonymousClasses.php:107 LongVariable $nonDocVariableNameIsFarTooLong',
                'AnonymousClasses.php:135 UnusedSuppression #[SuppressWarnings(LongVariable::class)]',
            ],
            $this->analyse('ruleset.yml', $threads, false, 'anonymous')
        );
    }

    public function testAnonymousClassSuppressionsInStrictMode(): void
    {
        static::assertSame(
            [
                'AnonymousClasses.php:18 UnusedPrivateField $suppressedLongPrivateField',
                'AnonymousClasses.php:18 LongVariable $suppressedLongPrivateField',
                'AnonymousClasses.php:18 LongVariable $suppressedLongPrivateField',
                'AnonymousClasses.php:25 LongVariable $docblockVariableNameIsFarTooLong',
                'AnonymousClasses.php:33 LongVariable $attributeVariableNameIsFarTooLong',
                'AnonymousClasses.php:41 LongVariable $wildcardVariableNameIsFarTooLong',
                'AnonymousClasses.php:48 LongVariable $unsuppressedVariableNameIsFarTooLong',
                'AnonymousClasses.php:53 UnusedSuppression #[SuppressWarnings(LongVariable::class)]',
                'AnonymousClasses.php:59 UnusedSuppression #[SuppressWarnings(LongVariable::class)]',
                'AnonymousClasses.php:72 LongVariable $innerVariableNameIsFarTooLong',
                'AnonymousClasses.php:77 UnusedSuppression #[SuppressWarnings]',
                'AnonymousClasses.php:95 LongVariable $assignedVariableNameIsFarTooLong',
                'AnonymousClasses.php:101 LongVariable $afterAssignedNameIsFarTooLong',
                'AnonymousClasses.php:107 LongVariable $nonDocVariableNameIsFarTooLong',
                'AnonymousClasses.php:118 LongVariable $firstValueNameIsFarTooLong',
                'AnonymousClasses.php:135 UnusedSuppression #[SuppressWarnings(LongVariable::class)]',
                'AnonymousClasses.php:151 LongVariable $suppressedFieldInFunction',
                'AnonymousClasses.php:156 LongVariable $functionMethodNameIsFarTooLong',
            ],
            $this->analyse('ruleset.yml', 1, true, 'anonymous')
        );
    }

    /**
     * @return list<string>
     */
    private function analyse(string $ruleSetFile, int $threads, bool $strict, string $sourceDir = 'source'): array
    {
        $ruleSetFactory = new RuleSetFactory();
        if ($strict) {
            $ruleSetFactory->setStrict();
        }

        $ruleSetList = $ruleSetFactory->createRuleSets([self::createResourceUriForCalledClass($ruleSetFile)]);

        $report = new Report();
        $phpmd = new PHPMD();
        $phpmd->setThreads($threads);
        $phpmd->processFiles([self::createResourceUriForCalledClass($sourceDir)], [], [], $ruleSetList, $report);

        static::assertFalse($report->hasErrors());

        $violations = [];
        foreach ($report->getRuleViolations() as $violation) {
            $violations[] = sprintf(
                '%s:%d %s %s',
                basename((string) $violation->getFileName()),
                $violation->getBeginLine(),
                $violation->getRule()->getName(),
                $violation->getArgs()[0] ?? ''
            );
        }

        return $violations;
    }
}
