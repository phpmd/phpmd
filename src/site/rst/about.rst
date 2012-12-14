=========================
PHPMD - PHP Mess Detector
=========================

This is the project site of *PHPMD*. It is a spin-off project of
`PHP Depend`__ and aims to be a PHP equivalent of the well known
Java tool `PMD`__. PHPMD can be seen as an user friendly and easy
to configure frontend for the raw metrics measured by PHP Depend.

__ http://pdepend.org
__ http://pmd.sourceforge.net

What PHPMD does is: It takes a given PHP source code base and look 
for several potential problems within that source. These problems
can be things like:

- Possible bugs
- Suboptimal code
- Overcomplicated expressions
- Unused parameters, methods, properties

PHPMD is a young project and so it only provides a limited set of
pre defined rules, compared with it's brother PMD, that detect code
smells and possible errors within the analyzed source code. Checkout
the `rules section`__ to learn more about all implemented rules.

__ /rules/index.html

Recent Releases
===============

- 2012/12/14 - From now on we will use GitHub's `issue tracker`__ as our main
  ticket solution. The old PivotalTracker will not be used anymore.
- 2012/12/14 - PHPMD 1.4.1: This `release`__ integrates some pull requests from
  GitHub. Additionally it closes some long pending bugs.
- 2012/09/08 - PHPMD 1.4.0: This `release`__ integrates several longer pending
  pull requests and smaller bugfixes. One major addition is support for Composer
  as distribution channel.

- 2012/02/25 - PHPMD 1.3.2; This `release`__ closes a minor issue in PHPMD
  related to the Suhosin patch and ``memory_limit``.

- 2012/02/04 - PHPMD 1.3.0: This `release`__ depends on the latest PHP_Depend
  version 1.0.0.

- 2012/01/27 - PHPMD 1.2.1: New bugfix `release`__ of PHPMD that fixes several
  minor issues in PHPMD. Additionally we have updated to a more recent 
  PHP_Depend version.

__ https://github.com/phpmd/phpmd/issues
__ /download/release/1.4.1/changelog.html
__ /download/release/1.4.0/changelog.html
__ /download/release/1.3.2/changelog.html
__ /download/release/1.3.0/changelog.html
__ /download/release/1.2.1/changelog.html
