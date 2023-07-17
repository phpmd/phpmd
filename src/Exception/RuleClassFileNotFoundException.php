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

namespace PHPMD\Exception;

/**
 * This type of exception is thrown when the class file for a configured rule
 * does not exist within php's include path.
 */
class RuleClassFileNotFoundException extends \RuntimeException
{
    /**
     * Constructs a new class file not found exception.
     *
     * @param string $className The rule class name.
     */
    public function __construct($className)
    {
        parent::__construct('Cannot load source file for class: ' . $className);
    }
}
