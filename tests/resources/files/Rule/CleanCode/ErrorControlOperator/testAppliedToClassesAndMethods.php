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

/**
 * Class testAppliedToClassesAndMethods
 */
class testAppliedToClassesAndMethods
{
    /**
     * @var string
     */
    private $baz = 'baz';

    /**
     * testAppliedToClassesAndMethods
     */
    public function testAppliedToClassesAndMethods()
    {
        $foo = @$this->fooBar();
        ++$foo;
        @!$baz = 1 / 0;
        if (@is_readable(__FILE__)) {
            $bar = new DateTime('now');
            @$baz = $bar;
        }
        $this->baz = !$foo;
    }

    /**
     * fooBar
     *
     * @return int
     */
    private function fooBar()
    {
        @$foo = $this->baz / 0;
        @$baz = !$foo;

        return 2;
    }
}
