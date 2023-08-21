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

namespace PHPMD\TextUI;

use Closure;
use PHPMD\AbstractTest;
use PHPMD\Baseline\BaselineMode;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Console\OutputInterface;
use PHPMD\Rule;
use ReflectionProperty;

/**
 * Test case for the {@link \PHPMD\TextUI\CommandLineOptions} class.
 *
 * @covers \PHPMD\TextUI\CommandLineOptions
 */
class CommandLineOptionsTest extends AbstractTest
{
    /**
     * testAssignsInputArgumentToInputProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputArgumentToInputProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals(__FILE__, $opts->getInputPath());
    }

    /**
     * @return void
     * @since 2.14.0
     */
    public function testVerbose()
    {
        $args = array('foo.php', __FILE__, 'text', 'design', '-vvv');
        $opts = new CommandLineOptions($args);
        $renbderer = $opts->createRenderer();

        $verbosityExtractor = new ReflectionProperty('PHPMD\\Renderer\\TextRenderer', 'verbosityLevel');
        $verbosityExtractor->setAccessible(true);

        $verbosityLevel = $verbosityExtractor->getValue($renbderer);

        self::assertSame(OutputInterface::VERBOSITY_DEBUG, $verbosityLevel);
    }

    /**
     * @return void
     * @since 2.14.0
     */
    public function testColored()
    {
        $args = array('foo.php', __FILE__, 'text', 'design', '--color');
        $opts = new CommandLineOptions($args);
        $renderer = $opts->createRenderer();

        $coloredExtractor = new ReflectionProperty('PHPMD\\Renderer\\TextRenderer', 'colored');
        $coloredExtractor->setAccessible(true);

        $colored = $coloredExtractor->getValue($renderer);

        self::assertTrue($colored);
    }

    /**
     * testAssignsFormatArgumentToReportFormatProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentToReportFormatProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentToRuleSetProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentToRuleSetProperty()
    {
        $args = array('foo.php', __FILE__, 'text', 'design');
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenRequiredArgumentsNotSet
     *
     * @return void
     * @since 1.1.0
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExpectedExceptionWhenRequiredArgumentsNotSet()
    {
        $args = array(__FILE__, 'text', 'design');
        new CommandLineOptions($args);
    }

    /**
     * testAssignsInputFileOptionToInputPathProperty
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsInputFileOptionToInputPathProperty()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('Dir1/Class1.php,Dir2/Class2.php', $opts->getInputPath());
    }

    /**
     * testAssignsFormatArgumentCorrectWhenCalledWithInputFile
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsFormatArgumentCorrectWhenCalledWithInputFile()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('text', $opts->getReportFormat());
    }

    /**
     * testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile
     *
     * @return void
     * @since 1.1.0
     */
    public function testAssignsRuleSetsArgumentCorrectWhenCalledWithInputFile()
    {
        $uri = self::createResourceUriForTest('inputfile.txt');

        $args = array('foo.php', 'text', 'design', '--inputfile', $uri);
        $opts = new CommandLineOptions($args);

        self::assertEquals('design', $opts->getRuleSets());
    }

    /**
     * testThrowsExpectedExceptionWhenInputFileNotExists
     *
     * @return void
     * @since 1.1.0
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExpectedExceptionWhenInputFileNotExists()
    {
        $args = array('foo.php', 'text', 'design', '--inputfile', 'inputfail.txt');
        new CommandLineOptions($args);
    }

    /**
     * testHasVersionReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasVersionReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasVersion());
    }

    /**
     * testCliOptionsAcceptsVersionArgument
     *
     * @return void
     */
    public function testCliOptionsAcceptsVersionArgument()
    {
        $args = array(__FILE__, '--version');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasVersion());
    }

    /**
     * Tests if ignoreErrorsOnExit returns false by default
     *
     * @return void
     */
    public function testIgnoreErrorsOnExitReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreErrorsOnExit argument
     *
     * @return void
     */
    public function testCliOptionsAcceptsIgnoreErrorsOnExitArgument()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-errors-on-exit');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->ignoreErrorsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreErrorsOnExit option
     *
     * @return void
     */
    public function testCliUsageContainsIgnoreErrorsOnExitOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains('--ignore-errors-on-exit:', $opts->usage());
    }

    /**
     * Tests if ignoreViolationsOnExit returns false by default
     *
     * @return void
     */
    public function testIgnoreViolationsOnExitReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI options accepts ignoreViolationsOnExit argument
     *
     * @return void
     */
    public function testCliOptionsAcceptsIgnoreViolationsOnExitArgument()
    {
        $args = array(__FILE__, __FILE__, 'text', 'unusedcode', '--ignore-violations-on-exit');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->ignoreViolationsOnExit());
    }

    /**
     * Tests if CLI usage contains ignoreViolationsOnExit option
     *
     * @return void
     */
    public function testCliUsageContainsIgnoreViolationsOnExitOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains('--ignore-violations-on-exit:', $opts->usage());
    }

    /**
     * Tests if CLI usage contains the auto-discovered renderers
     *
     * @return void
     */
    public function testCliUsageContainsAutoDiscoveredRenderers()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains(
            'Available formats: ansi, baseline, checkstyle, github, gitlab, html, json, sarif, text, xml.',
            $opts->usage()
        );
    }

    /**
     * testCliUsageContainsStrictOption
     *
     * @return void
     */
    public function testCliUsageContainsStrictOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertContains('--strict:', $opts->usage());
    }

    /**
     * testCliOptionsIsStrictReturnsFalseByDefault
     *
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsIsStrictReturnsFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasStrict());
    }

    /**
     * testCliOptionsAcceptsStrictArgument
     *
     * @return void
     * @since 1.2.0
     */
    public function testCliOptionsAcceptsStrictArgument()
    {
        $args = array(__FILE__, '--strict', __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        self::assertTrue($opts->hasStrict());

        $args = array(__FILE__, '--not-strict', __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        self::assertFalse($opts->hasStrict());
    }

    /**
     * @return void
     */
    public function testCliOptionsAcceptsMinimumpriorityArgument()
    {
        $args = array(__FILE__, '--minimumpriority', 42, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertEquals(42, $opts->getMinimumPriority());
    }

    /**
     * @return void
     */
    public function testCliOptionsAcceptsMaximumpriorityArgument()
    {
        $args = array(__FILE__, '--maximumpriority', 42, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertEquals(42, $opts->getMaximumPriority());
    }

    /**
     * @return void
     */
    public function testCliOptionGenerateBaselineFalseByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::NONE, $opts->generateBaseline());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityNormal()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_NORMAL, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityVerbose()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '-v');
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_VERBOSE, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityVeryVerbose()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '-vv');
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_VERY_VERBOSE, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionVerbosityDebug()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '-vvv');
        $opts = new CommandLineOptions($args);
        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $opts->getVerbosity());
    }

    /**
     * @return void
     */
    public function testCliOptionGenerateBaselineShouldBeSet()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '--generate-baseline');
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::GENERATE, $opts->generateBaseline());
    }

    /**
     * @return void
     */
    public function testCliOptionUpdateBaselineShouldBeSet()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '--update-baseline');
        $opts = new CommandLineOptions($args);
        static::assertSame(BaselineMode::UPDATE, $opts->generateBaseline());
    }

    /**
     * @return void
     */
    public function testCliOptionBaselineFileShouldBeNullByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);
        static::assertNull($opts->baselineFile());
    }

    /**
     * @return void
     */
    public function testCliOptionBaselineFileShouldBeWithFilename()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '--baseline-file', 'foobar.txt');
        $opts = new CommandLineOptions($args);
        static::assertSame('foobar.txt', $opts->baselineFile());
    }

    /**
     * @return void
     */
    public function testGetMinimumPriorityReturnsLowestValueByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertEquals(Rule::LOWEST_PRIORITY, $opts->getMinimumPriority());
    }

    /**
     * @return void
     */
    public function testGetCoverageReportReturnsNullByDefault()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertNull($opts->getCoverageReport());
    }

    /**
     * @return void
     */
    public function testGetCoverageReportWithCliOption()
    {
        $opts = new CommandLineOptions(
            array(
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--coverage',
                __METHOD__,
            )
        );

        $this->assertEquals(__METHOD__, $opts->getCoverageReport());
    }

    /**
     * @return void
     */
    public function testGetCacheWithCliOption()
    {
        $opts = new CommandLineOptions(
            array(
                __FILE__,
                __FILE__,
                'text',
                'codesize',
            )
        );

        $this->assertSame(ResultCacheStrategy::CONTENT, $opts->cacheStrategy());
        $this->assertFalse($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            array(
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--cache',
                '--cache-strategy',
                ResultCacheStrategy::TIMESTAMP,
            )
        );

        $this->assertSame(ResultCacheStrategy::TIMESTAMP, $opts->cacheStrategy());
        $this->assertTrue($opts->isCacheEnabled());

        $opts = new CommandLineOptions(
            array(
                __FILE__,
                __FILE__,
                'text',
                'codesize',
                '--cache',
                '--cache-strategy',
                ResultCacheStrategy::CONTENT,
                '--cache-file',
                'abc',
            )
        );

        $this->assertSame(ResultCacheStrategy::CONTENT, $opts->cacheStrategy());
        $this->assertSame('abc', $opts->cacheFile());
        $this->assertTrue($opts->isCacheEnabled());
    }

    /**
     * @return void
     */
    public function testExcludeOption()
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', '--ignore', 'foo/bar', '--error-file', 'abc');
        $opts = new CommandLineOptions($args);

        $this->assertSame('abc', $opts->getErrorFile());
        $this->assertSame('foo/bar', $opts->getIgnore());
        $this->assertSame(array(
            'The --ignore option is deprecated, please use --exclude instead.',
        ), $opts->getDeprecations());

        $args = array(__FILE__, __FILE__, 'text', 'codesize', '--exclude', 'bar/biz');
        $opts = new CommandLineOptions($args);

        $this->assertSame('bar/biz', $opts->getIgnore());
    }

    /**
     * @param string $reportFormat
     * @param string $expectedClass
     * @return void
     * @dataProvider dataProviderCreateRenderer
     */
    public function testCreateRenderer($reportFormat, $expectedClass)
    {
        $args = array(__FILE__, __FILE__, $reportFormat, 'codesize');
        $opts = new CommandLineOptions($args);

        $this->assertInstanceOf($expectedClass, $opts->createRenderer($reportFormat));
    }

    /**
     * @return array
     */
    public function dataProviderCreateRenderer()
    {
        return array(
            array('html', 'PHPMD\\Renderer\\HtmlRenderer'),
            array('text', 'PHPMD\\Renderer\\TextRenderer'),
            array('xml', 'PHPMD\\Renderer\\XmlRenderer'),
            array('ansi', 'PHPMD\\Renderer\\AnsiRenderer'),
            array('github', 'PHPMD\\Renderer\\GitHubRenderer'),
            array('gitlab', 'PHPMD\\Renderer\\GitLabRenderer'),
            array('json', 'PHPMD\\Renderer\\JSONRenderer'),
            array('checkstyle', 'PHPMD\\Renderer\\CheckStyleRenderer'),
            array('sarif', 'PHPMD\\Renderer\\SARIFRenderer'),
            array('PHPMD_Test_Renderer_PEARRenderer', 'PHPMD_Test_Renderer_PEARRenderer'),
            array('PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'),
            /* Test what happens when class already exists. */
            array('PHPMD\\Test\\Renderer\\NamespaceRenderer', 'PHPMD\\Test\\Renderer\\NamespaceRenderer'),
        );
    }

    /**
     * @param string $reportFormat
     * @return void
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp (^Can\'t )
     * @dataProvider dataProviderCreateRendererThrowsException
     */
    public function testCreateRendererThrowsException($reportFormat)
    {
        $args = array(__FILE__, __FILE__, $reportFormat, 'codesize');
        $opts = new CommandLineOptions($args);
        $opts->createRenderer();
    }

    /**
     * @return array
     */
    public function dataProviderCreateRendererThrowsException()
    {
        return array(
            array(''),
            array('PHPMD\\Test\\Renderer\\NotExistsRenderer'),
        );
    }

    /**
     * @param string $deprecatedName
     * @param string $newName
     * @param Closure $result
     * @dataProvider dataProviderDeprecatedCliOptions
     */
    public function testDeprecatedCliOptions($deprecatedName, $newName, Closure $result)
    {
        $args = array(__FILE__, __FILE__, 'text', 'codesize', sprintf('--%s', $deprecatedName), '42');
        $opts = new CommandLineOptions($args);

        $this->assertSame(
            array(
                sprintf(
                    'The --%s option is deprecated, please use --%s instead.',
                    $deprecatedName,
                    $newName
                ),
            ),
            $opts->getDeprecations()
        );
        $result($opts);

        $args = array(__FILE__, __FILE__, 'text', 'codesize', sprintf('--%s', $newName), '42');
        $opts = new CommandLineOptions($args);

        $this->assertSame(
            array(),
            $opts->getDeprecations()
        );
        $result($opts);
    }

    /**
     * @return array
     */
    public function dataProviderDeprecatedCliOptions()
    {
        $testCase = $this;

        return array(
            array('extensions', 'suffixes', function (CommandLineOptions $opts) use ($testCase) {
                $testCase->assertSame('42', $opts->getExtensions());
            }),
            array('ignore', 'exclude', function (CommandLineOptions $opts) use ($testCase) {
                $testCase->assertSame('42', $opts->getIgnore());
            }),
        );
    }

    /**
     * @param array $options
     * @param array $expected
     * @return void
     * @dataProvider dataProviderGetReportFiles
     */
    public function testGetReportFiles(array $options, array $expected)
    {
        $args = array_merge(array(__FILE__, __FILE__, 'text', 'codesize'), $options);
        $opts = new CommandLineOptions($args);

        $this->assertEquals($expected, $opts->getReportFiles());
    }

    public function dataProviderGetReportFiles()
    {
        return array(
            array(
                array('--reportfile-xml', __FILE__),
                array('xml' => __FILE__),
            ),
            array(
                array('--reportfile-html', __FILE__),
                array('html' => __FILE__),
            ),
            array(
                array('--reportfile-text', __FILE__),
                array('text' => __FILE__),
            ),
            array(
                array('--reportfile-github', __FILE__),
                array('github' => __FILE__),
            ),
            array(
                array('--reportfile-gitlab', __FILE__),
                array('gitlab' => __FILE__),
            ),
            array(
                array(
                    '--reportfile-text',
                    __FILE__,
                    '--reportfile-xml',
                    __FILE__,
                    '--reportfile-html',
                    __FILE__,
                    '--reportfile-github',
                    __FILE__,
                    '--reportfile-gitlab',
                    __FILE__,
                ),
                array(
                    'text' => __FILE__,
                    'xml' => __FILE__,
                    'html' => __FILE__,
                    'github' => __FILE__,
                    'gitlab' => __FILE__,
                ),
            ),
        );
    }
}
