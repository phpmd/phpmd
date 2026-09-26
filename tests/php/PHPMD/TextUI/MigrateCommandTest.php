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
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(MigrateCommand::class)]
class MigrateCommandTest extends AbstractTestCase
{
    public function testWritesYamlNextToTheXmlFile(): void
    {
        $name = 'phpmd-migrate-' . uniqid();
        $source = self::createTempFileUri($name . '.xml.dist');
        copy(self::createFileUri('config/phpmd2.xml'), $source);
        $target = self::createTempFileUri($name . '.yml.dist');

        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => $source]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode, $tester->getDisplay());
        static::assertFileEquals(self::createFileUri('config/phpmd2.xml'), $source);
        static::assertStringContainsString("maximum: 300\n", (string) file_get_contents($target));
        static::assertStringContainsString('Renamed property minimum of rule NPathComplexity', $tester->getDisplay());
    }

    public function testWritesJson(): void
    {
        $target = self::createTempFileUri('phpmd-migrate-' . uniqid() . '.json');

        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute([
            'file' => self::createFileUri('config/phpmd2.xml'),
            '--format' => 'json',
            '--output' => $target,
        ]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode, $tester->getDisplay());
        $config = json_decode((string) file_get_contents($target), true);
        static::assertIsArray($config);
        static::assertSame('My rules', $config['name'] ?? null);
    }

    public function testRefusesToOverwriteWithoutForce(): void
    {
        $target = self::createTempFileUri();
        file_put_contents($target, 'keep');

        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => self::createFileUri('config/phpmd2.xml'), '--output' => $target]);

        static::assertSame(SymfonyCommand::FAILURE, $exitCode);
        static::assertStringEqualsFile($target, 'keep');

        $exitCode = $tester->execute([
            'file' => self::createFileUri('config/phpmd2.xml'),
            '--output' => $target,
            '--force' => true,
        ]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode);
        static::assertStringContainsString('name: \'My rules\'', (string) file_get_contents($target));
    }

    public function testMigratesYamlInPlaceWithBackup(): void
    {
        $source = self::createTempFileUri('phpmd-migrate-' . uniqid() . '.yml');
        copy(self::createFileUri('config/phpmd2.yml'), $source);
        $backup = self::createTempFileUri(basename($source) . '.bak');

        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => $source]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode, $tester->getDisplay());
        static::assertFileEquals(self::createFileUri('config/phpmd2.yml'), $backup);
        static::assertStringNotContainsString('reportLevel', (string) file_get_contents($source));
    }

    public function testLeavesCurrentConfigAlone(): void
    {
        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => self::createFileUri('config/phpmd3.yml')]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode);
        static::assertStringContainsString('up to date', $tester->getDisplay());
        static::assertFileDoesNotExist(self::createFileUri('config/phpmd3.yml.bak'));
    }

    public function testDryRunPrintsTheConfiguration(): void
    {
        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => self::createFileUri('config/phpmd2.xml'), '--dry-run' => true]);

        static::assertSame(SymfonyCommand::SUCCESS, $exitCode);
        static::assertStringContainsString("maximum: 300\n", $tester->getDisplay());
        static::assertFileDoesNotExist(self::createFileUri('config/phpmd2.yml.dist'));
    }

    public function testFailsWithoutConfigFile(): void
    {
        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => self::createFileUri('config/missing.xml')]);

        static::assertSame(SymfonyCommand::FAILURE, $exitCode);
        static::assertStringContainsString('Unable to read', $tester->getDisplay());
    }

    public function testRejectsUnsupportedFormat(): void
    {
        $tester = new CommandTester(new MigrateCommand());
        $exitCode = $tester->execute(['file' => self::createFileUri('config/phpmd2.xml'), '--format' => 'xml']);

        static::assertSame(SymfonyCommand::FAILURE, $exitCode);
        static::assertStringContainsString('The format must be one of: yml, json', $tester->getDisplay());
    }
}
