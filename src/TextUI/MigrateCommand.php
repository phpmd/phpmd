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

use PHPMD\Config\ConfigDumper;
use PHPMD\Config\ConfigFileFinder;
use PHPMD\Config\ConfigFormat;
use PHPMD\Config\ConfigMigration;
use PHPMD\Config\ConfigMigrator;
use PHPMD\Exception\Exception as PhpmdException;
use PHPMD\Exception\RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException as InvalidSymfonyArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Migrates a configuration file written for PHPMD 2 to the current format.
 */
#[AsCommand(
    name: 'migrate',
    description: 'Migrates a configuration file to the current format, converting it to YAML by default',
)]
final class MigrateCommand extends SymfonyCommand
{
    /** Formats the migrated configuration can be written in. */
    private const FORMATS = [ConfigFormat::Yaml, ConfigFormat::Json];

    /**
     * @throws InvalidSymfonyArgumentException
     */
    protected function configure(): void
    {
        $this->addArgument(
            'file',
            InputArgument::OPTIONAL,
            'The configuration file to migrate [default: the auto-detected configuration file]'
        );
        $this->addOption(
            'format',
            null,
            InputOption::VALUE_REQUIRED,
            'The format to write the migrated configuration in. One of: ' . implode(', ', $this->formatNames()),
            ConfigFormat::Yaml->value,
            $this->formatNames()
        );
        $this->addOption(
            'output',
            null,
            InputOption::VALUE_REQUIRED,
            'Where to write the migrated configuration [default: next to the migrated file, '
            . 'with the extension of the chosen format]'
        );
        $this->addOption(
            'preserve-behavior',
            null,
            InputOption::VALUE_NONE,
            'Lower the configured thresholds of rules that reported values equal to the threshold in PHPMD 2 '
            . 'by one, so the same values are still reported'
        );
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Print the migrated configuration instead of writing it'
        );
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite the output file if it already exists');
        $this->setHelp(
            <<<'EOD'
                The <info>%command.name%</info> command upgrades a PHPMD 2 configuration file:

                  * renamed rule classes are replaced by their current names
                  * threshold properties like <comment>minimum</comment>, <comment>reportLevel</comment> and
                    <comment>maxmethods</comment> are renamed to <comment>maximum</comment>
                  * the file is converted to a recommended format

                The original file is kept. When the migrated file replaces it in place, a backup
                with the <comment>.bak</comment> extension is written first.

                  <info>%command.full_name% phpmd.xml</info>
                EOD
        );
    }

    /**
     * @throws InvalidSymfonyArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        try {
            return $this->migrate($input, $style);
        } catch (PhpmdException $exception) {
            $style->getErrorStyle()->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @throws PhpmdException
     * @throws InvalidSymfonyArgumentException
     */
    private function migrate(InputInterface $input, SymfonyStyle $style): int
    {
        $source = $this->resolveSource($input);
        if ($source === null) {
            $style->getErrorStyle()->error('No configuration file found, please provide a filename.');

            return self::FAILURE;
        }

        $format = $this->resolveFormat($input);

        $migrator = new ConfigMigrator();
        if ($input->getOption('preserve-behavior')) {
            $migrator->preserveBehavior();
        }
        $migration = $migrator->migrate($source);
        $content = (new ConfigDumper())->dump($migration->config, $format);

        if ($input->getOption('dry-run')) {
            $style->write($content, false, OutputInterface::OUTPUT_RAW);
            $this->printReport($style->getErrorStyle(), $migration);

            return self::SUCCESS;
        }

        $target = $this->getStringOption($input, 'output') ?? $this->deriveTarget($source, $format);
        if (!$this->isSameFile($source, $target)) {
            return $this->writeNewFile($input, $style, $migration, $content, [$source, $target]);
        }

        if ($migration->changes === [] && $migration->notes === []) {
            $style->success("{$source} is already up to date.");

            return self::SUCCESS;
        }

        $this->write($source . '.bak', (string) file_get_contents($source));
        $style->writeln("Backed up {$source} to {$source}.bak");
        $this->write($source, $content);
        $this->printReport($style, $migration);
        $style->success("Wrote the migrated configuration to {$source}.");

        return self::SUCCESS;
    }

    /**
     * @param array{string, string} $files The migrated file and the file to write.
     * @throws PhpmdException
     * @throws InvalidSymfonyArgumentException
     */
    private function writeNewFile(
        InputInterface $input,
        SymfonyStyle $style,
        ConfigMigration $migration,
        string $content,
        array $files,
    ): int {
        [$source, $target] = $files;
        if (file_exists($target) && !$input->getOption('force')) {
            $style->getErrorStyle()->error("{$target} already exists, use --force to overwrite it.");

            return self::FAILURE;
        }

        $this->write($target, $content);
        $this->printReport($style, $migration);
        $style->success("Wrote the migrated configuration to {$target}.");
        $style->writeln("Review {$target}, then remove {$source} and update any --ruleset option pointing to it.");

        return self::SUCCESS;
    }

    /**
     * @throws InvalidSymfonyArgumentException
     */
    private function resolveSource(InputInterface $input): ?string
    {
        $file = $input->getArgument('file');

        return is_string($file) && $file !== '' ? $file : (new ConfigFileFinder())->find();
    }

    /**
     * @throws RuntimeException
     * @throws InvalidSymfonyArgumentException
     */
    private function resolveFormat(InputInterface $input): ConfigFormat
    {
        $format = ConfigFormat::tryFrom((string) $this->getStringOption($input, 'format'));
        if ($format === null || !in_array($format, self::FORMATS, true)) {
            throw new RuntimeException('The format must be one of: ' . implode(', ', $this->formatNames()) . '.');
        }

        return $format;
    }

    /**
     * @return list<string>
     */
    private function formatNames(): array
    {
        return array_map(static fn(ConfigFormat $format): string => $format->value, self::FORMATS);
    }

    /**
     * @throws InvalidSymfonyArgumentException
     */
    private function getStringOption(InputInterface $input, string $name): ?string
    {
        $value = $input->getOption($name);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Swaps the extension of the source for the one of the format, keeping a trailing ".dist".
     */
    private function deriveTarget(string $source, ConfigFormat $format): string
    {
        if (ConfigFormat::fromFileName($source) === $format) {
            return $source;
        }

        $count = 0;
        $target = preg_replace(
            '/\.(?:xml|ya?ml|json|php)((?:\.dist)?)$/i',
            '.' . $format->value . '$1',
            $source,
            1,
            $count
        );

        return $count > 0 && is_string($target) ? $target : $source . '.' . $format->value;
    }

    private function isSameFile(string $source, string $target): bool
    {
        return file_exists($target) && realpath($target) === realpath($source);
    }

    /**
     * @throws RuntimeException
     */
    private function write(string $fileName, string $content): void
    {
        if (file_put_contents($fileName, $content) === false) {
            throw new RuntimeException("Unable to write to '{$fileName}'.");
        }
    }

    private function printReport(SymfonyStyle $style, ConfigMigration $migration): void
    {
        if ($migration->changes !== []) {
            $style->section('Changes');
            $style->listing($migration->changes);
        }
        if ($migration->notes !== []) {
            $style->warning(['Please check these manually:', ...$migration->notes]);
        }
    }
}
