==================
Command line usage
==================

Type phpmd [filename|directory[,filename|directory[,...]]] [report format] [ruleset file], i.e: ::

  mapi@arwen ~ $ phpmd PHP/Depend/DbusUI/ xml rulesets/codesize.xml
  <?xml version="1.0" encoding="UTF-8" ?>
  <pmd version="0.0.1" timestamp="2009-12-19T22:17:18+01:00">
    <file name="/projects/pdepend/PHP/Depend/DbusUI/ResultPrinter.php">
      <violation beginline="67"
                 endline="224"
                 rule="TooManyMethods"
                 ruleset="Code Size Rules"
                 package="PHP_Depend\DbusUI"
                 class="PHP_Depend_DbusUI_ResultPrinter"
                 priority="3">
        This class has too many methods, consider refactoring it.
      </violation>
    </file>
  </pmd>

You can pass a comma-separated string with list of file names
or a directory names, containing PHP source code to PHPMD.

The PHPMD Phar distribution includes the rule set files inside
its archive, even if the "rulesets/codesize.xml" parameter above looks
like a filesystem reference.

Command line options
====================

- Notice that the default output is in XML, so you can redirect it to
  a file and XSLT it or whatever

- You can also use shortened names to refer to the built-in rule sets,
  like this: ::

    phpmd PHP/Depend/DbusUI/ xml codesize

- The command line interface also accepts the following optional arguments:

  - ``--verbose, -v, -vv, -vvv`` - The output verbosity level. Will print more information
    what is being processed or cached. Will be send to ``STDERR`` to not interfere
    with report output. ``text`` output will also have under each error a link
    to the documentation of the rule and format the location in a way that most
    IDEs will convert into a link to open the file at the line of the error
    when clicked.

  - ``--minimumpriority`` ``--min-priority`` ``--minimum-priority`` - The rule priority threshold; rules with lower
    priority than they will not be used.

  - ``--maximumpriority`` ``--max-priority`` ``--maximum-priority`` - The rule priority threshold; rules with higher
    priority than this will not be used.

  - ``--reportfile`` ``--report-file`` - Sends the report output to the specified file,
    instead of the default output target ``STDOUT``.

  - ``--error-file`` - Sends errors (other than reported violations) output to a file; defaults to STDERR,

  - ``--suffixes`` - Comma-separated string of valid source code filename
    extensions, e.g. php, phtml.

  - ``--exclude`` - Comma-separated string of patterns that are used to ignore
    directories. Use asterisks to exclude by pattern. For example ``*src/foo/*.php`` or ``*src/foo/*``

  - ``--strict`` - Also report those nodes with a @SuppressWarnings annotation.

  - ``--not-strict`` - Does not report those nodes with a @SuppressWarnings annotation.

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

  -  ``--baseline-file`` - the filepath to a custom baseline xml file. If absent will
    default to ``phpmd.baseline.xml``

  - ``--color`` - enable color in output, for instance text renderer
    will show rule name in yellow and error description in red.

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

You can also mix custom `rule set files </documentation/creating-a-ruleset.html>`_ with build-in rule sets: ::

  ~ $ phpmd /path/to/source text codesize,/my/rules.xml

That's it. With this behavior you can specify you own combination of rule sets
that will check the source code.

Using multiple source files and folders
```````````````````````````````````````

PHPMD also allows you to specify multiple source directories in case you want
to create one output for certain parts of your code ::

 ~ $ phpmd /path/to/code,index.php,/another/place/with/code text codesize

Or use glob pattern: ::

  ~ $ phpmd src/main/php/*/*/*{Renderer,Node}.php text my/rules.xml

Scan input
``````````

PHPMD can also read the standard input `stdin`: ::

  ~ $ cat src/MyService.php | phpmd - text my/rules.xml

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

Some more formats can be obtained by conversion such as:

*junit* can be obtained using `xsltproc` package on the Debian-based systems or `libxslt` on Alpine and CentOS. with this given `junit.xslt config file <https://phpmd.org/junit.xslt>`_::

  ~ $ phpmd src xml cleancode | xsltproc junit.xslt -

Baseline
=========

For existing projects a violation baseline can be generated. All violations in this baseline will be ignored in further inspections.

The recommended approach would be a ``phpmd.xml`` in the root of the project. To generate the phpmd.baseline.xml next to it::

  ~ $ phpmd /path/to/source text phpmd.xml --generate-baseline

To specify a custom baseline filepath for export::

  ~ $ phpmd /path/to/source text phpmd.xml --generate-baseline --baseline-file /path/to/source/phpmd.baseline.xml

By default PHPMD will look next to ``phpmd.xml`` for ``phpmd.baseline.xml``. To overwrite this behaviour::

  ~ $ phpmd /path/to/source text phpmd.xml --baseline-file /path/to/source/phpmd.baseline.xml

To clean up an existing baseline file and *only remove* no longer existing violations::

  ~ $ phpmd /path/to/source text phpmd.xml --update-baseline
