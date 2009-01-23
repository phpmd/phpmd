<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/pmd
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Abstract base class for PHP_PMD test cases.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
 */
abstract class PHP_PMD_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directory with test files.
     *
     * @var string $_filesDirectory
     */
    private static $_filesDirectory = null;

    /**
     * This method initializes the test environment, it configures the files
     * directory and sets the include_path for svn versions.
     *
     * @return void
     */
    public static function init()
    {
        self::$_filesDirectory = dirname(__FILE__);

        if (strpos('@package_version@', '@package_version') === false) {
            return;
        }

        // Configure include path
        $include = realpath(dirname(__FILE__) . '/../../../');
        set_include_path(get_include_path() . PATH_SEPARATOR . $include);
    }

    /**
     * Creates a full filename for a test content in the <em>_files</b> directory.
     *
     * @param string $localPath The local path within the <em>_files</b> dir.
     *
     * @return string
     */
    protected static function createFileUri($localPath)
    {
        return self::$_filesDirectory . '/' . $localPath;
    }
}

/**
 * Init the test environment.
 */
PHP_PMD_AbstractTest::init();
?>
