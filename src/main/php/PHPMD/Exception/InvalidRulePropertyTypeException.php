<?php

declare(strict_types=1);

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

use RuntimeException;
use Throwable;

/**
 * This type of exception is thrown when the class file for a configured rule
 * does not exist within php's include path.
 */
class InvalidRulePropertyTypeException extends RuntimeException
{
    public function __construct(string $class, string $key, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Invalid type for $class::\$$key: $message", $code, $previous);
    }
}
