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

/**
 * @link https://github.com/phpmd/phpmd/issues/110
 */
class testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo
{

    public function foo()
    {
        $this
            ->bar()
            ->baz()
            ->baw();
    }

    public function abc()
    {
        $this
            ->bar()
            ->baz();
        $this->baw();
    }

    public function xyz()
    {
        $this
            ->bar()
            ->baz()
            ->baw()
            ->bar()
            ->baz()
            ->baw()
            ->bar()
            ->baz()
            ->baw();
    }

    /**
     * @return $this
     */
    private function bar()
    {
        // Do some stuff ...
        return $this;
    }

    /**
     * @return $this
     */
    private function baz()
    {
        // Do some stuff ...
        return $this;
    }

    /**
     * @return $this
     */
    private function baw()
    {
        // Do some stuff ...
        return $this;
    }
}
