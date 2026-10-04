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

class testRuleDoesNotApplyToStaticPropertiesFromParentClass extends testRuleDoesNotApplyToStaticPropertiesFromParentClassParent
{
    public function testRuleDoesNotApplyToStaticPropertiesFromParentClass($key)
    {
        return self::$fromParent[$key] + static::$fromParentTrait[$key] + self::$fromGrandParent[$key];
    }
}

class testRuleDoesNotApplyToStaticPropertiesFromParentClassParent extends testRuleDoesNotApplyToStaticPropertiesFromParentClassGrandParent
{
    use testRuleDoesNotApplyToStaticPropertiesFromParentClassTrait;

    protected static array $fromParent = [];
}

class testRuleDoesNotApplyToStaticPropertiesFromParentClassGrandParent
{
    public static array $fromGrandParent = [];
}

trait testRuleDoesNotApplyToStaticPropertiesFromParentClassTrait
{
    protected static array $fromParentTrait = [];
}
