<?php

namespace PHPMD\Console;

use PHPMD\AbstractTestCase;
use Throwable;

/**
 * @coversDefaultClass  \PHPMD\Console\Output
 * @covers ::__construct
 */
class OutputTest extends AbstractTestCase
{
    /** @var TestOutput */
    private $output;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();
        $stream = fopen('php://memory', 'w+b');
        static::assertIsResource($stream);
        $this->output = new TestOutput($stream);
    }

    /**
     * @throws Throwable
     * @covers ::getVerbosity
     * @covers ::setVerbosity
     */
    public function testSetGetVerbosity(): void
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        static::assertSame(OutputInterface::VERBOSITY_VERBOSE, $this->output->getVerbosity());

        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        static::assertSame(OutputInterface::VERBOSITY_DEBUG, $this->output->getVerbosity());
    }

    /**
     * @throws Throwable
     * @covers ::write
     */
    public function testWriteSingleMessage(): void
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->output->write('message', false, OutputInterface::VERBOSITY_VERBOSE);

        static::assertSame('message', $this->output->getOutput());
    }

    /**
     * @throws Throwable
     * @covers ::write
     */
    public function testWriteMultiMessageWithNewline(): void
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->output->write(['foo', 'bar'], true, OutputInterface::VERBOSITY_VERBOSE);

        static::assertSame("foo\nbar\n", $this->output->getOutput());
    }

    /**
     * @param int    $verbosity
     * @param string $expected
     * @param string $msg
     * @throws Throwable
     * @dataProvider verbosityProvider
     * @covers ::write
     */
    public function testWriteWithVerbosityOption($verbosity, $expected, $msg): void
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

    /**
     * @return list<mixed>
     */
    public static function verbosityProvider(): array
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
     * @throws Throwable
     * @covers ::writeln
     */
    public function testWritelnMessage(): void
    {
        $this->output->writeln('message', OutputInterface::VERBOSITY_QUIET);

        static::assertSame("message\n", $this->output->getOutput());
    }
}
