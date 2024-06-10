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
 * @author Lukas Bestle <project-phpmd@lukasbestle.com>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Renderer;

use PHPMD\PHPMD;
use PHPMD\Report;

/**
 * This class will render a SARIF (Static Analysis
 * Results Interchange Format) report.
 */
final class SARIFRenderer extends JSONRenderer
{
    /**
     * Create report data and add renderer meta properties
     *
     * @return array<string, mixed>
     */
    protected function initReportData(): array
    {
        return [
            'version' => '2.1.0',
            '$schema' => 'https://raw.githubusercontent.com/oasis-tcs/' .
                'sarif-spec/master/Schemata/sarif-schema-2.1.0.json',
            'runs' => [
                [
                    'tool' => [
                        'driver' => [
                            'name' => 'PHPMD',
                            'informationUri' => 'https://phpmd.org',
                            'version' => PHPMD::VERSION,
                            'rules' => [],
                        ],
                    ],
                    'originalUriBaseIds' => [
                        'WORKINGDIR' => [
                            'uri' => self::pathToUri(getcwd() ?: '') . '/',
                        ],
                    ],
                    'results' => [],
                ],
            ],
        ];
    }

    /**
     * Add violations, if any, to the report data
     *
     * @param Report $report The report with potential violations.
     * @param array<string, array<int, array<string, array<array<mixed>>>>> $data The report output to add the
     *                                                                            violations to.
     * @return array<string, array<int, array<string, array<array<mixed>>>>> The report output with violations, if any.
     */
    protected function addViolationsToReport(Report $report, array $data): array
    {
        $rules = [];
        $results = [];
        $ruleIndices = [];

        foreach ($report->getRuleViolations() as $violation) {
            $rule = $violation->getRule();
            $ruleRef = str_replace(' ', '', $rule->getRuleSetName()) . '/' . $rule->getName();

            if (!isset($ruleIndices[$ruleRef])) {
                $ruleIndices[$ruleRef] = count($rules);

                $ruleData = [
                    'id' => $ruleRef,
                    'name' => $rule->getName(),
                    'shortDescription' => [
                        'text' => $rule->getRuleSetName() . ': ' . $rule->getName(),
                    ],
                    'messageStrings' => [
                        'default' => [
                            'text' => trim($rule->getMessage()),
                        ],
                    ],
                    'help' => [
                        'text' => trim(str_replace("\n", ' ', $rule->getDescription())),
                    ],
                    'helpUri' => $rule->getExternalInfoUrl(),
                    'properties' => [
                        'ruleSet' => $rule->getRuleSetName(),
                        'priority' => $rule->getPriority(),
                    ],
                ];

                $examples = $rule->getExamples();
                if (!empty($examples)) {
                    $ruleData['help']['markdown'] =
                        $ruleData['help']['text'] .
                        "\n\n### Example\n\n```php\n" .
                        implode("\n```\n\n```php\n", array_map(trim(...), $examples)) . "\n```";
                }

                $since = $rule->getSince();
                if ($since) {
                    $ruleData['properties']['since'] = 'PHPMD ' . $since;
                }

                $rules[] = $ruleData;
            }

            $arguments = $violation->getArgs();
            if ($arguments === null) {
                $arguments = [];
            }

            $results[] = [
                'ruleId' => $ruleRef,
                'ruleIndex' => $ruleIndices[$ruleRef],
                'message' => [
                    'id' => 'default',
                    'arguments' => array_map('strval', $arguments),
                    'text' => $violation->getDescription(),
                ],
                'locations' => [
                    [
                        'physicalLocation' => [
                            'artifactLocation' => self::pathToArtifactLocation((string) $violation->getFileName()),
                            'region' => [
                                'startLine' => $violation->getBeginLine(),
                                'endLine' => $violation->getEndLine(),
                            ],
                        ],
                    ],
                ],
            ];
        }

        $data['runs'][0]['tool']['driver']['rules'] = $rules;
        $data['runs'][0]['results'] = $results + $data['runs'][0]['results'];

        return $data;
    }

    /**
     * Add errors, if any, to the report data
     *
     * @param Report $report The report with potential errors.
     * @param array<string, array<int, array<string, list<array<string, mixed>>>>> $data The report output to add the
     *                                                                                   errors to.
     * @return array<string, array<int, array<string, list<array<string, mixed>>>>> The report output with errors, if
     *                                                                              any.
     */
    protected function addErrorsToReport(Report $report, array $data): array
    {
        $errors = $report->getErrors();
        foreach ($errors as $error) {
            $data['runs'][0]['results'][] = [
                'level' => 'error',
                'message' => [
                    'text' => $error->getMessage(),
                ],
                'locations' => [
                    [
                        'physicalLocation' => [
                            'artifactLocation' => self::pathToArtifactLocation($error->getFile()),
                        ],
                    ],
                ],
            ];
        }

        return $data;
    }

    /**
     * Makes an absolute path relative to the working directory
     * if possible, otherwise prepends the `file://` protocol
     * and returns the result as a SARIF `artifactLocation`
     *
     * @return array<string, string>
     */
    private static function pathToArtifactLocation(string $path): array
    {
        $workingDir = getcwd() ?: '';
        if (str_starts_with($path, $workingDir)) {
            // relative path
            return [
                'uri' => substr($path, strlen($workingDir) + 1),
                'uriBaseId' => 'WORKINGDIR',
            ];
        }

        // absolute path with protocol
        return [
            'uri' => self::pathToUri($path),
        ];
    }

    /**
     * Converts an absolute path to a file:// URI
     */
    private static function pathToUri(string $path): string
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        // file:///C:/... on Windows systems
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return 'file://' . $path;
    }
}
