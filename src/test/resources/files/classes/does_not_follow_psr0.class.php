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
 * Some class that stands as an example for classes not following PSR-0.

 * @author Gerrit Addiks <gerrit@addiks.de>
 */
class some_class_that_does_not_follow_psr0 extends \PHPMD\AbstractRule{

    /**
     * A method that returnes foo, bar and baz.
     *
     * @return string
     */
    public function getFooBarBaz(){
        return array('foo', 'bar', 'baz');
    }

    public function apply(\PHPMD\AbstractNode $node){

    }

}
