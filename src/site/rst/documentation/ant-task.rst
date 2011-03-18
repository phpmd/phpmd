====================
PHPMD ant task usage
====================

To ease the usage of PHPMD in your build process we provide an `ant task`__
that integrates PHPMD into the `ant`__ build tool. The ant task jar file can
be found in the `download`__ section of the PHPMD homepage.

__ http://ant.apache.org/manual/targets.html
__ http://ant.apache.org/
__ /download/extensions#ant-task

To make the task available in your ant build file you have two options.
The first option is to copy or link the ``*.jar`` file into the ``lib``
directory of your ant installation. ::

  mapi@arwen ~ $ wget \
       http://phpmd.org/download/extensions/ant-phpmd-0.1.0.jar
  ...
  mapi@arwen ~ $ ln -s ~/ant-phpmd-0.1.0.jar /opt/ant/lib/ant-phpmd.jar

The second option is to call ant with the command line switch ``-lib`` ::

  mapi@arwen ~ $ ant -lib ant-phpmd-0.1.0.jar


Now we can start using PHPMD in our ant ``build.xml`` file by adding
a task definition that informs ant about the new task and registers
it with a custom identifier. ::

  <?xml version="1.0" encoding="UTF-8" ?>
  <project name="phpmd.example" default="example" basedir=".">

      <taskdef name="phpmd" classname="org.phpmd.ant.PHPMDTask"/>

  </project>

Now the PHPMD task can be called through an xml element named ``<phpmd />``
in this build: ::

  <?xml version="1.0" encoding="UTF-8"?>
  <project name="phpmd.example" default="example" basedir=".">

      <taskdef name="phpmd" classname="org.phpmd.ant.PHPMDTask"/>

      <target name="example">
          <phpmd rulesetfiles="unusedcode" failonerror="off">
              <formatter type="xml" toFile="${basedir}/pmd.xml" />
              <fileset dir="${basedir}/PHP/PMD">
                  <include name="*.php" />
              </fileset>
          </phpmd>
      </target>

  </project>

Parameters
==========

The following attributes can be defined on the PHPMD task xml-element.

===================== ========================================================================================================================= ================================================
 Attribute             Description                                                                                                               Required
===================== ========================================================================================================================= ================================================
 rulesetfiles          A comma delimited list of ruleset files ('rulesets/basic.xml,rulesets/design.xml') or identifiers of build-in rulesets.   Yes, unless the ruleset nested element is used
 failonerror           Whether or not to fail the build if any errors occur while processing the files                                           No
 failOnRuleViolation   Whether or not to fail the build if PHPMD finds any problems                                                              No
 minimumPriority       The rule priority threshold; rules with lower priority than they will not be used                                         No
===================== ========================================================================================================================= ================================================

Nested xml elements
===================

The ``<formatter />`` specifies the format of and the output file to
which the report is written.

**Parameters**

=========== =================================================================== ==========
 Attribute   Description                                                         Required
=========== =================================================================== ==========
 format      The report output format. Supported formats are xml,html and text.  Yes
 toFile      A filename into which the report is written                         Yes
=========== =================================================================== ==========

The ``<ruleset />`` xml element is another way to specify rulesets. Here
is a modified version of the previous example: ::

  <target name="example">
      <phpmd failonerror="off">
          <formatter type="text" toFile="${basedir}/pmd.xml" />
          <ruleset>unusedcode</ruleset>
          <ruleset>codesize</ruleset
          <fileset dir="${basedir}/PHP/PMD">
              <include name="*.php" />
          </fileset>
      </phpmd>
  </target>

Postprocessing the report file with XSLT
========================================

There are several XSLT scripts which can be used to transform the XML
report into some nice html pages. To do this, make sure you use the
XML formatter in the PHPMD task invocation, i.e.: ::

  <formatter type="xml" toFile="${builddir}/~report.xml"/>

Then, after the end of the PHPMD task, do this: ::

  <xslt in="${builddir}/~report.xml"
        style="${basedir}/report.xslt"
        out="${reportdir}/report.html" />


