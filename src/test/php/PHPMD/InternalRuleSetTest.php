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

namespace PHPMD;

/**
 * Test case for the parser factory class.
 *
 * @covers \PHPMD\InternalRuleSet
 */
final class InternalRuleSetTest extends AbstractTestCase
{
    /**
     * testFactoryConfiguresInputDirectory
     */
    public function testGetNames(): void
    {
        self::assertSame([
            'cleancode',
            'codesize',
            'controversial',
            'design',
            'naming',
            'unusedcode',
        ], InternalRuleSet::getNames());
    }

    /**
     * testFactoryConfiguresInputFile
     */
    public function testFactoryConfiguresInputFile(): void
    {
        self::assertSame(
            'cleancode,codesize,controversial,design,naming,unusedcode',
            InternalRuleSet::getNamesConcatenated(),
        );
    }
}
