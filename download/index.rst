=========
Downloads
=========

Installing as a Phar
====================

You can always fetch the latest stable version as a Phar archive through
the following version agnostic link: ::

  ~ $ wget -c https://phpmd.org/static/latest/phpmd.phar

The Phar files of *PHPMD* are signed with a public key associated to ``pgp@phpmd.org.``.
The `key(s) associated with this E-Mail address`__ can be queried at `keys.openpgp.org`__.

Installing using PHIVE
======================

Alternatively, **PHPMD** can be installed through `PHAR Installation and Verification Environment (PHIVE)`__.
After `installing PHIVE`__,**PHPMD** can be installed using the following command: ::

  php phive.phar install phpmd
  Phive 0.15.2 - Copyright (C) 2015-2023 by Arne Blankerts, Sebastian Heuer and Contributors
  Fetching repository list
  Downloading key 9093F8B32E4815AA
  Trying to connect to keys.openpgp.org (37.218.245.50)
  Successfully downloaded key.

          Fingerprint: E7A7 4510 2ECC 980F 7338 B307 9093 F8B3 2E48 15AA

          PHPMD (PHP Mess Detector) <pgp@phpmd.org>

          Created: 2023-09-15

  Import this key? [y|N] y
    Linking Y:\\.phive\phars/phpmd-2.14.1.phar to /path/to/your/project/tools/phpmd.bat

There are alternative `commands for PHIVE`__.

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

__ https://keys.openpgp.org/search?q=pgp%40phpmd.org
__ https://keys.openpgp.org/
__ https://phar.io
__ https://phar.io/#Install
__ https://phar.io/#Usage
__ http://getcomposer.org/composer.phar
__ https://github.com/phpmd/phpmd
__ http://getcomposer.org/composer.phar
__ http://pdepend.org
__ http://php.net/downloads.php
