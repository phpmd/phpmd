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

.. image:: https://codecov.io/gh/phpmd/phpmd/branch/master/graph/badge.svg?token=XrBrvTLJeE
   :target: https://codecov.io/gh/phpmd/phpmd
   :alt: Codecov Status

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

Type ``phpmd analyze [options] [--] [<paths>...]``, i.e: ::

  ~ $ phpmd analyze src/

If no ruleset is specified, PHPMD will look for a configuration file in the
current directory. The following file names are detected automatically (in
order of priority): ``phpmd.yml``, ``phpmd.yaml``, ``phpmd.json``,
``phpmd.xml``, ``phpmd.php``, as well as their ``.``-prefixed and
``.dist``-suffixed variants (e.g. ``.phpmd.yml`` or ``phpmd.yml.dist``).

A ``phpmd.yml`` rule set file could look like this:

.. code:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  exclude-pattern:
    - "*/vendor/*"
  rules:
    - ref: rulesets/codesize.xml
    - ref: rulesets/cleancode.xml
    - ref: rulesets/controversial.xml
    - ref: rulesets/design.xml
    - ref: rulesets/naming.xml
    - ref: rulesets/unusedcode.xml

Rule sets can also be written in XML, JSON, or PHP. See the
`creating a custom rule set </documentation/creating-a-ruleset.html>`_
documentation for details on all supported formats.

You can pass a comma-separated string with list of file names
or a directory names, containing PHP source code to PHPMD.

The `PHPMD Phar distribution`__ includes the rule set files inside
its archive, even if the "rulesets/codesize.xml" parameter above looks
like a filesystem reference.

__ https://phpmd.org/download/index.html

Command line options
--------------------

- The default output format is ``text``. You can change it with the ``--format`` option.

- You can also use shortened names to refer to the built-in rule sets,
  like this: ::

    phpmd analyze --ruleset codesize src/

- The command line interface also accepts the following optional arguments:

  - ``--verbose, -v, -vv, -vvv`` - The output verbosity level. Will print more information
    what is being processed or cached. Will be send to ``STDERR`` to not interfere
    with report output. ``text`` output will also have under each error a link
    to the documentation of the rule and format the location in a way that most
    IDEs will convert into a link to open the file at the line of the error
    when clicked.

  - ``--minimum-priority`` - The rule priority threshold; rules with lower
    priority than this will not be used.

  - ``--maximum-priority`` - The rule priority threshold; rules with higher
    priority than this will not be used.

  - ``--reportfile-text``, ``--reportfile-xml``, ``--reportfile-html``, etc. - Sends the report output
    to the specified file. Multiple report files in different formats can be written simultaneously.

  - ``--suffixes`` - Comma-separated string of valid source code filename
    extensions, e.g. php,phtml.

  - ``--exclude`` - Comma-separated string of patterns that are used to ignore
    directories. Use asterisks to exclude by pattern. For example ``*src/foo/*.php`` or ``*src/foo/*``.
    Exclude patterns can also be configured via ``exclude-pattern`` in your rule sets.

  - ``--strict`` - Also report those nodes with a ``#[SuppressWarnings]`` attribute.

  - ``--not-strict`` - Does not report those nodes with a ``#[SuppressWarnings]`` attribute (default).

  - ``--ignore-errors-on-exit`` - will exit with a zero code, even on error.

  - ``--ignore-violations-on-exit`` - will exit with a zero code, even if any
    violations are found.

  - ``--cache`` - will enable the result cache. Will default to ``.phpmd.result-cache.php`` in the
    current working directory.

  - ``--cache-file`` - in cooperation with ``--cache`` will override the default result cache file path of
    ``.phpmd.result-cache.php`` to the given file path.

  - ``--cache-strategy`` - sets the caching strategy to determine if a file is still fresh. Either
    `content` to base it on the file contents, or `timestamp` to base it on the file modified timestamp.

  - ``--generate-baseline`` - will generate a ``phpmd.baseline.xml`` for existing violations
    next to the ruleset definition file. The file paths of the violations will be relative to the current
    working directory.

  - ``--update-baseline`` - will remove all violations from an existing ``phpmd.baseline.xml``
    that no longer exist. New violations will _not_ be added. The file path of the violations will be relative
    to the current working directory.

  - ``--baseline-file`` - the filepath to a custom baseline xml file. If absent will
    default to ``phpmd.baseline.xml``

  - ``--bootstrap`` - an optional PHP script to load before running the analysis.

  - ``--input-file`` - a file containing a list of source paths to analyze (one per line).

  - ``--no-progress`` - do not show the progress bar, only the results.

  - ``--coverage`` - Clover style CodeCoverage report, as produced by PHPUnit's --coverage-clover
    option.

  - ``--color`` - enable color in output, for instance text renderer
    will show rule name in yellow and error description in red.
  - ``--extra-line-in-excerpt`` - specify how many extra lines are added to a code snippet in html format

  - ``--threads`` - the number of threads to use to parse the files.

  - ``--xdebug`` - will enable Xdebug for debugging PHP Mess Detector.

  An example command line: ::

    phpmd analyze --reportfile-text report.txt --suffixes php,phtml src/

  Options can be placed before or after arguments, and can be separated from
  their value with a space or an equal (``=``) sign. If you have paths starting
  with ``-``, place options first, then use ``--`` to mark the start of the
  arguments list: ::

    phpmd analyze --reportfile-text report.txt -- -foo/Folder

Using multiple rule sets
````````````````````````

PHPMD uses so called rule sets that configure/define a set of rules which will
be applied against the source under test. If you have a ``phpmd.yml`` in your
project root, it will be used automatically. You can also select a built-in
rule set explicitly: ::

  ~ $ phpmd analyze --ruleset codesize /path/to/source

If you would like to apply more than one rule set against your source, you can
pass the ``--ruleset`` option multiple times: ::

  ~ $ phpmd analyze --ruleset codesize --ruleset unusedcode --ruleset naming /path/to/source

You can also mix custom `rule set files`__ with built-in rule sets: ::

  ~ $ phpmd analyze --ruleset codesize --ruleset /my/rules.xml /path/to/source

__ https://phpmd.org/documentation/creating-a-ruleset.html

That's it. With this behavior you can specify you own combination of rule sets
that will check the source code.

Using multiple source files and folders
```````````````````````````````````````

PHPMD also allows you to specify multiple source directories in case you want
to create one output for certain parts of your code ::

 ~ $ phpmd analyze /path/to/code index.php /another/place/with/code

Or use a glob pattern: ::

  ~ $ phpmd analyze src/*/*{Renderer,Node}.php

Scan input
``````````

PHPMD can also read the standard input `stdin`: ::

  ~ $ cat src/MyService.php | phpmd analyze -

So the PHP code to be scanned may be generated by an other program
not necessarily to be store in file.

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
- *ansi*, colorful, formatted text for the command line.
- *html*, single HTML file with possible problems.
- *json*, formats JSON report.
- *github*, a format that GitHub Actions understands.
- *githubcheckruns*, JSON format for the GitHub Check Runs API.
- *gitlab*, a format that GitLab CI understands.
- *sarif*, the Static Analysis Results Interchange Format.
- *checkstyle*, language and tool agnostic XML format.

Baseline
--------

For existing projects a violation baseline can be generated. All violations in this baseline will be ignored in further inspections.

The recommended approach would be a rule set file (e.g. ``phpmd.yml`` or ``phpmd.xml``) in the root of the project. To generate the ``phpmd.baseline.xml`` next to it::

  ~ $ phpmd analyze --generate-baseline /path/to/source

To specify a custom baseline filepath for export::

  ~ $ phpmd analyze --generate-baseline --baseline-file /path/to/source/phpmd.baseline.xml /path/to/source

By default PHPMD will look next to your rule set file for ``phpmd.baseline.xml``. To overwrite this behaviour::

  ~ $ phpmd analyze --baseline-file /path/to/source/phpmd.baseline.xml /path/to/source

To clean up an existing baseline file and *only remove* no longer existing violations::

  ~ $ phpmd analyze --update-baseline /path/to/source

PHPMD for enterprise
--------------------

Available as part of the Tidelift Subscription.

The maintainers of ``PHPMD`` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. `Learn more.`__

__ https://tidelift.com/subscription/pkg/packagist-phpmd-phpmd?utm_source=packagist-phpmd-phpmd&utm_medium=referral&utm_campaign=enterprise&utm_term=repo

Contributing
------------

If you want to contribute to PHPMD, please consult the `contribution guide`__.

__ ./.github/CONTRIBUTING.md
