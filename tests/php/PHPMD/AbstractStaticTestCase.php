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

use Closure;
use ErrorException;
use PHPUnit\Framework\TestCase;

/**
 * Abstract base class for PHPMD test cases.
 */
abstract class AbstractStaticTestCase extends TestCase
{
    /** Directory with test files. */
    protected static string $filesDirectory;

    /**
     * Original directory is used to reset a changed working directory.
     *
     * @return void
     */
    private static ?string $originalWorkingDirectory = null;

    /**
     * Temporary files created by a test.
     *
     * @var list<string>
     */
    private static array $tempFiles = [];

    /**
     * This method initializes the test environment, it configures the files
     * directory and sets the include_path for svn versions.
     */
    public static function setUpBeforeClass(): void
    {
        $path = realpath(__DIR__ . '/../../resources/files');
        static::assertNotFalse($path);
        self::$filesDirectory = $path;

        if (!str_contains(get_include_path(), self::$filesDirectory)) {
            set_include_path(
                sprintf(
                    '%s%s%s%s%s',
                    get_include_path(),
                    PATH_SEPARATOR,
                    self::$filesDirectory,
                    PATH_SEPARATOR,
                    realpath(__DIR__ . '/../')
                )
            );
        }

        // Prevent timezone warnings if no default TZ is set (PHP > 5.1.0)
        date_default_timezone_set('UTC');
    }

    /**
     * Return to original working directory if changed.
     */
    protected static function returnToOriginalWorkingDirectory(): void
    {
        if (self::$originalWorkingDirectory !== null) {
            chdir(self::$originalWorkingDirectory);
        }

        self::$originalWorkingDirectory = null;
    }

    /**
     * Cleanup temporary files created for the test.
     */
    protected static function cleanupTempFiles(): void
    {
        // cleanup any open resources on temp files
        gc_collect_cycles();
        foreach (self::$tempFiles as $tempFile) {
            unlink($tempFile);
        }

        self::$tempFiles = [];
    }

    /**
     * Returns the absolute path for a test resource for the current test.
     *
     * @since 1.1.0
     */
    protected static function createCodeResourceUriForTest(): string
    {
        $frame = static::getCallingTestCase();

        return self::createResourceUriForTest($frame['function'] . '.php');
    }

    /**
     * Convert [1, 'a', $any] into [[1], ['a'], [$any]].
     *
     * @param array<string> $values list of values.
     * @return array<string, array<string>>
     */
    protected static function getValuesAsArrays(array $values): array
    {
        $valuesAsArray = [];
        foreach ($values as $value) {
            $valuesAsArray[$value] = [$value];
        }

        return $valuesAsArray;
    }

    /**
     * Returns the absolute path for a test resource for the current test.
     *
     * @param string $localPath The local/relative file location
     *
     * @since 1.1.0
     */
    protected static function createResourceUriForTest(string $localPath): string
    {
        $class = static::getCallingTestCase()['class'] ?? null;
        static::assertIsString($class);

        return static::getResourceFilePathFromClassName($class, $localPath);
    }

    /**
     * Asserts the actual xml output matches against the expected file.
     *
     * @param string $actualOutput Generated xml output.
     * @param string $expectedFileName File with expected xml result.
     */
    public static function assertXmlEquals(string $actualOutput, string $expectedFileName): void
    {
        $actual = simplexml_load_string($actualOutput);
        static::assertNotFalse($actual);
        // Remove dynamic timestamp and duration attribute
        if (isset($actual['timestamp'])) {
            $actual['timestamp'] = '';
        }
        if (isset($actual['duration'])) {
            $actual['duration'] = '';
        }
        if (isset($actual['version'])) {
            $actual['version'] = '@package_version@';
        }

        $content = file_get_contents(self::createFileUri($expectedFileName));
        static::assertNotFalse($content);
        $expected = str_replace('#{rootDirectory}', self::$filesDirectory, $content);

        $expected = str_replace('_DS_', DIRECTORY_SEPARATOR, $expected);
        $xml = $actual->saveXML();

        static::assertNotFalse($xml);
        static::assertXmlStringEqualsXmlString($expected, $xml);
    }

    /**
     * Asserts the actual JSON output matches against the expected file.
     *
     * @param string $actualOutput Generated JSON output.
     * @param string $expectedFileName File with expected JSON result.
     * @param bool $removeDynamicValues If set to `false`, the actual output is not normalized,
     *                                          if set to a closure, the closure is applied on the actual output array.
     */
    public static function assertJsonEquals(
        string $actualOutput,
        string $expectedFileName,
        bool $removeDynamicValues = true
    ): void {
        $actual = json_decode($actualOutput, true);
        static::assertIsArray($actual);
        // Remove dynamic timestamp and duration attribute
        if ($removeDynamicValues) {
            if (isset($actual['timestamp'])) {
                $actual['timestamp'] = '';
            }
            if (isset($actual['duration'])) {
                $actual['duration'] = '';
            }
            if (isset($actual['version'])) {
                $actual['version'] = '@package_version@';
            }
        }

        $content = file_get_contents(self::createFileUri($expectedFileName));
        static::assertNotFalse($content);
        $expected = str_replace('#{rootDirectory}', self::$filesDirectory, $content);

        $cwd = getcwd();
        static::assertNotFalse($cwd);

        $expected = str_replace('#{workingDirectory}', $cwd, $expected);
        $expected = str_replace('_DS_', DIRECTORY_SEPARATOR, $expected);

        static::assertJsonStringEqualsJsonString($expected, json_encode($actual, JSON_THROW_ON_ERROR));
    }

    /**
     * Changes the working directory for a single test.
     *
     * @param string $localPath The temporary working directory.
     */
    protected static function changeWorkingDirectory(string $localPath = ''): void
    {
        self::$originalWorkingDirectory = getcwd() ?: null;

        if (0 === preg_match('(^([A-Z]:|/))', $localPath)) {
            $localPath = self::createFileUri($localPath);
        }
        chdir($localPath);
    }

    /**
     * Creates a full filename for a test content in the <em>_files</b> directory.
     */
    protected static function createFileUri(string $localPath = ''): string
    {
        return self::$filesDirectory . '/' . $localPath;
    }

    /**
     * Creates a file uri for a temporary test file.
     */
    protected static function createTempFileUri(?string $fileName = null): string
    {
        if ($fileName !== null) {
            $filePath = sys_get_temp_dir() . '/' . $fileName;
        } else {
            $filePath = tempnam(sys_get_temp_dir(), 'phpmd.');
            static::assertNotFalse($filePath);
        }

        return (self::$tempFiles[] = $filePath);
    }

    /**
     * Returns the trace frame of the calling test case.
     *
     * @return array{function: string, class?: class-string}
     * @throws ErrorException
     */
    protected static function getCallingTestCase(): array
    {
        foreach (debug_backtrace() as $frame) {
            if (str_starts_with($frame['function'], 'test')) {
                return $frame;
            }
        }

        throw new ErrorException('Cannot locate calling test case.');
    }

    protected static function getResourceFilePathFromClassName(string $className, string $localPath): string
    {
        return self::getResourceFilePath(self::getTestPathFromClassName($className), $localPath);
    }

    private static function getTestPathFromClassName(string $className): string
    {
        $regexp = '([a-z]([0-9]+)Test$)i';

        if (preg_match($regexp, $className, $match)) {
            $parts = explode('\\', $className);

            return $parts[count($parts) - 2] . '/' . $match[1];
        }

        return strtr(substr($className, 6, -4), '\\', '/');
    }

    private static function getResourceFilePath(string $directory, string $file): string
    {
        return sprintf(
            '%s/../../resources/files/%s/%s',
            __DIR__,
            $directory,
            $file
        );
    }
}
