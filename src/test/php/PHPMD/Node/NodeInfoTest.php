<?php

namespace PHPMD\Node;

use PHPMD\AbstractTest;

/**
 * @coversDefaultClass \PHPMD\Node\NodeInfo
 */
class NodeInfoTest extends AbstractTest
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $nodeInfo = new NodeInfo(
            '/file/path',
            'namespace',
            'className',
            'methodName',
            'functionName',
            123,
            456
        );
        static::assertSame('/file/path', $nodeInfo->fileName);
        static::assertSame('namespace', $nodeInfo->namespaceName);
        static::assertSame('className', $nodeInfo->className);
        static::assertSame('methodName', $nodeInfo->methodName);
        static::assertSame('functionName', $nodeInfo->functionName);
        static::assertSame(123, $nodeInfo->beginLine);
        static::assertSame(456, $nodeInfo->endLine);
    }
}
