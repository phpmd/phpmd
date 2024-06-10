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
 * @link http://phpmd.org/
 */

namespace PHPMD\Node;

use PDepend\Source\AST\ASTFunction;
use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Test case for the function node implementation.
 *
 * @covers \PHPMD\Node\AbstractCallableNode
 * @covers \PHPMD\Node\FunctionNode
 */
class FunctionTest extends AbstractTestCase
{
    /**
     * testMagicCallDelegatesToWrappedPHPDependFunction
     * @throws Throwable
     */
    public function testMagicCallDelegatesToWrappedPHPDependFunction(): void
    {
        $function = $this->getMockBuilder(ASTFunction::class)->setConstructorArgs([null])->getMock();
        $function->expects(static::once())
            ->method('getStartLine');

        $node = new FunctionNode($function);
        $node->getStartLine();
    }
}
