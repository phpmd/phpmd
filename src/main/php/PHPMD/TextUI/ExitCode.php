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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD\TextUI;

/**
 * Exit codes used by the PHPMD command line tool.
 */
enum ExitCode: int
{
    /** When no exception was thrown, no violation was found and no error occurred */
    case Success = 0;

    /** When an exception was thrown */
    case Exception = 1;

    /** When at least one violation was found, the ignore-violations-on-exit option was not enabled and the base-line mode is disabled */
    case Violation = 2;

    /** When an error occurred and the ignore-errors-on-exit CLI option was not enabled */
    case Error = 3;
}
