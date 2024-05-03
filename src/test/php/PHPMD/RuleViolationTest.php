<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 *
 * @link http://phpmd.org/
 */

namespace PHPMD;

use PHPMD\Node\NodeInfo;

/**
 * Test case for the {@link \PHPMD\RuleViolation} class.
 *
 * @covers \PHPMD\RuleViolation
 *
 * @since 0.2.5
 */
class RuleViolationTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testNodeInfoGetters()
    {
        $rule = $this->getRuleMock();

        $nodeInfo = new NodeInfo('fileName', 'namespace', 'className', 'methodName', 'functionName', 123, 456);

        $violation = new RuleViolation($rule, $nodeInfo, 'foo');
        static::assertSame('fileName', $violation->getFileName());
        static::assertSame('namespace', $violation->getNamespaceName());
        static::assertSame('className', $violation->getClassName());
        static::assertSame('methodName', $violation->getMethodName());
        static::assertSame('functionName', $violation->getFunctionName());
        static::assertSame(123, $violation->getBeginLine());
        static::assertSame(456, $violation->getEndLine());
    }
}
