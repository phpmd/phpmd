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

The PHPMD PEAR or Phar distribution includes the rule set files inside 
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

  - ``--minimumpriority`` - The rule priority threshold; rules with lower
    priority than they will not be used.

  - ``--reportfile`` - Sends the report output to the specified file, 
    instead of the default output target ``STDOUT``.

  - ``--extensions`` - Comma separated string of valid PHP source file
    extensions.

  - ``--ignore`` - Comma separated string of files or directories that
    will be ignored during the parsing process.

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

__ /documentation/creating-a-ruleset.html

That's it. With this behavior you can specify you own combination of rule sets
that will check the source code.

Exit codes
==========

PHPMD's command line tool currently defines three different exit codes.

- *0*, This exit code indicates that everything worked as expected. This means
  there was no error/exception and PHPMD hasn't detected any rule violation
  in the code under test.
- *1*, This exit code indicates that an error/exception occured which has
  interrupted PHPMD during execution.
- *2*, This exit code means that PHPMD has processed the code under test
  without the occurence of an error/exception, but it has detected rule
  violations in the analyzed source code.

Renderers
=========

At the moment PHPMD comes with the following three renderers:

- *xml*, which formats the report as XML.
- *text*, simple textual format.
- *html*, single HTML file with possible problems.

