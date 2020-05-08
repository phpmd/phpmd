==================
Command line usage
==================

Type phpmd [filename|directory] [report format] [ruleset file], i.e: ::

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

You can pass a file name or a directory name containing PHP source
code to PHPMD.

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

  - ``--minimumpriority`` ``--min-priority`` ``--minimum-priority`` - The rule priority threshold; rules with lower
    priority than they will not be used.

  - ``--maximumpriority`` ``--max-priority`` ``--maximum-priority`` - The rule priority threshold; rules with higher
    priority than this will not be used.

  - ``--reportfile`` ``--report-file`` - Sends the report output to the specified file,
    instead of the default output target ``STDOUT``.

  - ``--suffixes`` - Comma-separated string of valid source code filename
    extensions, e.g. php, phtml.

  - ``--exclude`` - Comma-separated string of patterns that are used to ignore
    directories. Use asterisks to exclude by pattern. For example ``*src/foo/*.php`` or ``*src/foo/*``

  - ``--strict`` - Also report those nodes with a @SuppressWarnings annotation.

  - ``--not-strict`` - Does not report those nodes with a @SuppressWarnings annotation.

  - ``--ignore-violations-on-exit`` - will exit with a zero code, even if any
    violations are found.

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

Exit codes
==========

PHPMD's command line tool currently defines three different exit codes.

- *0*, This exit code indicates that everything worked as expected. This means
  there was no error/exception and PHPMD hasn't detected any rule violation
  in the code under test.
- *1*, This exit code indicates that an error/exception occurred which has
  interrupted PHPMD during execution.
- *2*, This exit code means that PHPMD has processed the code under test
  without the occurrence of an error/exception, but it has detected rule
  violations in the analyzed source code. You can also prevent this behaviour
  with the ``--ignore-violations-on-exit`` flag, which will result to a *0*
  even if any violations are found.

Renderers
=========

At the moment PHPMD comes with the following five renderers:

- *xml*, which formats the report as XML.
- *text*, simple textual format.
- *ansi*, colorful, formated text for the command line.
- *html*, single HTML file with possible problems.
- *json*, formats JSON report.
