==================
Command line usage
==================

Type phpmd analyze [options] [--] [<paths>...], i.e: ::

  ~ $ phpmd analyze src/

If no ruleset is specified, PHPMD will look for a configuration file in the
current directory. The following file names are detected automatically (in
order of priority): ``phpmd.yml``, ``phpmd.yaml``, ``phpmd.json``,
``phpmd.xml``, ``phpmd.php``, as well as their ``.``-prefixed and
``.dist``-suffixed variants (e.g. ``.phpmd.yml`` or ``phpmd.yml.dist``).

See the `creating a custom rule set </documentation/creating-a-ruleset.html>`_
documentation for details on all supported configuration formats.

Creating a configuration file
-----------------------------

``phpmd init`` asks a few questions about your project (paths to analyze,
paths to exclude, whether to enable the result cache and which rule sets to
use) and writes the answers to a ``phpmd.yml``: ::

  ~ $ phpmd init

Run it with ``--no-interaction`` to accept the suggested settings, and pass
``--output`` to write the file somewhere else.

Migrating a configuration file
------------------------------

``phpmd migrate`` upgrades a configuration file written for PHPMD 2. It
replaces renamed rule classes and threshold properties by their current names
and converts the file to YAML: ::

  ~ $ phpmd migrate phpmd.xml

Without a file argument the auto-detected configuration file is migrated. The
original file is kept, so remove it once you have reviewed the result. The
command supports these options:

- ``--format`` - ``yml`` (the default) or ``json``.
- ``--output`` - where to write the migrated file, by default next to the
  original file with the extension of the chosen format.
- ``--preserve-behavior`` - lower the configured thresholds of rules that
  reported values equal to the threshold in PHPMD 2 by one, so the same values
  are still reported. See the
  `upgrade guide <https://github.com/phpmd/phpmd/blob/master/UPGRADING.md>`_.
- ``--dry-run`` - print the migrated configuration instead of writing it.
- ``--force`` - overwrite the output file if it already exists.

If the output path is the original file itself, the original is first backed
up with a ``.bak`` extension.

You can pass file or directory names, separated by spaces, containing PHP
source code to PHPMD.

The PHPMD Phar distribution includes the rule set files inside
its archive, even if the "rulesets/codesize.xml" parameter above looks
like a filesystem reference.

Command line options
====================

- The default output format is ``text``. You can change it with the ``--format`` option.

- You can also use shortened names to refer to the built-in rule sets,
  like this: ::

    phpmd analyze --ruleset codesize src/

- The command line interface also accepts the following optional arguments:

  - ``--verbose, -v, -vv, -vvv`` - The output verbosity level. Everything
    printed in addition to the report is sent to ``STDERR`` so that it does not
    interfere with the report output.

    - ``-v`` makes the ``text`` output show under each error a link to the
      documentation of the rule and format the location in a way that most
      IDEs will convert into a link to open the file at the line of the error
      when clicked.
    - ``-vv`` also prints the effective configuration before the analysis
      starts: the scanned paths, the exclude patterns, the file suffixes, the
      thread count, the strict mode, the baseline file, the loaded rule sets
      with their rule counts and the result cache status. The exit code is
      printed at the end.
    - ``-vvv`` additionally lists every argument and option the command
      received, every loaded rule with its priority, and the result cache
      decision for every file.

  - ``--minimum-priority`` - The rule priority threshold; rules with lower
    priority than this will not be used.
    Can also be configured via ``<minimum-priority>`` in the rule sets.

  - ``--maximum-priority`` - The rule priority threshold; rules with higher
    priority than this will not be used.
    Can also be configured via ``<maximum-priority>`` in the rule sets.

  - ``--reportfile-text``, ``--reportfile-xml``, ``--reportfile-html``, etc. - Sends the report output
    to the specified file. Multiple report files in different formats can be written simultaneously.

  - ``--suffixes`` - A valid source code filename extension, e.g. php or phtml.
    Repeat the option for several extensions: ``--suffixes php --suffixes phtml``.
    Can also be configured via ``<suffixes>`` in the rule sets.

  - ``--exclude`` - A pattern that is used to ignore files and directories. Use asterisks to
    exclude by pattern, for example ``*src/foo/*.php`` or ``*src/foo/*``. Repeat the option for
    several patterns: ``--exclude vendor --exclude '*Test.php'``. The patterns are added to the
    ``<exclude-pattern>`` entries of the rule sets and to the version control directories
    (``.git``, ``.svn``, ``CVS``, ``.bzr``, ``.hg``, ``SCCS``) which are always excluded.

  - ``--strict`` - Also report those nodes with a ``#[SuppressWarnings]`` attribute.

  - ``--no-strict`` - Does not report those nodes with a ``#[SuppressWarnings]`` attribute (default).

  - ``--ignore-errors-on-exit`` - will exit with a zero code, even on error.

  - ``--ignore-violations-on-exit`` - will exit with a zero code, even if any
    violations are found.

  - ``--cache`` - will enable the result cache. Will default to ``.phpmd.result-cache.php`` in the
    current working directory.
    Can also be configured via ``<cache>`` in the rule sets.

  - ``--cache-file`` - in cooperation with ``--cache`` will override the default result cache file path of
    ``.phpmd.result-cache.php`` to the given file path.
    Can also be configured via ``<cache-file>`` in the rule sets.

  - ``--cache-strategy`` - sets the caching strategy to determine if a file is still fresh. Either
    `content` to base it on the file contents, or `timestamp` to base it on the file modified timestamp.
    Can also be configured via ``<cache-strategy>`` in the rule sets.

  - ``--generate-baseline`` - will generate a ``phpmd.baseline.xml`` for existing violations
    next to the ruleset definition file. The file paths of the violations will be relative to the current
    working directory.

  - ``--update-baseline`` - will remove all violations from an existing ``phpmd.baseline.xml``
    that no longer exist. Violations that are not in the baseline are reported as usual and will
    _not_ be added. The file path of the violations will be relative to the current working directory.

  - ``--baseline-file`` - the filepath to a custom baseline xml file. If absent will
    default to ``phpmd.baseline.xml``
    Can also be configured via ``<baseline-file>`` in the rule sets.

  - ``--ansi`` / ``--no-ansi`` - force or disable color in output, for instance the text renderer
    will show rule name in yellow and error description in red. By default color is used when
    the output is a terminal.

  - ``--xdebug`` - will enable Xdebug for debugging PHP Mess Detector.

  - ``--bootstrap`` - an optional PHP script to load before running the analysis.
    Can also be configured via ``<bootstrap>`` in the rule sets.

  - ``--input-file`` - a file containing a list of source paths to analyze (one per line).

  - ``--progress`` / ``--no-progress`` - show or hide the progress bar. The bar is written to
    ``STDERR`` and shown by default unless the output is quiet. ``--progress`` forces it even
    together with ``--quiet`` or ``--silent``, so a script can hide the report and still watch
    the analysis advance.

  - ``--threads`` - the number of threads to use to parse the files. Defaults to the
    number of CPU cores.
    Can also be configured via ``<threads>`` in the rule sets.

  - ``--coverage`` - Clover style CodeCoverage report, as produced by PHPUnit's --coverage-clover
    option.

  An example command line: ::

    phpmd analyze --reportfile-text report.txt --suffixes php --suffixes phtml src/

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

You can also mix custom `rule set files </documentation/creating-a-ruleset.html>`_ with built-in rule sets: ::

  ~ $ phpmd analyze --ruleset codesize --ruleset /my/rules.xml /path/to/source

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
==========

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
=========

At the moment PHPMD comes with the following renderers:

- *xml*, which formats the report as XML.
- *text*, simple textual format.
- *ansi*, colorful, formatted text for the command line.
- *html*, single HTML file with possible problems.
- *json*, formats JSON report.
- *gitlab*, a format that GitLab CI understands.
- *github*, a format that GitHub Actions understands (see `CI Integration </documentation/ci-integration.html#github-actions>`_).
- *githubcheckruns*, JSON format for the `GitHub Check Runs API <https://docs.github.com/en/rest/checks/runs#create-a-check-run>`_.
- *sarif*, the Static Analysis Results Interchange Format.
- *checkstyle*, language and tool agnostic XML format.

The ``xml`` renderer writes a Java-PMD compatible report. Running it against
the built-in ``codesize`` rule set::

  ~ $ phpmd analyze --format xml --ruleset codesize src/

prints a report similar to this::

  <?xml version="1.0" encoding="UTF-8" ?>
  <pmd version="3.0.0" tool="phpmd" timestamp="2026-08-26T12:23:29+02:00">
    <file name="/projects/example/src/DbusUI/ResultPrinter.php">
      <violation beginline="67"
                 endline="224"
                 rule="TooManyMethods"
                 ruleset="Code Size Rules"
                 package="Example\DbusUI"
                 externalInfoUrl="https://phpmd.org/rules/codesize.html#toomanymethods"
                 class="ResultPrinter"
                 priority="3">
        The class ResultPrinter has 31 non-getter- and setter-methods. Consider refactoring ResultPrinter to keep number of methods under 25.
      </violation>
    </file>
  </pmd>

Some more formats can be obtained by conversion such as:

*junit* can be obtained using `xsltproc` package on the Debian-based systems or `libxslt` on Alpine and CentOS. with this given `junit.xslt config file <https://phpmd.org/junit.xslt>`_::

  ~ $ phpmd analyze --format xml --ruleset cleancode src | xsltproc junit.xslt -

Baseline
=========

For existing projects a violation baseline can be generated. All violations in this baseline will be ignored in further inspections.

The recommended approach would be a rule set file (e.g. ``phpmd.yml`` or ``phpmd.xml``) in the root of the project. To generate the phpmd.baseline.xml next to it::

  ~ $ phpmd analyze --generate-baseline /path/to/source

To specify a custom baseline filepath for export::

  ~ $ phpmd analyze --generate-baseline --baseline-file /path/to/source/phpmd.baseline.xml /path/to/source

By default PHPMD will look next to your rule set file for ``phpmd.baseline.xml``. To overwrite this behaviour::

  ~ $ phpmd analyze --baseline-file /path/to/source/phpmd.baseline.xml /path/to/source

To clean up an existing baseline file and *only remove* no longer existing violations. Violations
that are not in the baseline are still reported and make the command exit with a non-zero code::

  ~ $ phpmd analyze --update-baseline /path/to/source
