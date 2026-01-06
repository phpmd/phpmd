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

class testRuleDoesNotApplyForMagicMethods
{
    public function __construct()
    {
    }

    public function __debugInfo()
    {
    }

    public function __destruct()
    {
    }

    public function __call($name, $arguments)
    {
    }

    public function __clone()
    {
    }

    public function __get($name)
    {
    }

    public function __serialize(): array
    {
    }

    public function __invoke()
    {
    }

    public function __isset($name)
    {
    }

    public function __set($name, $value)
    {
    }

    public function __sleep()
    {
    }

    public function __toString()
    {
        return '';
    }

    public function __unserialize(array $data): void
    {
    }

    public function __unset($name)
    {
    }

    public function __wakeup()
    {
    }

    public static function __callStatic($name, $arguments)
    {
    }

    public static function __set_state($an_array)
    {
    }

    public function __notAllowed()
    {
    }
}
