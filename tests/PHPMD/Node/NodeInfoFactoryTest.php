<?php

namespace PHPMD\Node;

use PHPMD\AbstractTest;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @coversDefaultClass \PHPMD\Node\NodeInfoFactory
 */
class NodeInfoFactoryTest extends AbstractTest
{
    /**
     * @covers ::fromNode
     */
    public function testFromNodeForAbstractTypeNode()
    {
        /** @var AbstractTypeNode&MockObject $node */
        $node = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Node\AbstractTypeNode')->disableOriginalConstructor()
        );
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
    public function testFromNodeForMethodNode()
    {
        /** @var MethodNode&MockObject $node */
        $node = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Node\MethodNode')->disableOriginalConstructor()
        );
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
    public function testFromNodeForFunctionNode()
    {
        /** @var MethodNode&MockObject $node */
        $node = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Node\FunctionNode')->disableOriginalConstructor()
        );
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
