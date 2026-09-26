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
use PHPMD\Config\ConfigFormat;
use PHPMD\Exception\Exception as PhpmdException;
use PHPMD\RuleSetFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException as InvalidSymfonyArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Interactive wizard that writes a configuration file with suggested settings.
 */
#[AsCommand(
    name: 'init',
    description: 'Generates a configuration file with suggested settings through an interactive wizard',
)]
final class InitCommand extends SymfonyCommand
{
    /**
     * Directories that commonly hold a project's source code, suggested when they exist.
     *
     * @var list<string>
     */
    private const SOURCE_DIRECTORIES = ['src', 'lib', 'app'];

    /**
     * @throws InvalidSymfonyArgumentException
     */
    protected function configure(): void
    {
        $this->addOption(
            'output',
            null,
            InputOption::VALUE_REQUIRED,
            'Where to write the configuration file',
            'phpmd.yml'
        );
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite the configuration file if it exists');
        $this->setHelp(
            <<<'EOD'
                The <info>%command.name%</info> command asks a few questions about your project and writes
                a YAML configuration file that <info>phpmd analyze</info> picks up automatically.

                Run it with <comment>--no-interaction</comment> to accept all suggested settings.
                EOD
        );
    }

    /**
     * @throws InvalidSymfonyArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $target = $input->getOption('output');
        $target = is_string($target) && $target !== '' ? $target : 'phpmd.yml';
        if (file_exists($target) && !$input->getOption('force')) {
            $overwrite = $input->isInteractive() && $style->confirm("{$target} already exists, overwrite it?", false);
            if (!$overwrite) {
                $style->getErrorStyle()->error("{$target} already exists, use --force to overwrite it.");

                return self::FAILURE;
            }
        }

        $style->title('PHPMD configuration');
        $config = $this->askConfig($style);

        try {
            $content = (new ConfigDumper())->dump($config, ConfigFormat::fromFileName($target));
        } catch (PhpmdException $exception) {
            $style->getErrorStyle()->error($exception->getMessage());

            return self::FAILURE;
        }
        if (file_put_contents($target, $content) === false) {
            $style->getErrorStyle()->error("Unable to write to '{$target}'.");

            return self::FAILURE;
        }

        $style->success("Wrote the configuration to {$target}.");
        $style->listing([
            'Run <info>phpmd analyze</info> to check your code.',
            'Run <info>phpmd analyze --generate-baseline</info> to accept the current violations '
            . 'and only report new ones.',
            ...($config['cache'] ?? false)
                ? ['Add <comment>.phpmd.result-cache.php</comment> to your .gitignore.']
                : [],
        ]);

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function askConfig(SymfonyStyle $style): array
    {
        $cwd = getcwd() ?: '.';
        $config = ['name' => basename($cwd)];

        $paths = $this->askList($style, 'Which paths should be analyzed?', $this->suggestPaths($cwd));
        $config['paths'] = $paths ?: ['.'];

        $vendorExcluded = in_array('.', $config['paths'], true) && is_dir($cwd . '/vendor');
        $excludes = $this->askList(
            $style,
            'Which paths should be excluded? Use * as a wildcard',
            $vendorExcluded ? ['vendor/*'] : []
        );
        if ($excludes !== []) {
            $config['exclude-pattern'] = $excludes;
        }

        if ($style->confirm('Enable the result cache, to only analyze files that changed since the last run?', true)) {
            $config['cache'] = true;
        }

        $config['rules'] = array_map(
            static fn(string $ruleSet): array => ['ref' => "rulesets/{$ruleSet}.xml"],
            $this->askRuleSets($style)
        );

        return $config;
    }

    /**
     * @return list<string>
     */
    private function suggestPaths(string $cwd): array
    {
        $paths = array_values(array_filter(
            self::SOURCE_DIRECTORIES,
            static fn(string $directory): bool => is_dir($cwd . '/' . $directory)
        ));

        return $paths ?: ['.'];
    }

    /**
     * @param list<string> $default
     * @return list<string>
     */
    private function askList(SymfonyStyle $style, string $question, array $default): array
    {
        $answer = $style->ask("{$question} (comma-separated)", $default === [] ? null : implode(', ', $default));
        $answer = is_string($answer) ? $answer : '';

        $values = array_map(trim(...), explode(',', $answer));

        return array_values(array_filter($values, static fn(string $value): bool => $value !== ''));
    }

    /**
     * @return list<string>
     */
    private function askRuleSets(SymfonyStyle $style): array
    {
        $ruleSets = (new RuleSetFactory())->listAvailableRuleSets();

        $question = new ChoiceQuestion(
            'Which rule sets should be enabled? (comma-separated)',
            $ruleSets,
            implode(',', array_keys($ruleSets))
        );
        $question->setMultiselect(true);

        $answer = $style->askQuestion($question);

        return array_values(array_filter((array) $answer, is_string(...)));
    }
}
