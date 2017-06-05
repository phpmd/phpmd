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

function global_function_duplicate()
{
    return 'foo';
}

class testRuleAppliesWhenKeyIsDeclaredInNonStandardWay extends testRuleAppliesWhenKeyIsDeclaredInNonStandardWayParent
{
    const DUPLICATED_KEY = 'foo';

    static $classStaticPropertyDuplicate = 'foo';

    private $classPrivatePropertyDuplicate = 'foo';

    protected $classProtectedPropertyDuplicate = 'foo';

    public $classPublicPropertyDuplicate = 'foo';

    private static $classPrivateStaticPropertyDuplicate = 'foo';

    public function testRuleAppliesWhenKeyIsDeclaredInNonStandardWay()
    {
        if (!defined('GLOBAL_DUPLICATE')) {
            define('GLOBAL_DUPLICATE', 'foo');
        }

        $foo = 'foo';
        $baz = $this->getDuplicatedName();

        return array(
            // not applied - comment
            0 => 'b/ar', // not applied - first occurrence
            false => 'bar', // applied - shallow cast to integer 0
            1 => 'bar', // not applied - first occurrence
            true => 'bar', // applied - shallow cast to integer 1
            '' => 'bar', // not applied - first occurrence
            null => 'bar', // applied - shallow cast to empty string
            'foo' => 'bar', // not applied - first occurrence
            'f' . 'o' . 'o' => 'bar', // not applied - resolves in string 'foo' (to be implemented?)
            'f' . 'o' . (14 * 1) || 1 . 'o' => 'bar', // not applied - resolves in string 'foo' (to be implemented?)
            self::DUPLICATED_KEY => 'bar', // not applied (to be implemented?)
            parent::DUPLICATED_KEY => 'bar', // not applied (to be implemented?)
            static::DUPLICATED_KEY => 'bar', // not applied (to be implemented?)
            self::INTERFACE_DUPLICATED_KEY => 'bar', // not applied (to be implemented?)
            parent::INTERFACE_DUPLICATED_KEY => 'bar', // not applied (to be implemented?)
            static::INTERFACE_DUPLICATED_KEY => 'bar', // not applied (to be implemented?)
            $foo => 'bar', // not applied - resolving variables is impossible in this context
            $baz => 'bar', // not applied - see above
            $this->getDuplicatedName() => 'bar', // not applied - resolving variable depends on inheritance tree
            self::$classStaticPropertyDuplicate => 'bar', // not applied - static property may be modified externally
            self::$classPrivateStaticPropertyDuplicate, // not applied - see above
            $this->classPrivatePropertyDuplicate => 'bar', // not applied - may be modified at any time
            $this->classProtectedPropertyDuplicate => 'bar', // not applied - see above
            $this->classPublicPropertyDuplicate => 'bar', // not applied - see above
            global_function_duplicate() => 'bar', // not applied - may be different depending on namespace
            GLOBAL_DUPLICATE => 'bar', // not applied - may be different depending on context
            "foo" => 'bar', // applied - checkpoint
        );
    }

    /**
     * @return string
     */
    public function getDuplicatedName()
    {
        return 'foo';
    }
}

interface testRuleAppliesWhenKeyIsDeclaredInNonStandardWayInterface
{
    const INTERFACE_DUPLICATED_KEY = 'foo';
}

abstract class testRuleAppliesWhenKeyIsDeclaredInNonStandardWayAbstract
    implements testRuleAppliesWhenKeyIsDeclaredInNonStandardWayInterface
{
    const DUPLICATED_KEY = 'foo';
}

class testRuleAppliesWhenKeyIsDeclaredInNonStandardWayParent
    extends testRuleAppliesWhenKeyIsDeclaredInNonStandardWayAbstract
{
    const DUPLICATED_KEY = 'foo';
}
