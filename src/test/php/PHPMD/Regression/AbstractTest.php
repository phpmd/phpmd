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

namespace PHPMD\Regression;

/**
 * Regression test for issue 001.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractTest extends \PHPMD\AbstractTest
{
    /**
     * Creates a full filename for a test content in the <em>_files</b> directory.
     *
     * @param string $localPath The local path within the <em>_files</b> dir.
     *
     * @return string
     */
    protected static function createFileUri($localPath = '')
    {
        $trace = debug_backtrace();

        $ticket = '';
        if (preg_match('(\D(\d+)Test$)', $trace[1]['class'], $match)) {
            $ticket = $match[1];
        }

        if ($localPath === '') {
            $localPath = $trace[1]['function'] . '.php';
        }
        return parent::createFileUri('Regression/' . $ticket . '/' . $localPath);
    }
}
