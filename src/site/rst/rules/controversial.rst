===================
Controversial Rules
===================

This ruleset contains a collection of controversial rules.

Superglobals
============

Since: PHPMD 0.2

Accessing a super-global variable directly is considered a bad practice. These variables should be encapsulated in objects that are provided by a framework, for instance.

Example: ::

  class Foo {
      public function bar() {
          $name = $_POST['foo'];
      }
  }

CamelCaseClassName
==================

Since: PHPMD 0.2

It is considered best practice to use the CamelCase notation to name classes.

Example: ::

  class class_name {
  }

+-----------------------------------+---------------+---------------------------------------------------------+
| Name                              | Default Value | Description                                             |
+===================================+===============+=========================================================+
| camelcase-abbreviations           | false         | Name should be CamelCase including abbreviations.       |
+-----------------------------------+---------------+---------------------------------------------------------+

CamelCaseNamespace
==================

Since: PHPMD 2.16

A rule to use CamelCase notation to name namespaces.

Example: ::

  use example\name_space;

  class class_name {
  }

+-----------------------------------+---------------+---------------------------------------------------------+
| Name                              | Default Value | Description                                             |
+===================================+===============+=========================================================+
| camelcase-abbreviations           | false         | Name should be CamelCase including abbreviations.       |
+-----------------------------------+---------------+---------------------------------------------------------+
| exceptions                        |               | Comma-separated class name list of exceptions.          |
+-----------------------------------+---------------+---------------------------------------------------------+

CamelCasePropertyName
=====================

Since: PHPMD 0.2

It is considered best practice to use the camelCase notation to name attributes.

Example: ::

  class ClassName {
      protected $property_name;
  }

This rule has the following properties:

+-----------------------------------+---------------+---------------------------------------------------------+
| Name                              | Default Value | Description                                             |
+===================================+===============+=========================================================+
| allow-underscore                  | false         | Allow an optional, single underscore at the beginning.  |
+-----------------------------------+---------------+---------------------------------------------------------+
| allow-underscore-test             | false         | Is it allowed to have underscores in test method names. |
+-----------------------------------+---------------+---------------------------------------------------------+
| camelcase-abbreviations           | false         | Name should be camelCase including abbreviations.       |
+-----------------------------------+---------------+---------------------------------------------------------+

CamelCaseMethodName
===================

Since: PHPMD 0.2

It is considered best practice to use the camelCase notation to name methods.

Example: ::

  class ClassName {
      public function get_name() {
      }
  }

This rule has the following properties:

+-----------------------------------+---------------+---------------------------------------------------------+
| Name                              | Default Value |  Description                                            |
+===================================+===============+=========================================================+
| allow-underscore                  | false         | Allow an optional, single underscore at the beginning.  |
+-----------------------------------+---------------+---------------------------------------------------------+
| allow-underscore-test             | false         | Is it allowed to have underscores in test method names. |
+-----------------------------------+---------------+---------------------------------------------------------+
| camelcase-abbreviations           | false         | Name should be camelCase including abbreviations.       |
+-----------------------------------+---------------+---------------------------------------------------------+

CamelCaseParameterName
======================

Since: PHPMD 0.2

It is considered best practice to use the camelCase notation to name parameters.

Example: ::

  class ClassName {
      public function doSomething($user_name) {
      }
  }

This rule has the following properties:

+-----------------------------------+---------------+---------------------------------------------------------+
| Name                              | Default Value | Description                                             |
+===================================+===============+=========================================================+
| allow-underscore                  | false         | Allow an optional, single underscore at the beginning.  |
+-----------------------------------+---------------+---------------------------------------------------------+
| camelcase-abbreviations           | false         | Name should be camelCase including abbreviations.       |
+-----------------------------------+---------------+---------------------------------------------------------+

CamelCaseVariableName
=====================

Since: PHPMD 0.2

It is considered best practice to use the camelCase notation to name variables.

Example: ::

  class ClassName {
      public function doSomething() {
          $data_module = new DataModule();
      }
  }

This rule has the following properties:

+-----------------------------------+---------------+---------------------------------------------------------+
| Name                              | Default Value | Description                                             |
+===================================+===============+=========================================================+
| allow-underscore                  | false         | Allow an optional, single underscore at the beginning.  |
+-----------------------------------+---------------+---------------------------------------------------------+
| camelcase-abbreviations           | false         | Name should be camelCase including abbreviations.       |
+-----------------------------------+---------------+---------------------------------------------------------+

Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
