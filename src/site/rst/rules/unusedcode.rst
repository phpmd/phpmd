=================
Unused Code Rules
=================

The Unused Code Ruleset contains a collection of rules that find unused code.

UnusedPrivateField
==================

Since: PHPMD 0.2

Detects when a private field is declared and/or assigned a value, but not used.


Example: ::

  class Something
  {
      private static $FOO = 2; // Unused
      private $i = 5; // Unused
      private $j = 6;
      public function addOne()
      {
          return $this->j++;
      }
  }

UnusedLocalVariable
===================

Since: PHPMD 0.2

Detects when a local variable is declared and/or assigned, but not used.


Example: ::

  class Foo {
      public function doSomething()
      {
          $i = 5; // Unused
      }
  }

UnusedPrivateMethod
===================

Since: PHPMD 0.2

Unused Private Method detects when a private method is declared but is unused.


Example: ::

  class Something
  {
      private function foo() {} // unused
  }

UnusedFormalParameter
=====================

Since: PHPMD 0.2

Avoid passing parameters to methods or constructors and then not using those parameters.


Example: ::

  class Foo
  {
      private function bar($howdy)
      {
          // $howdy is not used
      }
  }


Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
        