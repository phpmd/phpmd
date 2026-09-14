<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use PHPMD\Rule;
use PHPMD\RuleViolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

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
     * @covers ::isBaselined
     */
    #[DataProvider('dataProvider')]
    public function testIsBaselined(bool $contains): void
    {
        $this->baselineSet->method('contains')->willReturn($contains);
        $validator = new BaselineValidator($this->baselineSet);
        static::assertSame($contains, $validator->isBaselined($this->violation));
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProvider(): array
    {
        return [
            'contains: true' => [true],
            'contains: false' => [false],
        ];
    }
}
