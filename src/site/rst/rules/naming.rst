============
Naming Rules
============

The Naming Ruleset contains a collection of rules about names - too long, too short, and so forth.

ShortVariable
=============

Since: PHPMD 0.2

Detects when a field, local, or parameter has a very short name.


Example: ::

  class Something {
      private $q = 15; // VIOLATION - Field
      public static function main( array $as ) {  // VIOLATION - Formal
          $r = 20 + $this->q; // VIOLATION - Local
          for (int $i = 0; $i < 10; $i++) { // Not a Violation (inside FOR)
              $r += $this->q;
          }
      }
  }

This rule has the following properties:

=================================== =============== ===========================================================
 Name                                Default Value   Description                                               
=================================== =============== ===========================================================
 minimum                             3               Minimum length for a variable, property or parameter name 
=================================== =============== ===========================================================

LongVariable
============

Since: PHPMD 0.2

Detects when a field, formal or local variable is declared with a long name.


Example: ::

  class Something {
      protected $reallyLongIntName = -3;  // VIOLATION - Field
      public static function main( array $argumentsList[] ) { // VIOLATION - Formal
          $otherReallyLongName = -5; // VIOLATION - Local
          for ($interestingIntIndex = 0;  // VIOLATION - For
               $interestingIntIndex < 10;
               $interestingIntIndex++ ) {
          }
      }
  }

This rule has the following properties:

=================================== =============== =========================================
 Name                                Default Value   Description                             
=================================== =============== =========================================
 maximum                             20              The variable length reporting threshold 
=================================== =============== =========================================

ShortMethodName
===============

Since: PHPMD 0.2

Detects when very short method names are used.


Example: ::

  class ShortMethod {
      public function a( $index ) { // Violation
      }
  }

This rule has the following properties:

=================================== =============== ==============================================
 Name                                Default Value   Description                                  
=================================== =============== ==============================================
 minimum                             3               Minimum length for a method or function name 
=================================== =============== ==============================================

ConstructorWithNameAsEnclosingClass
===================================

Since: PHPMD 0.2

A constructor method should not have the same name as the enclosing class, consider to use the PHP 5 __construct method.


Example: ::

  class MyClass {
       // this is bad because it is PHP 4 style
      public function MyClass() {}
      // this is good because it is a PHP 5 constructor
      public function __construct() {}
  }

ConstantNamingConventions
=========================

Since: PHPMD 0.2

Class/Interface constant nanmes should always be defined in uppercase.


Example: ::

  class Foo {
      const MY_NUM = 0; // ok
      const myTest = ""; // fail
  }

BooleanGetMethodName
====================

Since: PHPMD 0.2

Looks for methods named 'getX()' with 'boolean' as the return type. The convention is to name these methods 'isX()' or 'hasX()'.


Example: ::

  class Foo {
      /**
       * @return boolean
       */
      public function getFoo() {} // bad
      /**
       * @return bool
       */
      public function isFoo(); // ok
      /**
       * @return boolean
       */
      public function getFoo($bar); // ok, unless checkParameterizedMethods=true
  }

This rule has the following properties:

=================================== =============== ============================================================
 Name                                Default Value   Description                                                
=================================== =============== ============================================================
 checkParameterizedMethods           false           Applies only to methods without parameter when set to true 
=================================== =============== ============================================================


Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
        