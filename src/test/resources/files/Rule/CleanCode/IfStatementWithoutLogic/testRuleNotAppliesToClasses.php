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

namespace PHPMDTest;

class testRuleNotAppliesToClasses
{
    const FOO = 'foo';

    private $bar = 'bar';

    private $baz = 'baz';

    public function testRuleNotAppliesToClasses()
    {
        if (self::FOO == rand()) {
            // ...
        }
        if ('' === time() || static::FOO >> 1) {
            // ...
        }
        if ('100' ^ $this->bar == $this->bar . 'baz') {
            // ...
        }
        if (__LINE__ . 'foo' === $this->bar) {
            // ...
        }
        if (!static::FOO) {
            // ...
        }
        if ($this->bar) {
            // ...
        }
        if (self::FOO) {
            // ...
        }
        if (!self::FOO) {
            // ...
        }
        if (testRuleNotAppliesToClasses::FOO) {
            // ...
        }
        if ($this->getBaz()) {
            // ...
        }
        if (array()) {
            // ...
        }
    }

    private function getBaz()
    {
        return $this->baz;
    }
}
