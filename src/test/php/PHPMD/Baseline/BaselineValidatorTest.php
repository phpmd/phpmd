<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use PHPMD\Rule;
use PHPMD\RuleViolation;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineValidator
 * @covers ::__construct
 */
class BaselineValidatorTest extends AbstractTestCase
{
    /** @var BaselineSet|MockObject */
    private $baselineSet;

    /** @var MockObject|RuleViolation */
    private $violation;

    protected function setUp(): void
    {
        parent::setUp();
        $rule = $this->getMockFromBuilder(
            $this->getMockBuilder(Rule::class)->disableOriginalConstructor()
        );
        $this->violation = $this->getMockFromBuilder(
            $this->getMockBuilder(RuleViolation::class)->disableOriginalConstructor()
        );
        $this->violation
            ->method('getRule')
            ->willReturn($rule);
        $this->baselineSet = $this->getMockFromBuilder(
            $this->getMockBuilder(BaselineSet::class)->disableOriginalConstructor()
        );
    }

    /**
     * @param bool   $contains
     * @param string $baselineMode
     * @param bool   $isBaselined
     * @dataProvider dataProvider
     * @covers ::isBaselined
     */
    public function testIsBaselined($contains, $baselineMode, $isBaselined)
    {
        $this->baselineSet->method('contains')->willReturn($contains);
        $validator = new BaselineValidator($this->baselineSet, $baselineMode);
        static::assertSame($isBaselined, $validator->isBaselined($this->violation));
    }

    public static function dataProvider(): array
    {
        return [
            'contains: true, mode: none' => [true, BaselineMode::NONE, true],
            'contains: false, mode: none' => [false, BaselineMode::NONE, false],
            'contains: true, mode: update' => [true, BaselineMode::UPDATE, false],
            'contains: false, mode: update' => [false, BaselineMode::UPDATE, true],
            'contains: true, mode: generate' => [true, BaselineMode::GENERATE, false],
            'contains: false, mode: generate' => [false, BaselineMode::GENERATE, false],
        ];
    }
}
