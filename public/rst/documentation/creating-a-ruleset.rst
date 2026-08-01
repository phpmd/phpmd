===============================
How to create a custom rule set
===============================

If you would like to only pick some of the rules that come with PHPMD and
you want to customize some of the predefined thresholds, you can do this
by creating your own rule set file that references a custom collection of
rules with an individual configuration.

Supported configuration formats
================================

PHPMD supports rule set configuration in YAML, XML, JSON, and PHP. YAML is
the recommended format for new projects because of its readability and
simplicity. The following file names are auto-detected when no rule set is
specified on the command line (in order of priority):

``phpmd.yml``, ``phpmd.yaml``, ``phpmd.json``, ``phpmd.xml``, ``phpmd.php``,
as well as their ``.``-prefixed and ``.dist``-suffixed variants (e.g.
``.phpmd.yml`` or ``phpmd.yml.dist``).

Starting with an empty rule set
================================

The simplest way to start is to create a ``phpmd.yml`` file in the root of
your project. Here is a minimal template.

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules: []

Adding rule references
=======================

The first thing we would like to do is to add all `unused code`__ rules
to the new rule set file. This can be done with a ``ref`` entry that
references the entire `unused code`__ rule set that comes with PHPMD.

__ /rules/unusedcode.html
__ /rules/unusedcode.html

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules:
    - ref: rulesets/unusedcode.xml

That's it. Now the custom rule set applies all `unused code`__ rules
against the analyzed source code.

__ /rules/unusedcode.html

We would also like to use the `cyclomatic complexity`__ rule from the
existing `codesize`__ set in our custom rule set. We can add another
``ref`` entry pointing to the specific rule within the rule set.

__ /rules/codesize.html#cyclomaticcomplexity
__ /rules/codesize.html

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules:
    # Import the entire unused code rule set
    - ref: rulesets/unusedcode.xml
    # Import the cyclomatic complexity rule
    - ref: rulesets/codesize.xml/CyclomaticComplexity

Now that the new rule set uses the `cyclomatic complexity`__ rule we would
also like to customize some of the rule's properties. First we will
increase the rule's priority to the highest possible priority value ``1``
and we also decrease the threshold when the rule reports a violation.

__ /rules/codesize.html#cyclomaticcomplexity

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules:
    # Import the entire unused code rule set
    - ref: rulesets/unusedcode.xml
    # Import and customize the cyclomatic complexity rule
    - ref: rulesets/codesize.xml/CyclomaticComplexity
      priority: 1
      properties:
        maximum: 5

PHPMD handles all custom settings additively. This means that PHPMD keeps
the original configuration for every setting that isn't customized in a
rule reference.

Excluding rules from a rule set
================================

We would like to reuse the `naming`__ rule set of PHPMD. But we don't like
the two variable naming rules, so we must exclude them from our rule set
file. This can be achieved by adding an ``exclude`` list to the rule
reference.

__ /rules/naming.html

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules:
    - ref: rulesets/unusedcode.xml
    - ref: rulesets/codesize.xml/CyclomaticComplexity
      priority: 1
      properties:
        maximum: 5
    # Import naming rules, but exclude some
    - ref: rulesets/naming.xml
      exclude:
        - ShortVariable
        - LongVariable

Changing individual properties in a rule set
=============================================

We would like to use the `clean code`__ rule set, but our code uses the
static constructors of the PHP date and time classes. This causes rule
violations with the ``StaticAccess`` rule. To modify the ``exceptions``
property of that rule while still keeping the rest of the rule set, we
need to import the whole rule set excluding the ``StaticAccess`` rule
and then include the ``StaticAccess`` rule individually.

__ /rules/cleancode.html

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules:
    - ref: rulesets/unusedcode.xml
    - ref: rulesets/codesize.xml/CyclomaticComplexity
      priority: 1
      properties:
        maximum: 5
    - ref: rulesets/naming.xml
      exclude:
        - ShortVariable
        - LongVariable
    # Import clean code rules, but customize StaticAccess
    - ref: rulesets/cleancode.xml
      exclude:
        - StaticAccess
    - ref: rulesets/cleancode.xml/StaticAccess
      properties:
        exceptions: '\DateTime,\DateInterval,\DateTimeZone'

Excluding files from analysis
==============================

You can exclude files and directories from analysis by adding
``exclude-pattern`` entries to your rule set file. These patterns work
the same way as the ``--exclude`` CLI option. Use asterisks to match
by pattern.

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  exclude-pattern:
    - "*/vendor/*"
    - "*/tests/resources/*"
  rules:
    - ref: rulesets/cleancode.xml

XML configuration format
=========================

PHPMD also supports rule sets in the traditional XML format. If you are
working with an existing project that already uses an XML rule set, or if you
prefer XML, here is how the same configuration from the examples above looks
in XML.

Starting with an empty rule set:

.. code-block:: xml

  <?xml version="1.0"?>
  <ruleset name="My first PHPMD rule set"
           xmlns="https://phpmd.org/xml/ruleset/1.0.0"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="https://phpmd.org/xml/ruleset/1.0.0
                       http://phpmd.org/xml/ruleset_xml_schema_1.0.0.xsd"
           xsi:noNamespaceSchemaLocation="
                       http://phpmd.org/xml/ruleset_xml_schema_1.0.0.xsd">
      <description>
          My custom rule set that checks my code...
      </description>
  </ruleset>

Adding rules, customizing properties, and excluding rules:

.. code-block:: xml

  <?xml version="1.0"?>
  <ruleset name="My first PHPMD rule set"
           xmlns="https://phpmd.org/xml/ruleset/1.0.0"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="https://phpmd.org/xml/ruleset/1.0.0
                       http://phpmd.org/xml/ruleset_xml_schema_1.0.0.xsd"
           xsi:noNamespaceSchemaLocation="
                       http://phpmd.org/xml/ruleset_xml_schema_1.0.0.xsd">
      <description>
          My custom rule set that checks my code...
      </description>

      <!-- Exclude files from analysis -->
      <exclude-pattern>*/vendor/*</exclude-pattern>
      <exclude-pattern>*/tests/resources/*</exclude-pattern>

      <!-- Import the entire unused code rule set -->
      <rule ref="rulesets/unusedcode.xml" />

      <!-- Import and customize the cyclomatic complexity rule -->
      <rule ref="rulesets/codesize.xml/CyclomaticComplexity">
          <priority>1</priority>
          <properties>
              <property name="maximum" value="5" />
          </properties>
      </rule>

      <!-- Import naming rules, but exclude some -->
      <rule ref="rulesets/naming.xml">
          <exclude name="ShortVariable" />
          <exclude name="LongVariable" />
      </rule>

      <!-- Import clean code rules, but customize StaticAccess -->
      <rule ref="rulesets/cleancode.xml">
          <exclude name="StaticAccess" />
      </rule>
      <rule ref="rulesets/cleancode.xml/StaticAccess">
          <properties>
              <property name="exceptions">
                  <value>
                    \DateTime,
                    \DateInterval,
                    \DateTimeZone
                  </value>
              </property>
          </properties>
      </rule>
  </ruleset>

This approach is also required when you need to change other aspects of a rule
beyond properties, such as its ``<priority>``.

Overwriting properties without excluding
========================================

When you only need to change properties on multiple rules from an imported
rule set, you can use a rule entry with a ``name`` instead of a ``ref``.
This avoids having to exclude and re-include each rule individually.

.. code-block:: yaml

  name: My first PHPMD rule set
  description: My custom rule set that checks my code...
  rules:
    # Import the entire codesize ruleset
    - ref: rulesets/codesize.xml
    # Customize properties on individual rules without excluding them first
    - name: ExcessiveParameterList
      properties:
        maximum: 15
    - name: TooManyFields
      properties:
        maximum: 35
    - name: TooManyMethods
      properties:
        maximum: 35

In XML, the equivalent uses ``<rule name="...">`` elements with only a
``<properties>`` child — no ``ref`` attribute:

.. code-block:: xml

  <?xml version="1.0"?>
  <ruleset name="My first PHPMD rule set"
           xmlns="https://phpmd.org/xml/ruleset/1.0.0"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="https://phpmd.org/xml/ruleset/1.0.0
                       http://phpmd.org/xml/ruleset_xml_schema_1.0.0.xsd"
           xsi:noNamespaceSchemaLocation="
                       http://phpmd.org/xml/ruleset_xml_schema_1.0.0.xsd">
      <description>
          My custom rule set that checks my code...
      </description>

      <!-- Import the entire codesize ruleset -->
      <rule ref="rulesets/codesize.xml" />

      <!-- Customize properties on individual rules without excluding them first -->
      <rule name="ExcessiveParameterList">
          <properties>
              <property name="maximum" value="15" />
          </properties>
      </rule>

      <rule name="TooManyFields">
          <properties>
              <property name="maximum" value="35" />
          </properties>
      </rule>

      <rule name="TooManyMethods">
          <properties>
              <property name="maximum" value="35" />
          </properties>
      </rule>
  </ruleset>

JSON and PHP configuration formats
====================================

Rule sets can also be written in JSON or PHP. Here is the same
configuration in those formats.

JSON example (``phpmd.json``):

.. code-block:: json

  {
      "name": "My first PHPMD rule set",
      "description": "My custom rule set that checks my code...",
      "exclude-pattern": [
          "*/vendor/*",
          "*/tests/resources/*"
      ],
      "rules": [
          {"ref": "rulesets/unusedcode.xml"},
          {
              "ref": "rulesets/codesize.xml/CyclomaticComplexity",
              "priority": 1,
              "properties": {"maximum": 5}
          },
          {
              "ref": "rulesets/naming.xml",
              "exclude": ["ShortVariable", "LongVariable"]
          },
          {
              "ref": "rulesets/cleancode.xml",
              "exclude": ["StaticAccess"]
          },
          {
              "ref": "rulesets/cleancode.xml/StaticAccess",
              "properties": {
                  "exceptions": "\\DateTime,\\DateInterval,\\DateTimeZone"
              }
          }
      ]
  }

PHP example (``phpmd.php``):

.. code-block:: php

  <?php

  return [
      'name' => 'My first PHPMD rule set',
      'description' => 'My custom rule set that checks my code...',
      'exclude-pattern' => [
          '*/vendor/*',
          '*/tests/resources/*',
      ],
      'rules' => [
          ['ref' => 'rulesets/unusedcode.xml'],
          [
              'ref' => 'rulesets/codesize.xml/CyclomaticComplexity',
              'priority' => 1,
              'properties' => ['maximum' => 5],
          ],
          [
              'ref' => 'rulesets/naming.xml',
              'exclude' => ['ShortVariable', 'LongVariable'],
          ],
          [
              'ref' => 'rulesets/cleancode.xml',
              'exclude' => ['StaticAccess'],
          ],
          [
              'ref' => 'rulesets/cleancode.xml/StaticAccess',
              'properties' => [
                  'exceptions' => '\DateTime,\DateInterval,\DateTimeZone',
              ],
          ],
      ],
  ];

Conclusion
==========

With PHPMD's rule set syntax it is possible to customize all aspects of
rules for your own needs and you can reuse every existing rule set in
your own configuration. You should take a look at PHPMD's rule `documentation`__
if it happens that you don't know what rules exist or you don't know
exactly which settings are available for one rule, while you create your
own set of rules. Another good source of information are the rule set
`files`__ that are shipped with PHPMD.

__ /rules/index.html
__ https://github.com/phpmd/phpmd/tree/master/rulesets
