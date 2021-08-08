PHPMD
=====

PHPMD is a spin-off project of PHP Depend and aims to be a PHP equivalent of the well known Java tool PMD. PHPMD can be seen as an user friendly frontend application for the raw metrics stream measured by PHP Depend.

https://phpmd.org

.. image:: https://poser.pugx.org/phpmd/phpmd/v/stable.svg
   :target: https://packagist.org/packages/phpmd/phpmd
   :alt: Latest Stable Version

.. image:: https://poser.pugx.org/phpmd/phpmd/license.svg
   :target: https://packagist.org/packages/phpmd/phpmd
   :alt: License

.. image:: https://travis-ci.org/phpmd/phpmd.svg?branch=master
   :target: https://travis-ci.org/phpmd/phpmd
   :alt: Travis Build Status

.. image:: https://ci.appveyor.com/api/projects/status/pc08owbun2y00kwk?svg=true
   :target: https://ci.appveyor.com/project/phpmd/phpmd
   :alt: AppVeyor Build Status

.. image:: https://scrutinizer-ci.com/g/phpmd/phpmd/badges/build.png?b=master
   :target: https://scrutinizer-ci.com/g/phpmd/phpmd/build-status/master
   :alt: Scrutinizer Build Status

.. image:: https://scrutinizer-ci.com/g/phpmd/phpmd/badges/quality-score.png?b=master
   :target: https://scrutinizer-ci.com/g/phpmd/phpmd/?branch=master
   :alt: Scrutinizer Code Quality

.. image:: https://badges.gitter.im/phpmd/community.svg
   :target: https://gitter.im/phpmd/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge
   :alt: Chat with us on Gitter

.. image:: https://poser.pugx.org/phpmd/phpmd/d/monthly
   :target: https://packagist.org/packages/phpmd/phpmd
   :alt: Monthly downloads

.. image:: https://poser.pugx.org/phpmd/phpmd/downloads
   :target: https://packagist.org/packages/phpmd/phpmd
   :alt: Total downloads

Installation
------------

See https://phpmd.org/download/index.html

Command line usage
------------------

Type ``phpmd [filename|directory] [report format] [ruleset file]``, i.e: ::

  mapi@arwen ~ $ phpmd php/PDepend/DbusUI/ xml rulesets.xml

While the ``rulesets.xml`` ruleset file could look like this::

  <?xml version="1.0"?>
  <ruleset name="My first PHPMD rule set"
           xmlns="http://pmd.sf.net/ruleset/1.0.0"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0
                         http://pmd.sf.net/ruleset_xml_schema.xsd"
           xsi:noNamespaceSchemaLocation="
                         http://pmd.sf.net/ruleset_xml_schema.xsd">
    <description>
      My custom rule set that checks my code...
    </description>

    <rule ref="rulesets/codesize.xml" />
    <rule ref="rulesets/cleancode.xml" />
    <rule ref="rulesets/controversial.xml" />
    <rule ref="rulesets/design.xml" />
    <rule ref="rulesets/naming.xml" />
    <rule ref="rulesets/unusedcode.xml" />
  </ruleset>

The xml report would like like this::

  <?xml version="1.0" encoding="UTF-8" ?>
  <pmd version="0.0.1" timestamp="2009-12-19T22:17:18+01:00">
    <file name="/projects/pdepend/PHP/Depend/DbusUI/ResultPrinter.php">
      <violation beginline="81"
                 endline="81"
                 rule="UnusedFormalParameter"
                 ruleset="Unused Code Rules"
                 externalInfoUrl="https://phpmd.org/rules/unusedcode.html#unusedformalparameter"
                 priority="3">
        Avoid unused parameters such as '$builder'.
      </violation>
    </file>
  </pmd>

You can pass a file name or a directory name containing PHP source
code to PHPMD.

The `PHPMD Phar distribution`__ includes the rule set files inside
its archive, even if the "rulesets/codesize.xml" parameter above looks
like a filesystem reference.

__ https://phpmd.org/download/index.html

Command line options
--------------------

- Notice that the default output is in XML, so you can redirect it to
  a file and XSLT it or whatever

- You can also use shortened names to refer to the built-in rule sets,
  like this: ::

    phpmd PHP/Depend/DbusUI/ xml codesize

- The command line interface also accepts the following optional arguments:

  - ``--minimumpriority`` - The rule priority threshold; rules with lower
    priority than they will not be used.

  - ``--reportfile`` - Sends the report output to the specified file,
    instead of the default output target ``STDOUT``.

  - ``--suffixes`` - Comma-separated string of valid source code filename
    extensions, e.g. php,phtml.

  - ``--exclude`` - Comma-separated string of patterns that are used to ignore
    directories. Use asterisks to exclude by pattern. For example ``*src/foo/*.php`` or ``*src/foo/*``

  - ``--strict`` - Also report those nodes with a @SuppressWarnings annotation.

  - ``--ignore-errors-on-exit`` - will exit with a zero code, even on error.

  - ``--ignore-violations-on-exit`` - will exit with a zero code, even if any
    violations are found.

  - ``--generate-baseline`` - will generate a ``phpmd.baseline.xml`` for existing violations
    next to the ruleset definition file. The file paths of the violations will be relative to the current
    working directory.

  - ``--update-baseline`` - will remove all violations from an existing ``phpmd.baseline.xml``
    that no longer exist. New violations will _not_ be added. The file path of the violations will be relative
    to the current working directory.

  - ``--baseline-file`` - the filepath to a custom baseline xml file.

  An example command line: ::

    phpmd PHP/Depend/DbusUI xml codesize --reportfile phpmd.xml --suffixes php,phtml

Using multiple rule sets
````````````````````````

PHPMD uses so called rule sets that configure/define a set of rules which will
be applied against the source under test. The default distribution of PHPMD is
already shipped with a few default sets, that can be used out-of-box. You can
call PHPMD's cli tool with a set's name to apply this configuration: ::

  ~ $ phpmd /path/to/source text codesize

But what if you would like to apply more than one rule set against your source?
You can also pass a list of rule set names, separated by comma to PHPMD's cli
tool: ::

  ~ $ phpmd /path/to/source text codesize,unusedcode,naming

You can also mix custom `rule set files`__ with build-in rule sets: ::

  ~ $ phpmd /path/to/source text codesize,/my/rules.xml

__ https://phpmd.org/documentation/creating-a-ruleset.html

That's it. With this behavior you can specify you own combination of rule sets
that will check the source code.

Using multiple source files and folders
```````````````````````````````````````

PHPMD also allowes you to specify multiple source directories in case you want
to create one output for certain parts of your code ::

 ~ $ phpmd /path/to/code,index.php,/another/place/with/code text codesize

Exit codes
----------

PHPMD's command line tool currently defines four different exit codes.

- *0*, This exit code indicates that everything worked as expected. This means
  there was no error/exception and PHPMD hasn't detected any rule violation
  in the code under test.
- *1*, This exit code indicates that an exception occurred which has
  interrupted PHPMD during execution.
- *2*, This exit code means that PHPMD has processed the code under test
  without the occurrence of an error/exception, but it has detected rule
  violations in the analyzed source code. You can also prevent this behaviour
  with the ``--ignore-violations-on-exit`` flag, which will result to a *0*
  even if any violations are found.
- *3*, This exit code means that one or multiple files under test could not
   be processed because of an error. There may also be violations in other
   files that could be processed correctly.

Renderers
---------

At the moment PHPMD comes with the following renderers:

- *xml*, which formats the report as XML.
- *text*, simple textual format.
- *html*, single HTML file with possible problems.
- *json*, formats JSON report.
- *ansi*, a command line friendly format.
- *github*, a format that GitHub Actions understands.
- *sarif*, the Static Analysis Results Interchange Format.
- *checkstyle*, language and tool agnostic XML format

Baseline
--------

For existing projects a violation baseline can be generated. All violations in this baseline will be ignored in further inspections.

The recommended approach would be a ``phpmd.xml`` in the root of the project. To generate the ``phpmd.baseline.xml`` next to it::

  ~ $ phpmd /path/to/source text phpmd.xml --generate-baseline

To specify a custom baseline filepath for export::

  ~ $ phpmd /path/to/source text phpmd.xml --generate-baseline --baseline-file /path/to/source/phpmd.baseline.xml

By default PHPMD will look next to ``phpmd.xml`` for ``phpmd.baseline.xml``. To overwrite this behaviour::

  ~ $ phpmd /path/to/source text phpmd.xml --baseline-file /path/to/source/phpmd.baseline.xml

To clean up an existing baseline file and *only remove* no longer existing violations::

  ~ $ phpmd /path/to/source text phpmd.xml --update-baseline

PHPMD for enterprise
--------------------

Available as part of the Tidelift Subscription.

The maintainers of ``PHPMD`` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. `Learn more.`__

__ https://tidelift.com/subscription/pkg/packagist-phpmd-phpmd?utm_source=packagist-phpmd-phpmd&utm_medium=referral&utm_campaign=enterprise&utm_term=repo
