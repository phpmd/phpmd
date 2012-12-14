=========
Downloads
=========

Installing via Composer
=======================

Create a ``composer.json`` file in your project directory and add *PHPMD*
as a required dependency: ::

  {
      "require": {
          "phpmd/phpmd" : "1.4.0"
      }
  }

Then install Composer in your project (or `download the composer.phar`__
directly): ::

  ~ $ curl -s http://getcomposer.org/installer | php

And finally let Composer install the project dependencies: ::

  ~ $ php composer.phar install

__ http://getcomposer.org/composer.phar

From the github repository
==========================

If you like to participate on the social coding plattform `GitHub`__,
you can use PHPMD's mirror to fork and contribute to PHPMD. ::

  ~ $ git clone git://github.com/phpmd/phpmd.git

Then ``cd`` into the checkout directory initialize the referenced sub modules: ::

  ~ $ cd phpmd
  ~/phpmd $ git submodule update --init

This installs the build framework used by PHPMD. To initialize PHPMD's
requirements you should now invoke *Ant* with the ``initialize`` target: ::

  ~/phpmd $ ant initialize

This command installs the dependencies used by PHPMD. Please not that this
command will produce a lot of output on the shell.

Installing as a PEAR package
============================

The preferred way to install PHPMD should be the `PEAR installer`__
and PHPMD's `PEAR channel`__, where you will always find the latest
stable version. Because PHPMD heavily relies on metrics measured 
with `PHP Depend`__ you must also discover this project's 
`PEAR Channel`__. Just enter: ::

  ~ $ pear channel-discover pear.phpmd.org
  ~ $ pear channel-discover pear.pdepend.org
  ~ $ pear install --alldeps phpmd/PHP_PMD

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
