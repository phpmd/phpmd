@echo off
REM This file is part of PHP Mess Detector.
REM
REM Copyright (c) Manuel Pichler <mapi@phpmd.org>.
REM All rights reserved.
REM
REM Licensed under BSD License
REM For full copyright and license information, please see the LICENSE file.
REM Redistributions of files must retain the above copyright notice.
REM
REM @author Manuel Pichler <mapi@phpmd.org>
REM @copyright Manuel Pichler. All rights reserved.
REM @license https://opensource.org/licenses/bsd-license.php BSD License
REM @link http://phpmd.org/

if "%PHPBIN%" == "" set PHPBIN=@php_bin@
if not exist "%PHPBIN%" if "%PHP_PEAR_PHP_BIN%" neq "" goto USE_PEAR_PATH
GOTO RUN
:USE_PEAR_PATH
set PHPBIN=%PHP_PEAR_PHP_BIN%
:RUN
"%PHPBIN%" "@bin_dir@\phpmd" %*
