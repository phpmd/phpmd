=========
Downloads
=========

PEAR Installer
==============

The preferred way to install PHPMD should be the `PEAR installer`__
and PHPMD's `PEAR channel`__, where you will always find the latest
stable version. Because PHPMD heavily relies on metrics measured 
with `PHP Depend`__ you must also discover this project's 
`PEAR Channel`__. Just enter: ::

  mapi@arwen ~ $ pear channel-discover pear.phpmd.org
  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install --alldeps phpmd/PHP_PMD

From the github repository
==========================

If you like to participate on the social coding plattform `GitHub`__,
you can use PHPMD's mirror to fork and contribute to PHPMD. ::

  mapi@arwen ~ $ git clone git://github.com/phpmd/phpmd.git

Requirements
============

PHPMD itself is considered as an early development version at its
current state. It relies on the following software products:

- `PHP_Depend >= 1.0.0`__
- `PHP >= 5.2.3`__

__ http://pear.php.net/manual/en/installation.php
__ http://pear.phpmd.org
__ http://pdepend.org
__ http://pear.pdepend.org
__ https://github.com/phpmd/phpmd
__ http://pdepend.org
__ http://php.net/downloads.php
