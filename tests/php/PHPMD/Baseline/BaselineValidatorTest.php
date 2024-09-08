<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use PHPMD\Rule;
use PHPMD\RuleViolation;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineValidator
 * @covers ::__construct
 */
class BaselineValidatorTest extends AbstractTestCase
{
    /** @var BaselineSet&MockObject */
    private $baselineSet;

    /** @var MockObject&RuleViolation */
    private $violation;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();
        $rule = $this->getMockBuilder(Rule::class)->disableOriginalConstructor()->getMock();
        $this->violation = $this->getMockBuilder(RuleViolation::class)->disableOriginalConstructor()->getMock();
        $this->violation
            ->method('getRule')
            ->willReturn($rule);
        $this->baselineSet = $this->getMockBuilder(BaselineSet::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @throws Throwable
     * @dataProvider dataProvider
     * @covers ::isBaselined
     */
    public function testIsBaselined(bool $contains, BaselineMode $baselineMode, bool $isBaselined): void
    {
        $this->baselineSet->method('contains')->willReturn($contains);
        $validator = new BaselineValidator($this->baselineSet, $baselineMode);
        static::assertSame($isBaselined, $validator->isBaselined($this->violation));
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProvider(): array
    {
        return [
            'contains: true, mode: none' => [true, BaselineMode::None, true],
            'contains: false, mode: none' => [false, BaselineMode::None, false],
            'contains: true, mode: update' => [true, BaselineMode::Update, false],
            'contains: false, mode: update' => [false, BaselineMode::Update, true],
            'contains: true, mode: generate' => [true, BaselineMode::Generate, false],
            'contains: false, mode: generate' => [false, BaselineMode::Generate, false],
        ];
    }
}
