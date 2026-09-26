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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\TextUI;

use PHPMD\AbstractTestCase;
use PHPMD\RuleSetFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(InitCommand::class)]
class InitCommandTest extends AbstractTestCase
{
    public function testWritesSuggestedSettingsWithoutInteraction(): void
    {
        $target = self::createTempFileUri('phpmd-init-' . uniqid() . '.yml');

        $tester = new CommandTester(new InitCommand());
        $exitCode = $tester->execute(['--output' => $target], ['interactive' => false]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode, $tester->getDisplay());
        $config = Yaml::parseFile($target);
        static::assertIsArray($config);
        // The working directory is the project root, which has a src/ directory
        static::assertSame(['src'], $config['paths'] ?? null);
        static::assertTrue($config['cache'] ?? null);
        static::assertCount(count((new RuleSetFactory())->listAvailableRuleSets()), (array) $config['rules']);

        // The file can be used as a rule set
        static::assertNotEmpty((new RuleSetFactory())->createSingleRuleSet($target)->getRules());
    }

    public function testUsesTheAnswers(): void
    {
        $target = self::createTempFileUri('phpmd-init-' . uniqid() . '.yml');

        $tester = new CommandTester(new InitCommand());
        $tester->setInputs(['lib, app', 'lib/generated/*', 'no', 'codesize,naming']);
        $exitCode = $tester->execute(['--output' => $target]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode, $tester->getDisplay());
        $config = Yaml::parseFile($target);
        static::assertIsArray($config);
        static::assertSame(['lib', 'app'], $config['paths'] ?? null);
        static::assertSame(['lib/generated/*'], $config['exclude-pattern'] ?? null);
        static::assertArrayNotHasKey('cache', $config);
        static::assertSame(
            [['ref' => 'rulesets/codesize.xml'], ['ref' => 'rulesets/naming.xml']],
            $config['rules'] ?? null
        );
    }

    public function testRefusesToOverwriteWithoutForce(): void
    {
        $target = self::createTempFileUri('phpmd-init-' . uniqid() . '.yml');
        file_put_contents($target, 'keep');

        $tester = new CommandTester(new InitCommand());
        $exitCode = $tester->execute(['--output' => $target], ['interactive' => false]);

        static::assertSame(SymfonyCommand::FAILURE, $exitCode);
        static::assertStringEqualsFile($target, 'keep');

        $exitCode = $tester->execute(['--output' => $target, '--force' => true], ['interactive' => false]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode);
        static::assertStringContainsString('rules:', (string) file_get_contents($target));
    }
}
