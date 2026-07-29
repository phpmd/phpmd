<?php

namespace PHPMD\Cache\Model;

use OutOfBoundsException;
use PHPMD\Node\NodeInfo;
use PHPMD\ProcessingError;
use PHPMD\Rule\CleanCode\BooleanArgumentFlag;
use PHPMD\RuleSet;
use PHPMD\RuleViolation;
use PHPUnit\Framework\TestCase;

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
     * @covers ::getCacheKey
     */
    public function testGetCacheKey(): void
    {
        static::assertSame($this->key, $this->state->getCacheKey());
    }

    /**
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
     * @covers ::getErrors
     * @covers ::setErrors
     */
    public function testGetSetErrors(): void
    {
        $errors = ['Unexpected end of token stream in file: /file/path.'];

        static::assertCount(0, $this->state->getErrors('/file/path'));

        $this->state->setErrors('/file/path', $errors);
        static::assertSame($errors, $this->state->getErrors('/file/path'));
    }

    /**
     * @covers ::addError
     * @covers ::getErrors
     * @covers ::getProcessingErrors
     */
    public function testAddErrorAndGetProcessingErrors(): void
    {
        $error = new ProcessingError('Unexpected end of token stream in file: /file/path.');

        static::assertCount(0, $this->state->getProcessingErrors());

        $this->state->addError('/file/path', $error);
        static::assertSame([$error->getMessage()], $this->state->getErrors('/file/path'));

        $errors = $this->state->getProcessingErrors();
        static::assertCount(1, $errors);
        static::assertSame($error->getMessage(), $errors[0]->getMessage());
        static::assertSame('/file/path', $errors[0]->getFile());
    }

    /**
     * @covers ::addError
     * @covers ::toArray
     */
    public function testToArrayWithErrors(): void
    {
        $error = new ProcessingError('Unexpected end of token stream in file: /file/path.');

        $this->state->setFileState('/file/path', 'hash');
        $this->state->addError('/file/path', $error);

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
                        'errors' => [
                            'Unexpected end of token stream in file: /file/path.',
                        ],
                    ],
                ],
            ],
        ];

        static::assertSame($expected, $this->state->toArray());
    }

    /**
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
     * @covers ::findRuleIn
     * @covers ::getRuleViolations
     */
    public function testGetRuleViolationsWithNullMetric(): void
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

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', null);

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        $violations = $this->state->getRuleViolations('', [$ruleSet]);
        static::assertCount(1, $violations);
        static::assertEquals($ruleViolation, $violations[0]);
    }

    /**
     * @covers ::getRuleViolations
     */
    public function testGetRuleViolationsWithEmptyState(): void
    {
        static::assertSame([], $this->state->getRuleViolations('', []));
    }

    /**
     * @covers ::getRuleViolations
     */
    public function testGetRuleViolationsSkipsFilesWithoutViolations(): void
    {
        $this->state->setFileState('/file/path', 'hash');
        $this->state->addError('/file/path', new ProcessingError('Failure in file: /file/path.'));

        static::assertSame([], $this->state->getRuleViolations('', []));
    }

    /**
     * @covers ::findRuleIn
     * @covers ::getRuleViolations
     */
    public function testGetRuleViolationsWithUnknownRuleThrowsException(): void
    {
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

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', 100);
        $this->state->addRuleViolation('/file/path', $ruleViolation);

        self::expectException(OutOfBoundsException::class);

        $this->state->getRuleViolations('', [new RuleSet()]);
    }

    /**
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
