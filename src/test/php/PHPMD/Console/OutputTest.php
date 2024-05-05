<?php

namespace PHPMD\Console;

use PHPMD\AbstractTestCase;

/**
 * @coversDefaultClass  \PHPMD\Console\Output
 * @covers ::__construct
 */
class OutputTest extends AbstractTestCase
{
    /** @var TestOutput */
    private $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->output = new TestOutput();
    }

    /**
     * @return void
     * @covers ::getVerbosity
     * @covers ::setVerbosity
     */
    public function testSetGetVerbosity()
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        static::assertSame(OutputInterface::VERBOSITY_VERBOSE, $this->output->getVerbosity());

        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $this->output->getVerbosity());
    }

    /**
     * @return void
     * @covers ::write
     */
    public function testWriteSingleMessage()
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->output->write("message", false, OutputInterface::VERBOSITY_VERBOSE);

        static::assertSame('message', $this->output->getOutput());
    }

    /**
     * @return void
     * @covers ::write
     */
    public function testWriteMultiMessageWithNewline()
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->output->write(["foo", "bar"], true, OutputInterface::VERBOSITY_VERBOSE);

        static::assertSame("foo\nbar\n", $this->output->getOutput());
    }

    /**
     * @param int    $verbosity
     * @param string $expected
     * @param string $msg
     * @dataProvider verbosityProvider
     * @covers ::write
     */
    public function testWriteWithVerbosityOption($verbosity, $expected, $msg)
    {
        $this->output->setVerbosity($verbosity);
        $this->output->write('1', false);
        $this->output->write('2', false, Output::VERBOSITY_QUIET);
        $this->output->write('3', false, Output::VERBOSITY_NORMAL);
        $this->output->write('4', false, Output::VERBOSITY_VERBOSE);
        $this->output->write('5', false, Output::VERBOSITY_VERY_VERBOSE);
        $this->output->write('6', false, Output::VERBOSITY_DEBUG);
        static::assertSame($expected, $this->output->getOutput(), $msg);
    }

    public static function verbosityProvider()
    {
        return [
            [
                Output::VERBOSITY_QUIET,
                '2',
                '->write() in QUIET mode only outputs when an explicit QUIET verbosity is passed',
            ],
            [
                Output::VERBOSITY_NORMAL,
                '123',
                '->write() in NORMAL mode outputs anything below an explicit VERBOSE verbosity',
            ],
            [
                Output::VERBOSITY_VERBOSE,
                '1234',
                '->write() in VERBOSE mode outputs anything below an explicit VERY_VERBOSE verbosity',
            ],
            [
                Output::VERBOSITY_VERY_VERBOSE,
                '12345',
                '->write() in VERY_VERBOSE mode outputs anything below an explicit DEBUG verbosity',
            ],
            [
                Output::VERBOSITY_DEBUG,
                '123456',
                '->write() in DEBUG mode outputs everything',
            ],
        ];
    }

    /**
     * @return void
     * @covers ::writeln
     */
    public function testWritelnMessage()
    {
        $this->output->writeln("message", OutputInterface::VERBOSITY_QUIET);

        static::assertSame("message\n", $this->output->getOutput());
    }
}
