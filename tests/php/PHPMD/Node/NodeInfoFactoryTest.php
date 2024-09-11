<?php

namespace PHPMD\Node;

use PHPMD\AbstractTestCase;

/**
 * @coversDefaultClass \PHPMD\Node\NodeInfoFactory
 */
class NodeInfoFactoryTest extends AbstractTestCase
{
    /**
     * @covers ::fromNode
     */
    public function testFromNodeForAbstractTypeNode(): void
    {
        $node = $this->getMockBuilder(AbstractTypeNode::class)->disableOriginalConstructor()->getMock();
        $node->method('getName')->willReturn('className');
        $node->method('getFileName')->willReturn('/file/path');
        $node->method('getNamespaceName')->willReturn('namespace');
        $node->method('getBeginLine')->willReturn(123);
        $node->method('getEndLine')->willReturn(456);

        $nodeInfo = NodeInfoFactory::fromNode($node);
        static::assertSame('/file/path', $nodeInfo->fileName);
        static::assertSame('namespace', $nodeInfo->namespaceName);
        static::assertSame('className', $nodeInfo->className);
        static::assertNull($nodeInfo->methodName);
        static::assertNull($nodeInfo->functionName);
        static::assertSame(123, $nodeInfo->beginLine);
        static::assertSame(456, $nodeInfo->endLine);
    }

    /**
     * @covers ::fromNode
     */
    public function testFromNodeForMethodNode(): void
    {
        $node = $this->getMockBuilder(MethodNode::class)->disableOriginalConstructor()->getMock();
        $node->method('getName')->willReturn('methodName');
        $node->method('getParentName')->willReturn('className');
        $node->method('getFileName')->willReturn('/file/path');
        $node->method('getNamespaceName')->willReturn('namespace');
        $node->method('getBeginLine')->willReturn(123);
        $node->method('getEndLine')->willReturn(456);

        $nodeInfo = NodeInfoFactory::fromNode($node);
        static::assertSame('/file/path', $nodeInfo->fileName);
        static::assertSame('namespace', $nodeInfo->namespaceName);
        static::assertSame('className', $nodeInfo->className);
        static::assertSame('methodName', $nodeInfo->methodName);
        static::assertNull($nodeInfo->functionName);
        static::assertSame(123, $nodeInfo->beginLine);
        static::assertSame(456, $nodeInfo->endLine);
    }

    /**
     * @covers ::fromNode
     */
    public function testFromNodeForFunctionNode(): void
    {
        $node = $this->getMockBuilder(FunctionNode::class)->disableOriginalConstructor()->getMock();
        $node->method('getName')->willReturn('functionName');
        $node->method('getFileName')->willReturn('/file/path');
        $node->method('getNamespaceName')->willReturn('namespace');
        $node->method('getBeginLine')->willReturn(123);
        $node->method('getEndLine')->willReturn(456);

        $nodeInfo = NodeInfoFactory::fromNode($node);
        static::assertSame('/file/path', $nodeInfo->fileName);
        static::assertSame('namespace', $nodeInfo->namespaceName);
        static::assertNull($nodeInfo->className);
        static::assertNull($nodeInfo->methodName);
        static::assertSame('functionName', $nodeInfo->functionName);
        static::assertSame(123, $nodeInfo->beginLine);
        static::assertSame(456, $nodeInfo->endLine);
    }
}
