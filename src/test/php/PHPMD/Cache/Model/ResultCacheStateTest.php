<?php

namespace PHPMD\Cache\Model;

use PHPMD\Node\NodeInfo;
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
    /** @var ResultCacheKey */
    private $key;

    /** @var ResultCacheState */
    private $state;

    protected function setUp()
    {
        $this->key   = new ResultCacheKey(true, array(), array(), 123);
        $this->state = new ResultCacheState($this->key, array());
    }

    /**
     * @covers ::getCacheKey
     */
    public function testGetCacheKey()
    {
        static::assertSame($this->key, $this->state->getCacheKey());
    }

    /**
     * @covers ::setViolations
     * @covers ::getViolations
     */
    public function testGetSetViolations()
    {
        $violations = array('violations');

        static::assertCount(0, $this->state->getViolations('/file/path'));

        $this->state->setViolations('/file/path', $violations);
        static::assertSame($violations, $this->state->getViolations('/file/path'));
    }

    /**
     * @covers ::addRuleViolation
     */
    public function testAddRuleViolation()
    {
        $rule     = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo('fileName', 'namespace', 'className', 'methodName', 'functionName', 123, 456);
        $metric   = array('line' => 100);

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', $metric);

        $expected = array(
            array(
                'rule'          => 'PHPMD\Rule\CleanCode\BooleanArgumentFlag',
                'namespaceName' => 'namespace',
                'className'     => 'className',
                'methodName'    => 'methodName',
                'functionName'  => 'functionName',
                'beginLine'     => 123,
                'endLine'       => 456,
                'description'   => 'violation',
                'args'          => null,
                'metric'        => $metric
            )
        );

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        static::assertSame($expected, $this->state->getViolations('/file/path'));
    }

    /**
     * @covers ::getRuleViolations
     * @covers ::findRuleIn
     */
    public function testGetRuleViolationsWithoutDescriptionArgs()
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new BooleanArgumentFlag());
        $rule     = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo('/file/path', 'namespace', 'className', 'methodName', 'functionName', 123, 456);
        $metric   = array('line' => 100);

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', $metric);

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        $violations = $this->state->getRuleViolations('', array($ruleSet));
        static::assertEquals($ruleViolation, $violations[0]);
    }

    /**
     * @covers ::getRuleViolations
     * @covers ::findRuleIn
     */
    public function testGetRuleViolationsWithDescriptionArgs()
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new BooleanArgumentFlag());
        $rule     = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo('/file/path', 'namespace', 'className', 'methodName', 'functionName', 123, 456);
        $metric   = array('line' => 100);

        $ruleViolation = new RuleViolation($rule, $nodeInfo, array('args' => array('foo' => 'bar'), 'message' => 'violation'), $metric);

        $this->state->addRuleViolation('/file/path', $ruleViolation);
        $violations = $this->state->getRuleViolations('', array($ruleSet));
        static::assertEquals($ruleViolation, $violations[0]);
    }

    /**
     * @covers ::setFileState
     * @covers ::isFileModified
     */
    public function testIsFileModified()
    {
        $this->state->setFileState('/file/path', 'hash');

        static::assertTrue($this->state->isFileModified('foobar', 'hash'));
        static::assertTrue($this->state->isFileModified('/file/path', 'foobar'));
        static::assertFalse($this->state->isFileModified('/file/path', 'hash'));
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new BooleanArgumentFlag());
        $rule     = new BooleanArgumentFlag();
        $nodeInfo = new NodeInfo('/file/path', 'namespace', 'className', 'methodName', 'functionName', 123, 456);
        $metric   = array('line' => 100);

        $ruleViolation = new RuleViolation($rule, $nodeInfo, 'violation', $metric);
        $this->state->setFileState('/file/path', 'hash');
        $this->state->addRuleViolation('/file/path', $ruleViolation);

        $expected = array(
            'key'   => array('strict' => true, 'rules' => array(), 'composer' => array(), 'phpVersion' => 123),
            'state' => array(
                'files' => array(
                    '/file/path' => array(
                        'hash'       => 'hash',
                        'violations' => array(
                            array(
                                'rule'          => 'PHPMD\Rule\CleanCode\BooleanArgumentFlag',
                                'namespaceName' => 'namespace',
                                'className'     => 'className',
                                'methodName'    => 'methodName',
                                'functionName'  => 'functionName',
                                'beginLine'     => 123,
                                'endLine'       => 456,
                                'description'   => 'violation',
                                'args'          => null,
                                'metric'        => $metric
                            )
                        )
                    )

                )
            )
        );

        static::assertSame($expected, $this->state->toArray());
    }
}
