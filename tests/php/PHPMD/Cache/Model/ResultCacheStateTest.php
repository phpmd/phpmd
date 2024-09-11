<?php

namespace PHPMD\Cache\Model;

use PHPMD\Node\NodeInfo;
use PHPMD\Rule\CleanCode\BooleanArgumentFlag;
use PHPMD\RuleSet;
use PHPMD\RuleViolation;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Cache\Model\ResultCacheState
 * @covers ::__construct
 */
class ResultCacheStateTest extends TestCase
{
    private ResultCacheKey $key;

    private ResultCacheState $state;

    protected function setUp(): void
    {
        $this->key = new ResultCacheKey(true, 'baseline', [], [], 123);
        $this->state = new ResultCacheState($this->key, []);
    }

    /**
     * @throws Throwable
     * @covers ::getCacheKey
     */
    public function testGetCacheKey(): void
    {
        static::assertSame($this->key, $this->state->getCacheKey());
    }

    /**
     * @throws Throwable
     * @covers ::getViolations
     * @covers ::setViolations
     */
    public function testGetSetViolations(): void
    {
        $violations = [[
            'metric' => null,
            'violations' => 100,
            'namespaceName' => null,
            'className' => null,
            'methodName' => null,
            'functionName' => null,
            'description' => '',
            'beginLine' => 0,
            'endLine' => 0,
            'rule' => '',
            'args' => null,
        ]];

        static::assertCount(0, $this->state->getViolations('/file/path'));

        $this->state->setViolations('/file/path', $violations);
        static::assertSame($violations, $this->state->getViolations('/file/path'));
    }

    /**
     * @throws Throwable
     * @covers ::addRuleViolation
     */
    public function testAddRuleViolation(): void
    {
        $rule = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo(
            'fileName',
            'namespace',
            'className',
            'methodName',
            'functionName',
            123,
            456
        );
        $metric = 100;

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', $metric);

        $expected = [
            [
                'rule' => BooleanArgumentFlag::class,
                'namespaceName' => 'namespace',
                'className' => 'className',
                'methodName' => 'methodName',
                'functionName' => 'functionName',
                'beginLine' => 123,
                'endLine' => 456,
                'description' => 'violation',
                'args' => null,
                'metric' => $metric,
            ],
        ];

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        static::assertSame($expected, $this->state->getViolations('/file/path'));
    }

    /**
     * @throws Throwable
     * @covers ::findRuleIn
     * @covers ::getRuleViolations
     */
    public function testGetRuleViolationsWithoutDescriptionArgs(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new BooleanArgumentFlag());
        $rule = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo(
            '/file/path',
            'namespace',
            'className',
            'methodName',
            'functionName',
            123,
            456
        );
        $metric = 100;

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', $metric);

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        $violations = $this->state->getRuleViolations('', [$ruleSet]);
        static::assertEquals($ruleViolation, $violations[0]);
    }

    /**
     * @throws Throwable
     * @covers ::findRuleIn
     * @covers ::getRuleViolations
     */
    public function testGetRuleViolationsWithDescriptionArgs(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new BooleanArgumentFlag());
        $rule = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo(
            '/file/path',
            'namespace',
            'className',
            'methodName',
            'functionName',
            123,
            456
        );
        $metric = 100;

        $ruleViolation = new RuleViolation(
            $rule,
            $nodeInfo,
            ['args' => ['bar'], 'message' => 'violation'],
            $metric
        );

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        $violations = $this->state->getRuleViolations('', [$ruleSet]);
        static::assertEquals($ruleViolation, $violations[0]);
    }

    /**
     * @throws Throwable
     * @covers ::isFileModified
     * @covers ::setFileState
     */
    public function testIsFileModified(): void
    {
        $this->state->setFileState('/file/path', 'hash');

        static::assertTrue($this->state->isFileModified('foobar', 'hash'));
        static::assertTrue($this->state->isFileModified('/file/path', 'foobar'));
        static::assertFalse($this->state->isFileModified('/file/path', 'hash'));
    }

    /**
     * @throws Throwable
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new BooleanArgumentFlag());
        $rule = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo(
            '/file/path',
            'namespace',
            'className',
            'methodName',
            'functionName',
            123,
            456
        );
        $metric = 100;

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', $metric);
        $this->state->setFileState('/file/path', 'hash');
        $this->state->addRuleViolation('/file/path', $ruleViolation);

        $expected = [
            'key' => [
                'strict' => true,
                'baselineHash' => 'baseline',
                'rules' => [],
                'composer' => [],
                'phpVersion' => 123,
            ],
            'state' => [
                'files' => [
                    '/file/path' => [
                        'hash' => 'hash',
                        'violations' => [
                            [
                                'rule' => BooleanArgumentFlag::class,
                                'namespaceName' => 'namespace',
                                'className' => 'className',
                                'methodName' => 'methodName',
                                'functionName' => 'functionName',
                                'beginLine' => 123,
                                'endLine' => 456,
                                'description' => 'violation',
                                'args' => null,
                                'metric' => $metric,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        static::assertSame($expected, $this->state->toArray());
    }
}
