=============
Release 1.0.1
=============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document describes the news features and bugfixes of the
               next feature release 1.0.1 of PHPMD.
:Keywords:     Release, Version, Features, Bugfixes

Version 1.0.1 of PHPMD was released on Februrary the 12th 2011. This release
closes two bugs in PHPMD.

Bugfixes
--------

- Fixed `#9930643`__: The include_path does not match with PHP_Depend's new
  directory layout. Fixed with commit `#531be78`__.
- Fixed `#9626017`__: Clear temporary resources after a test has finished.
  Fixed with commit `#b385f15`__.

Download
--------

You can download release 1.0.1 through PHPMD's `PEAR Channel Server`__ or you
can download the release as a `Phar archive`__

__ https://www.pivotaltracker.com/story/show/9930643
__ https://github.com/phpmd/phpmd/commit/531be78
__ https://www.pivotaltracker.com/story/show/9626017
__ https://github.com/phpmd/phpmd/commit/b385f15
__ http://pear.phpmd.org
__ http://static.phpmd.org/php/1.0.1/phpmd.phar