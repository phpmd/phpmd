=============
Release 1.1.0
=============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document describes the news features and bugfixes of the
               next feature release 1.1.0 of PHPMD. This version provides two
               new rules, one that utilizes the CBO metric to detect strong
               coupled classes and a second on that detects the usage of PHP's
               goto statement. Additionally this release closes a minor bug
               in the LongVariable rule.
:Keywords:     Release, Version, Features, Bugfixes, Coupling Between Objects, CBO, Goto Statement, PHPUnit

Version 1.1.0 of PHPMD was released on March the 20th 2011. The key features
for this release were two new rules. The first one utilizes the `Coupling
Between Objects (CBO)`__ metric to detect strongly coupled classes. The second
one detects the usage of PHP's questionable ``goto`` statement. Beside that
we have closed a minor bug in the LongVariable rule, where also private
properties with descriptive names were reported. And finally we have replaced
deprecated PHPUnit features in the PHPMD's test suite, so that PHPMD's tests
should now work with PHPUnit 3.4.x and 3.5.x without deprecated warnings.

Features
--------

- Implemented `#10474873`__: Add rule for PHP's goto statement. Implemented
  with commit `#2745a20`__.
- Implemented `#10474987`__: Implement rule for CBO metric. Implemented with
  commit `#14277b4`__.
- Implemented `#11012465`__: Replace deprecated PHPUnit features in test suite.
  Implemented with commit `#4adb88d`__.

Bugfixes
--------

- Fixed `#10096717`__: LongVariable rule should not apply on private
  properties. Fixed with commit `#f063bc9`__.

Download
--------

You can download release 1.1.0 through PHPMD's `PEAR Channel Server`__ or you
can download the release as a `Phar archive`__

__ http://pdepend.org/documentation/software-metrics/coupling-between-objects.html
__ https://www.pivotaltracker.com/story/show/10474873
__ https://github.com/phpmd/phpmd/commit/2745a20
__ https://www.pivotaltracker.com/story/show/10474987
__ https://github.com/phpmd/phpmd/commit/14277b4
__ https://www.pivotaltracker.com/story/show/11012465
__ https://github.com/phpmd/phpmd/commit/4adb88d
__ https://www.pivotaltracker.com/story/show/10096717
__ https://github.com/phpmd/phpmd/commit/f063bc9
__ http://pear.phpmd.org
__ http://static.phpmd.org/php/1.1.0/phpmd.phar