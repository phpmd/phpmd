============
Design Rules
============

The Code Size Ruleset contains a collection of rules that find software design related problems.

ExitExpression
==============

Since: PHPMD 0.2

An exit-expression within regular code is untestable and therefore it should be avoided. Consider to move the exit-expression into some kind of startup script where an error/exception code is returned to the calling environment.


Example: ::

  class Foo {
      public function bar($param)  {
          if ($param === 42) {
              exit(23);
          }
      }
  }

EvalExpression
==============

Since: PHPMD 0.2

An eval-expression is untestable, a security risk and bad practice. Therefore it should be avoided. Consider to replace the eval-expression with regular code.


Example: ::

  class Foo {
      public function bar($param)  {
          if ($param === 42) {
              eval('$param = 23;');
          }
      }
  }

GotoStatement
=============

Since: PHPMD 1.1.0

Goto makes code harder to read and it is nearly impossible to understand the control flow of an application that uses this language construct. Therefore it should be avoided. Consider to replace Goto with regular control structures and separate methods/function, which are easier to read.


Example: ::

  class Foo {
      public function bar($param)  {
          A:
          if ($param === 42) {
              goto X;
          }
          Y:
          if (time() % 42 === 23) {
              goto Z;
          }
          X:
          if (time() % 23 === 42) {
              goto Y;
          }
          Z:
          return 42;                 
      }
  }

NumberOfChildren
================

Since: PHPMD 0.2

A class with an excessive number of children is an indicator for an unbalanced class hierarchy. You should consider to refactor this class hierarchy.



This rule has the following properties:

=================================== =============== =============================================
 Name                                Default Value   Description                                 
=================================== =============== =============================================
 minimum                             15              Maximum number of acceptable child classes. 
=================================== =============== =============================================

DepthOfInheritance
==================

Since: PHPMD 0.2

A class with many parents is an indicator for an unbalanced and wrong class hierarchy. You should consider to refactor this class hierarchy.



This rule has the following properties:

=================================== =============== ==============================================
 Name                                Default Value   Description                                  
=================================== =============== ==============================================
 minimum                             6               Maximum number of acceptable parent classes. 
=================================== =============== ==============================================

CouplingBetweenObjects
======================

Since: PHPMD 1.1.0

A class with to many dependencies has negative impacts on several quality aspects of a class. This includes quality criterias like stability, maintainability and understandability


Example: ::

  class Foo {
      /**
       * @var \foo\bar\X
       */
      private $x = null;
  
      /**
       * @var \foo\bar\Y
       */
      private $y = null;
  
      /**
       * @var \foo\bar\Z
       */
      private $z = null;
  
      public function setFoo(\Foo $foo) {}
      public function setBar(\Bar $bar) {}
      public function setBaz(\Baz $baz) {}
  
      /**
       * @return \SplObjectStorage
       * @throws \OutOfRangeException
       * @throws \InvalidArgumentException
       * @throws \ErrorException
       */
      public function process(Iterator $it) {}
  
      // ...
  }

This rule has the following properties:

=================================== =============== ============================================
 Name                                Default Value   Description                                
=================================== =============== ============================================
 minimum                             13              Maximum number of acceptable dependencies. 
=================================== =============== ============================================


Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
        