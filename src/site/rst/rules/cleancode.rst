================
Clean Code Rules
================

The Clean Code ruleset contains rules that enforce a clean code base. This includes rules from SOLID and object calisthenics.

BooleanArgumentFlag
===================

Since: PHPMD 1.4.0

A boolean flag argument is a reliable indicator for a violation of the Single Responsibility Principle (SRP). You can fix this problem by extracting the logic in the boolean flag into its own class or method.


Example: ::

  class Foo {
      public function bar($flag = true) {
      }
  }

ElseExpression
==============

Since: PHPMD 1.4.0

An if expression with an else branch is basically not necessary. You can rewrite the conditions in a way that the else clause is not necessary and the code becomes simpler to read. To achieve this, use early return statements, though you may need to split the code it several smaller methods. For very simple assignments you could also use the ternary operations.


Example: ::

  class Foo
  {
      public function bar($flag)
      {
          if ($flag) {
              // one branch
          } else {
              // another branch
          }
      }
  }

StaticAccess
============

Since: PHPMD 1.4.0

Static access causes unexchangeable dependencies to other classes and leads to hard to test code. Avoid using static access at all costs and instead inject dependencies through the constructor. The only case when static access is acceptable is when used for factory methods.


Example: ::

  class Foo
  {
      public function bar()
      {
          Bar::baz();
      }
  }

This rule has the following properties:

=================================== =============== ===============================================
 Name                                Default Value   Description                                   
=================================== =============== ===============================================
 exceptions                                          Comma-separated class name list of exceptions 
=================================== =============== ===============================================

IfStatementAssignment
=====================

Since: PHPMD 2.7.0

Assignments in if clauses and the like are considered a code smell. Assignments in PHP return the right operand as their result. In many cases, this is an expected behavior, but can lead to many difficult to spot bugs, especially when the right operand could result in zero, null or an empty string.


Example: ::

  class Foo
  {
      public function bar($flag)
      {
          if ($foo = 'bar') { // possible typo
              // ...
          }
          if ($baz = 0) { // always false
              // ...
          }
      }
  }


IfStatementWithoutLogic
=======================

Since: PHPMD 2.7.0

Having if conditions without any logic should be avoided. It usually tells us, that we are dealing with development code fragment, debug data, etc.


Example: ::

  class Foo
  {
      public function bar()
      {
          if (true) { // always true
              // ...
          }
          if (1 !== 1) { // always false
              // ...
          }
      }
  }


Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
        