=========
Downloads
=========

Installing as a Phar
====================

You can always fetch the latest stable version as a phar archive through
the following version agnostic link: ::

  ~ $ wget -c https://phpmd.org/static/latest/phpmd.phar

Installing via Composer
=======================

Create a ``composer.json`` file in your project directory and add *PHPMD*
as a required dependency: ::

  {
      "require-dev": {
          "phpmd/phpmd" : "@stable"
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

If you like to participate on the social coding platform `GitHub`__,
you can use PHPMD's mirror to fork and contribute to PHPMD. ::

  ~ $ git clone git://github.com/phpmd/phpmd.git

Then ``cd`` into the checkout directory initialize the referenced sub modules: ::

  ~ $ cd phpmd
  ~/phpmd $ git submodule update --init

Then install Composer in your project (or `download the composer.phar`__
directly): ::

  ~ $ curl -s http://getcomposer.org/installer | php

And finally let Composer install the project dependencies: ::

  ~ $ php composer.phar install

Requirements
============

PHPMD itself is considered as an early development version at its
current state. It relies on the following software products:

- `PHP_Depend >= 2.0.0`__
- `PHP >= 5.3.9`__

__ https://github.com/phpmd/phpmd
__ http://getcomposer.org/composer.phar
__ http://pdepend.org
__ http://php.net/downloads.php
