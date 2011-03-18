==============================
Howto create a custom rule set
==============================

If you would like to only pick some of the rules that come with PHPMD and
you want to customize some of the pre defined thresholds, you can do this
by creating your own rule set file that references a custom collection of
rules with an individual configuration.

Starting with an empty ruleset.xml file
=======================================

The simpliest way to start with a new rule set is to copy one of the 
existing files and remove all the rule-tags from the document body. 
Otherwise you can use the following example as a template for your own 
rule set file. You should change the content of the ``@name`` attribute 
and ``<description />`` element to something that describes the purpose
of this set. ::

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
  </ruleset>

Adding rule references to the new ruleset.xml file
==================================================

The first thing we would like to do is to add all `unused code`__ rules
to the new rule set file. This can simply be done with a ``<rule />`` 
element that references the entire `unused code`__ rule set that comes 
with PHPMD.

__ /rules/unusedcode.html
__ /rules/unusedcode.html

::

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

      <!-- Import the entire unused code rule set -->
      <rule ref="rulesets/unusedcode.xml" />
  </ruleset>

That's it. Now the custom rule set applies all `unused code`__ rules
against the analyzed source code.

__ /rules/unusedcode.html

We would also like to use the `cyclomatic complexity`__ rule from the
existing `codesize`__ set in our custom rule set. To achieve this we can
reuse the same syntax with a ``<rule />`` element and a ``@ref`` attribute.

__ /rules/codesize.html#cyclomaticcomplexity
__ /rules/codesize.html

::

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

      <!-- Import the entire unused code rule set -->
      <rule ref="rulesets/unusedcode.xml" />

      <!-- Import the entire cyclomatic complexity rule -->
      <rule ref="rulesets/codesize.xml/CyclomaticComplexity" />
  </ruleset>

Now that the new rule set uses the `cyclomatic complexity`__ rule we would 
also like to customize some of the rule's properties. First we will 
increase the rule's priority to the highest possible priority value ``1`` 
and we also decrease the threshold when the rule reports a violation. This 
customization can be done with same xml elements that are used to configure
the original rule, so that you can take a look at one of the original rule 
set file. 

__ /rules/codesize.html#cyclomaticcomplexity

::

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

      <!-- Import the entire unused code rule set -->
      <rule ref="rulesets/unusedcode.xml" />

      <!-- 
          Import the entire cyclomatic complexity rule and 
          customize the rule configuration.
      -->
      <rule ref="rulesets/codesize.xml/CyclomaticComplexity">
          <priority>1</priority>
          <properties>
              <property name="reportLevel" value="5" />
          </properties>
      </rule>
  </ruleset>

You should know that PHPMD handles all custom settings additive. This 
means that PHPMD keeps the original configuration for every setting that
isn't customized in a rule reference.

Excluding rules from a rule set
===============================

Finally we would like to reuse the `naming`__ rule set of PHPMD. But we 
don't like the two variable naming rules, so that we must exclude them
from out rule set file. This exclusion can be achieved by declaring an
``<exclude />`` element within the rule reference. This element has an
attribute ``@name`` which specifies the name of the excluded rule.

__ /rules/naming.html


::

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

      <!-- Import the entire unused code rule set -->
      <rule ref="rulesets/unusedcode.xml" />

      <!-- 
          Import the entire cyclomatic complexity rule and 
          customize the rule configuration.
      -->
      <rule ref="rulesets/codesize.xml/CyclomaticComplexity">
          <priority>1</priority>
          <properties>
              <property name="reportLevel" value="5" />
          </properties>
      </rule>

      <!-- Import entire naming rule set and exclude rules -->
      <rule ref="rulesets/naming.xml">
          <exclude name="ShortVariable" />
          <exclude name="LongVariable" />
      </rule>
  </ruleset>

Conclusion
==========

With PHPMD's rule set syntax it is possible to customize all aspects of 
rules for your own needs and you can reuse every existing rule set xml file
in your own set. You should take a look at PHPMD's rule `documentation`__ 
if it happens that you don't know what rules exist or you don't know 
exactly, which settings are available for one rule, while you create your 
own set of rules. Another good source of information are the rule set
`files`__ that are shipped with PHPMD.

__ /rules/ 
__ http://tracker.phpmd.org/php_mess_detector/browse_code/view/rulesets/
