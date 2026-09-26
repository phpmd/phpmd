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

namespace PHPMD\Config;

use PHPMD\AbstractTestCase;
use PHPMD\Exception\RuntimeException;
use PHPMD\RuleSetFactory;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConfigMigrator::class)]
#[CoversClass(ConfigLoader::class)]
#[CoversClass(XmlConfigConverter::class)]
#[CoversClass(ConfigDumper::class)]
#[CoversClass(ConfigFormat::class)]
class ConfigMigratorTest extends AbstractTestCase
{
    public function testConvertsXmlAndRenamesThresholds(): void
    {
        $migration = (new ConfigMigrator())->migrate(self::createFileUri('config/phpmd2.xml'));

        static::assertSame(
            [
                'name' => 'My rules',
                'description' => "Custom rules\nfor my project",
                'exclude-pattern' => ['*/tests/*', '*/vendor/*'],
                'cache' => true,
                'rules' => [
                    [
                        'ref' => 'rulesets/codesize.xml',
                        'exclude' => ['NPathComplexity', 'TooManyMethods'],
                    ],
                    [
                        'ref' => 'rulesets/codesize.xml/NPathComplexity',
                        'properties' => ['maximum' => 300],
                    ],
                    [
                        'ref' => 'rulesets/codesize.xml/TooManyMethods',
                        'priority' => 2,
                        'properties' => ['maximum' => 30, 'ignorepattern' => '(^(set|get|is))i'],
                    ],
                    [
                        'ref' => 'rulesets/naming.xml/ShortVariable',
                        'properties' => ['minimum' => 2],
                    ],
                    [
                        'name' => 'LongMethodCustom',
                        'class' => 'PHPMD\Rule\Design\ExcessiveMethodLength',
                        'message' => 'Too long',
                        'properties' => ['maximum' => 80],
                    ],
                ],
            ],
            $migration->config
        );
        static::assertSame(
            [
                'Renamed property minimum of rule NPathComplexity to maximum.',
                'Renamed property maxmethods of rule TooManyMethods to maximum.',
                'Renamed rule class PHP_PMD_Rule_Design_LongMethod to PHPMD\Rule\Design\ExcessiveMethodLength.',
                'Renamed property minimum of rule ExcessiveMethodLength to maximum.',
            ],
            $migration->changes
        );
        static::assertSame([], $migration->notes);
    }

    public function testPreserveBehaviorLowersOnlyInclusiveThresholds(): void
    {
        $migrator = new ConfigMigrator();
        $migrator->preserveBehavior();
        $migration = $migrator->migrate(self::createFileUri('config/phpmd2.xml'));

        /** @var list<array{properties?: array<string, mixed>}> $rules */
        $rules = $migration->config['rules'];
        // NPathComplexity reported at the threshold in PHPMD 2
        static::assertSame(299, $rules[1]['properties']['maximum'] ?? null);
        // TooManyMethods already only reported values above the threshold
        static::assertSame(30, $rules[2]['properties']['maximum'] ?? null);
        // ShortVariable checks a lower bound and is left alone
        static::assertSame(2, $rules[3]['properties']['minimum'] ?? null);
        static::assertSame(79, $rules[4]['properties']['maximum'] ?? null);
    }

    public function testMigratesArrayConfig(): void
    {
        $migrator = new ConfigMigrator();
        $migrator->preserveBehavior();
        $migration = $migrator->migrate(self::createFileUri('config/phpmd2.yml'));

        static::assertSame(
            [
                'rules' => [
                    ['ref' => 'CyclomaticComplexity', 'properties' => ['maximum' => 15]],
                    ['ref' => 'custom/other.xml'],
                    ['ref' => 'rulesets/design.xml'],
                    ['name' => 'DepthOfInheritance', 'properties' => ['maximum' => 5]],
                    ['ref' => 'rulesets/design.xml/CouplingBetweenObjects', 'properties' => ['maximum' => 12]],
                ],
            ],
            $migration->config
        );
        static::assertContains(
            'Removed property reportLevel of rule CyclomaticComplexity, maximum is already set.',
            $migration->changes
        );
        static::assertSame(
            ['The referenced rule set custom/other.xml was not migrated, run the migration on it as well to do so.'],
            $migration->notes
        );
    }

    public function testCurrentConfigIsUnchanged(): void
    {
        $migration = (new ConfigMigrator())->migrate(self::createFileUri('config/phpmd3.yml'));

        static::assertSame([], $migration->changes);
        static::assertSame([], $migration->notes);
    }

    public function testMigratedConfigLoads(): void
    {
        $migration = (new ConfigMigrator())->migrate(self::createFileUri('config/phpmd2.xml'));
        $fileName = self::createTempFileUri('phpmd-migrated-' . uniqid() . '.yml');
        file_put_contents($fileName, (new ConfigDumper())->dump($migration->config, ConfigFormat::Yaml));

        $factory = new RuleSetFactory();
        $ruleSet = $factory->createSingleRuleSet($fileName);

        static::assertSame('My rules', $ruleSet->getName());
        static::assertSame(300, $ruleSet->getRuleByName('NPathComplexity')->getIntProperty('maximum'));
        static::assertSame(80, $ruleSet->getRuleByName('LongMethodCustom')->getIntProperty('maximum'));
        static::assertSame(['*/tests/*', '*/vendor/*'], $factory->getExcludePatterns([$fileName]));
        static::assertTrue($factory->isCacheEnabled([$fileName]));
    }

    public function testMissingFileThrows(): void
    {
        $this->expectException(RuntimeException::class);

        (new ConfigMigrator())->migrate(self::createFileUri('config/missing.xml'));
    }
}
