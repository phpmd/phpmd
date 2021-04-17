<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTest;
use PHPMD\RuleViolation;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineValidator
 * @covers ::__construct
 */
class BaselineValidatorTest extends AbstractTest
{
    /** @var BaselineSet|PHPUnit_Framework_MockObject_MockObject */
    private $baselineSet;

    /** @var RuleViolation|PHPUnit_Framework_MockObject_MockObject */
    private $violation;

    protected function setUp(): void
    {
        parent::setUp();
        $rule            = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Rule')->disableOriginalConstructor()
        );
        $this->violation = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\RuleViolation')->disableOriginalConstructor()
        );
        $this->violation
            ->method('getRule')
            ->willReturn($rule);
        $this->baselineSet = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Baseline\BaselineSet')->disableOriginalConstructor()
        );
    }

    /**
     * @covers ::isBaselined
     * @dataProvider dataProvider
     * @param bool   $contains
     * @param string $baselineMode
     * @param bool   $isBaselined
     */
    public function testIsBaselined($contains, $baselineMode, $isBaselined)
    {
        $this->baselineSet->method('contains')->willReturn($contains);
        $validator = new BaselineValidator($this->baselineSet, $baselineMode);
        static::assertSame($isBaselined, $validator->isBaselined($this->violation));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            'contains: true, mode: none'      => array(true, BaselineMode::NONE, true),
            'contains: false, mode: none'     => array(false, BaselineMode::NONE, false),
            'contains: true, mode: update'    => array(true, BaselineMode::UPDATE, false),
            'contains: false, mode: update'   => array(false, BaselineMode::UPDATE, true),
            'contains: true, mode: generate'  => array(true, BaselineMode::GENERATE, false),
            'contains: false, mode: generate' => array(false, BaselineMode::GENERATE, false),
        );
    }
}
