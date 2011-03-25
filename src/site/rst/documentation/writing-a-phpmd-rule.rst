============================
Howto write a Rule for PHPMD
============================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the how to develop custom rule classes
               for PHPMD (PHP Mess Detector). You will learn how to develop
               different rule types and how to configure them in a custom rule
               set file, so that PHPMD can use those rules for its analysis
               runs. Additionally you will learn several other aspects about
               PHPMD, like the violation message template engine and how to
               write customizable rule classes.
:Keywords:     NPM, Number of Public Methods, Software Metrics, PHPMD, PMD, rule set, rule, xml, violation, AST, Abstract Syntax Tree

This article describes how you can extend PHPMD with custom rule classes that
can be used to detect design issues or errors in the analyzed source code.

Let us start with some architecture basics behind PHPMD. All rules in PHPMD
must at least implement the `PHP_PMD_Rule`__ interface. You can also extend
the abstract rule base class `PHP_PMD_AbstractRule`__ which already provides
an implementation of all required infrastructure methods and application logic,
so that the only task which is left to you is the implementation of the
concrete validation code of your rule. To implement this validation-code the
PHPMD rule interface declares the ``apply()`` method which will be invoked by
the application during the source analysis phase. ::

  require_once 'PHP/PMD/AbstractRule.php';

  class Com_Example_Rule_NoFunctions extends PHP_PMD_AbstractRule
  {
      public function apply(PHP_PMD_AbstractNode $node)
      {
          // Check constraints against the given node instance
      }
  }

The ``apply()`` method gets an instance of `PHP_PMD_AbstractNode`__ as
argument. This node instance represents the different high level code artifacts
found in the analyzed source code. In this context high level artifact means
*interfaces*, *classes*, *methods* and *functions*. But how do we tell PHPMD
which of these artifacts are interesting for our rule, because we do not want
duplicate implementations of the descision code in every rule? To solve this
problem PHPMD uses so-called marker interfaces. The only purpose of these
interfaces is to label a rule class, which says: Hey I'm interessted in nodes
of type class and interface, or I am interessted in function artifacts. The
following list shows the available marker interfaces:

- `PHP_PMD_Rule_IClassAware`__
- `PHP_PMD_Rule_IFunctionAware`__
- `PHP_PMD_Rule_IInterfaceAware`__
- `PHP_PMD_Rule_IMethodAware`__

With this marker interfaces we can now extend the previous example, so that
the rule will be called for functions found in the analyzed source code. ::

  require_once 'PHP/PMD/AbstractRule.php';
  require_once 'PHP/PMD/Rule/IFunctionAware.php';

  class Com_Example_Rule_NoFunctions
         extends PHP_PMD_AbstractRule
      implements PHP_PMD_Rule_IFunctionAware
  {
      public function apply(PHP_PMD_AbstractNode $node)
      {
          // Check constraints against the given node instance
      }
  }

And because our coding guideline forbids functions every call to the ``apply()``
method will result in a rule violation. Such a violation can be reported to
PHPMD through the ``addViolation()`` method. The rule inherits this helper
method from it's parent class `PHP_PMD_AbstractRule`__. ::

  class Com_Example_Rule_NoFunctions // ...
  {
      public function apply(PHP_PMD_AbstractNode $node)
      {
          $this->addViolation($node);
      }
  }

That's it. The only thing left to do is adding a configuration entry for this
rule to a rule set file. This ruleset file is an XML document where all settings
of one or more rules can be configured, so that everyone can customize an
existing rule without any changes the rule's source. The syntax of the rule set
file is completly adapted from PHPMD's inspiring example `PMD`__. To get
started with a custom rule set you should take a look at one of the existing
`XML files`__ and then adapt one of the rule configurations for a newly created
rule. The most important elements of a rule configuration are:

- *@name*: Human readable name for the rule.
- *@message*: The error/violation message that will shown in the report.
- *@class*: The full qualified class name of the rule.
- *priority*: The priority for the rule. This can be a value in the range 1-5,
  where 1 is the highest priority and 5 the lowest priority.

::

  <ruleset name="example.com rules"
         xmlns="http://pmd.sf.net/ruleset/1.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"
         xsi:noNamespaceSchemaLocation="http://pmd.sf.net/ruleset_xml_schema.xsd">

      <rule name="FunctionRule"
            message = "Please do not use functions."
            class="Com_Example_Rule_NoFunctions"
            externalInfoUrl="http://example.com/phpmd/rules.html#functionrule">

          <priority>1</priority>
      </rule>
  </ruleset>

The previous listing shows a basic rule set file that configures all required
settings for the created example rule. For more details on PHPMD's rule set
file format you should take a look a the `Create a custom rule set`__ tutorial.

Finally the real world test. Let's assume we have saved the rule class in a
file ``Com/Example/Rule/NoFunction.php`` that is somewhere in the PHP
``include_path`` and we have saved the rule set in a file named
``example-rule.xml``. No we can test the rule from the command line with the
following command: ::

  ~ $ phpmd /my/source/example.com text /my/rules/example-rule.xml

  /my/source/example.com/functions.php:2    Please do not use functions.

That's it. Now we have a first custom rule class that can be used with PHPMD.

Writing a rule based on an existing Software Metric
===================================================

Since the root goal for the development of PHPMD was the implementation of a
simple and user friendly interface for PHP_Depend, we will show you in this
section how to develop a rule class, that uses a software metric measured by
`PHP_Depend`__ as input data.

In this section you will learn how to access software metrics for a given
`PHP_PMD_AbstractNode`__ instance. And you will learn how to use PHPMD's
configuration backend in such a way, that thresholds and other settings can
be customized without changing any PHP code. Additionally you will see how
the information content of an error message can be improved.

The first thing we need now is a software metric that we want to use as basis
for the new rule. A complete and up2date list of available software metrics
can be found PHP_Depend's `metric catalog`__. For this article we choose the
`Number of Public Methods (npm)`__ metric and we define an upper and a lower
threshold for our rule. The upper threshold is ``10``, because we think a class
with more public methods exposes to much of its privates and should be
refactored into two or more classes. For the lower threshold we choose ``1``,
because a class without any public method does not expose any service to
surrounding application.

The following code listing shows the entire rule class skeleton. As you can
see, this class implements the `PHP_PMD_Rule_IClassAware`__ interface, so that
PHPMD knows that this rule will only be called for classes. ::

  require_once 'PHP/PMD/AbstractRule.php';
  require_once 'PHP/PMD/Rule/IClassAware.php';

  class Com_Example_Rule_NumberOfPublicMethods
         extends PHP_PMD_AbstractRule
      implements PHP_PMD_Rule_IClassAware
  {
      const MINIMUM = 1,
            MAXIMUM = 10;

      public function apply(PHP_PMD_AbstractNode $node)
      {
          // Check constraints against the given node instance
      }
  }

Now that we have the rule skeleton we must access the ``npm`` metric which
is associated with the given node instance. All software metrics calculated for
a node object can directly be accessed through the ``getMetric()`` method of the
node instance. This method takes a single parameter, the abbreviation/acronym
of the metric as documented in PHP_Depends `metric catalog`__. ::

  require_once 'PHP/PMD/AbstractRule.php';
  require_once 'PHP/PMD/Rule/IClassAware.php';

  class Com_Example_Rule_NumberOfPublicMethods
         extends PHP_PMD_AbstractRule
      implements PHP_PMD_Rule_IClassAware
  {
      const MINIMUM = 1,
            MAXIMUM = 10;

      public function apply(PHP_PMD_AbstractNode $node)
      {
          $npm = $node->getMetric('npm');
          if ($npm < self::MINIMUM || $npm > self::MAXIMUM) {
              $this->addViolation($node);
          }
      }
  }

That's the coding part for the metric based rule. Now we must add this class
to a rule set file.

::

  <ruleset name="example.com rules"
         xmlns="http://pmd.sf.net/ruleset/1.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"
         xsi:noNamespaceSchemaLocation="http://pmd.sf.net/ruleset_xml_schema.xsd">

      <!-- ... -->

      <rule name="NumberOfPublics"
            message = "The context class violates the NPM metric."
            class="Com_Example_Rule_NumberOfPublicMethods"
            externalInfoUrl="http://example.com/phpmd/rules.html#numberofpublics">

          <priority>3</priority>
      </rule>
  </ruleset>

Now we can run PHPMD with this rule set file and it will report us all classes
that do not fulfill our requirement for the NPM metric. But as promised, we
will make this rule more customizable, so that it can be adjusted for different
project requirements. Therefore we will replace the two constants ``MINIMUM``
and ``MAXIMUM`` with properties that can be configured in the rule set file.
So let us start with the modified rule set file. ::

  <ruleset name="example.com rules"
         xmlns="http://pmd.sf.net/ruleset/1.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"
         xsi:noNamespaceSchemaLocation="http://pmd.sf.net/ruleset_xml_schema.xsd">

      <!-- ... -->

      <rule name="NumberOfPublics"
            message = "The context class violates the NPM metric."
            class="Com_Example_Rule_NumberOfPublicMethods"
            externalInfoUrl="http://example.com/phpmd/rules.html#numberofpublics">

          <priority>3</priority>
          <properties>
              <property name="minimum"
                        value="1"
                        description="Minimum number of public methods." />
              <property name="maximum"
                        value="10"
                        description="Maximum number of public methods." />
          </properties>
      </rule>
  </ruleset>

In PMD rule set files you can define as many properties for a rule as you like.
All of them will be injected into a rule instance by PHPMD's runtime
environment and then can be accessed through the ``get<type>Property()``
methods. Currently PHPMD supports the following getter methods.

- ``getBooleanProperty()``
- ``getIntProperty()``

So now let's modify the rule class and replace the hard coded constants with
the configurable properties. ::

  require_once 'PHP/PMD/AbstractRule.php';
  require_once 'PHP/PMD/Rule/IClassAware.php';

  class Com_Example_Rule_NumberOfPublicMethods
         extends PHP_PMD_AbstractRule
      implements PHP_PMD_Rule_IClassAware
  {
      public function apply(PHP_PMD_AbstractNode $node)
      {
          $npm = $node->getMetric('npm');
          if ($npm < $this->getIntProperty('minimum') ||
              $npm > $this->getIntProperty('maximum')
          ) {
              $this->addViolation($node);
          }
      }
  }

Now we are nearly done, but one issue is still left out. When we execute this
rule, the user will get the message *"The context class violates the NPM
metric."* which isn't really informative, because he must manually check if the
upper or lower threshold was exceeded and what the actual thresholds are. To
provide more information about a rule violation you can use PHPMD's minimalistic
template/placeholder engine for violation messages. With this engine you can
define violation messages with placeholders, that will be replaced with actual
values. The format for such placeholders is ``'{' + \d+ '}'``. ::

  <ruleset name="example.com rules"
         xmlns="http://pmd.sf.net/ruleset/1.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"
         xsi:noNamespaceSchemaLocation="http://pmd.sf.net/ruleset_xml_schema.xsd">

      <!-- ... -->

      <rule name="NumberOfPublics"
            message = "The class {0} has {1} public method, the threshold is {2}."
            class="Com_Example_Rule_NumberOfPublicMethods"
            externalInfoUrl="http://example.com/phpmd/rules.html#numberofpublics">

          <priority>3</priority>
          <properties>
              <property name="minimum"
                        value="1"
                        description="Minimum number of public methods." />
              <property name="maximum"
                        value="10"
                        description="Maximum number of public methods." />
          </properties>
      </rule>
  </ruleset>

Now we can adjust the rule class in such a manner, that it will set the correct
values for the placeholders ``{0}``, ``{1}`` and ``{2}`` ::

  require_once 'PHP/PMD/AbstractRule.php';
  require_once 'PHP/PMD/Rule/IClassAware.php';

  class Com_Example_Rule_NumberOfPublicMethods
         extends PHP_PMD_AbstractRule
      implements PHP_PMD_Rule_IClassAware
  {
      public function apply(PHP_PMD_AbstractNode $node)
      {
          $min = $this->getIntProperty('minimum');
          $max = $this->getIntProperty('maximum');
          $npm = $node->getMetric('npm');

          if ($npm < $min) {
              $this->addViolation($node, array(get_class($node), $npm, $min));
          } else if ($npm > $max) {
              $this->addViolation($node, array(get_class($node), $npm, $max));
          }
      }
  }

If we run this version of the rule we will get an error message like the one
shown in the following figure. ::

  The class FooBar has 42 public method, the threshold is 10.

Writing a rule based on the Abstract Syntax Tree
================================================

Now we will learn how to develop a PHPMD rule that utilizes PHP_Depend's
abstract syntax tree to detect violations or possible error in the analyzed
source code. The ability to access PHP_Depend's abstract syntax tree gives you
the most powerful way to write rules for PHPMD, because you can analyze nearly
all apects of the software under test. The syntax tree can be accessed through
the ``getFirstChildOfType()`` and ``findChildrenOfType()`` methods of the
`PHP_PMD_AbstractNode`__ class.

In this example we will implement a rule that detects the usage of the new and
controversial PHP feature ``goto``. Because we all know and agree that ``goto``
was already bad in Basic, we would like to prevent our developers from using
the bad feature. Therefore we implement a PHPMD rule, that searches through
PHP_Depend's for the ``goto`` language construct.

Because the ``goto`` statement cannot be found in classes and interfaces, but
in methods and functions, the new rule class must implement the two marker
interfaces `PHP_PMD_Rule_IFunctionAware`__ and `PHP_PMD_Rule_IMethodAware`__.

::

  require_once 'PHP/PMD/AbstractRule.php';
  require_once 'PHP/PMD/Rule/IMethodAware.php';
  require_once 'PHP/PMD/Rule/IFunctionAware.php';

  class PHP_PMD_Rule_Design_GotoStatement
         extends PHP_PMD_AbstractRule
      implements PHP_PMD_Rule_IMethodAware,
                 PHP_PMD_Rule_IFunctionAware
  {
      public function apply(PHP_PMD_AbstractNode $node)
      {
          foreach ($node->findChildrenOfType('GotoStatement') as $goto) {
              $this->addViolation($goto, array($node->getType(), $node->getName()));
          }
      }
  }

As you can see, we are searching for the string ``GotoStatement`` in the
previous example. This is a shortcut notation used by PHPMD to address concrete
PHP_Depend syntax tree nodes. All abstract syntax tree classes in PHP_Depend
have the following format: ::

  PHP_Depend_Code_ASTGotoStatement

where ::

  PHP_Depend_Code_AST

is fixed and everything else depends on the node type. And this fixed part of
the class name can be ommitted in PHPMD when searching for an abstract syntax
tree node. To implement additional rules you should take a look at PHP_Depend's
`Code package`__ where you can find all currently supported code nodes.

Conclusion
==========

In this article we have shown you several ways to implement custom rules for
PHPMD. If you think one of your rules could be reusable for other projects and
user, don't hesitate to propose your on the project's mailing list
`phpmd-users@phpmd.org`__ or to send us `GitHub`__ pull request.

__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/AbstractRule.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/AbstractNode.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IClassAware.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IFunctionAware.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IInterfaceAware.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IMethodAware.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/AbstractRule.php
__ http://pmd.sf.net/
__ https://github.com/phpmd/phpmd/tree/master/src/main/resources/rulesets
__ http://phpmd.org/documentation/creating-a-ruleset.html

__ http://pdepend.org
__ http://pdepend.org/documentation/software-metrics.html
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/AbstractNode.php
__ http://pdepend.org/documentation/software-metrics/number-of-public-methods.html
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IClassAware.php
__ http://pdepend.org/documentation/software-metrics.html

__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/AbstractNode.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IFunctionAware.php
__ https://github.com/phpmd/phpmd/blob/master/src/main/php/PHP/PMD/Rule/IMethodAware.php
__ https://github.com/pdepend/pdepend/tree/master/src/main/php/PHP/Depend/Code
__ mailto:phpmd-users@phpmd.org
__ https://github.com/phpmd/phpmd
