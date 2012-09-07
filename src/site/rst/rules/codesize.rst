===============
Code Size Rules
===============

The Code Size Ruleset contains a collection of rules that find code size related problems.

CyclomaticComplexity
====================

Since: PHPMD 0.1

Complexity is determined by the number of decision points in a method plus one for the method entry. The decision points are 'if', 'while', 'for', and 'case labels'. Generally, 1-4 is low complexity, 5-7 indicates moderate complexity, 8-10 is high complexity, and 11+ is very high complexity.


Example: ::

  // Cyclomatic Complexity = 12
  class Foo {
  1   public function example()  {
  2       if ($a == $b)  {
  3           if ($a1 == $b1) {
                  fiddle();
  4           } else if ($a2 == $b2) {
                  fiddle();
              }  else {
                  fiddle();
              }
  5       } else if ($c == $d) {
  6           while ($c == $d) {
                  fiddle();
              }
  7        } else if ($e == $f) {
  8           for ($n = 0; $n < $h; $n++) {
                  fiddle();
              }
          } else{
              switch ($z) {
  9               case 1:
                      fiddle();
                      break;
  10              case 2:
                      fiddle();
                      break;
  11              case 3:
                      fiddle();
                      break;
  12              default:
                      fiddle();
                      break;
              }
          }
      }
  }

This rule has the following properties:

=================================== =============== ===================================================================
 Name                                Default Value   Description                                                       
=================================== =============== ===================================================================
 reportLevel                         10              The Cyclomatic Complexity reporting threshold                     
 showClassesComplexity               true            Indicate if class average violation should be added to the report 
 showMethodsComplexity               true            Indicate if class average violation should be added to the report 
=================================== =============== ===================================================================

NPathComplexity
===============

Since: PHPMD 0.1

The NPath complexity of a method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.


Example: ::

  class Foo {
      function bar() {
          // lots of complicated code
      }
  }

This rule has the following properties:

=================================== =============== ===============================
 Name                                Default Value   Description                   
=================================== =============== ===============================
 minimum                             200             The npath reporting threshold 
=================================== =============== ===============================

ExcessiveMethodLength
=====================

Since: PHPMD 0.1

Violations of this rule usually indicate that the method is doing too much. Try to reduce the method size by creating helper methods and removing any copy/pasted code.


Example: ::

  class Foo {
      public function doSomething() {
          print("Hello world!" . PHP_EOL);
          print("Hello world!" . PHP_EOL);
          // 98 copies omitted for brevity.
      }
  }

This rule has the following properties:

=================================== =============== =====================================
 Name                                Default Value   Description                         
=================================== =============== =====================================
 minimum                             100             The method size reporting threshold 
=================================== =============== =====================================

ExcessiveClassLength
====================

Since: PHPMD 0.1

Long Class files are indications that the class may be trying to do too much. Try to break it down, and reduce the size to something manageable.


Example: ::

  class Foo {
    public function bar() {
      // 1000 lines of code
    }
  }

This rule has the following properties:

=================================== =============== ====================================
 Name                                Default Value   Description                        
=================================== =============== ====================================
 minimum                             1000            The class size reporting threshold 
=================================== =============== ====================================

ExcessiveParameterList
======================

Since: PHPMD 0.1

Long parameter lists can indicate that a new object should be created to wrap the numerous parameters. Basically, try to group the parameters together.


Example: ::

  class Foo {
      public function addData(
          $p0, $p1, $p2, $p3, $p4, $p5,
          $p5, $p6, $p7, $p8, $p9, $p10) {
      }
  }

This rule has the following properties:

=================================== =============== =========================================
 Name                                Default Value   Description                             
=================================== =============== =========================================
 minimum                             10              The parameter count reporting threshold 
=================================== =============== =========================================

ExcessivePublicCount
====================

Since: PHPMD 0.1

A large number of public methods and attributes declared in a class can indicate the class may need to be broken up as increased effort will be required to thoroughly test it.


Example: ::

  public class Foo {
      public $value;
      public $something;
      public $var;
      // [... more more public attributes ...]
  
      public function doWork() {}
      public function doMoreWork() {}
      public function doWorkAgain() {}
      // [... more more public methods ...]
  }

This rule has the following properties:

=================================== =============== =====================================
 Name                                Default Value   Description                         
=================================== =============== =====================================
 minimum                             45              The public item reporting threshold 
=================================== =============== =====================================

TooManyFields
=============

Since: PHPMD 0.1

Classes that have too many fields could be redesigned to have fewer fields, possibly through some nested object grouping of some of the information. For example, a class with city/state/zip fields could instead have one Address field.


Example: ::

  class Person {
     protected $one;
     private $two;
     private $three;
     [... many more fields ...]
  }

This rule has the following properties:

=================================== =============== ======================================
 Name                                Default Value   Description                          
=================================== =============== ======================================
 maxfields                           15              The field count reporting threshold  
=================================== =============== ======================================

TooManyMethods
==============

Since: PHPMD 0.1

A class with too many methods is probably a good suspect for refactoring, in order to reduce its complexity and find a way to have more fine grained objects.

This rule has the following properties:

=================================== =============== =======================================
 Name                                Default Value   Description                           
=================================== =============== =======================================
 maxmethods                          10              The method count reporting threshold  
=================================== =============== =======================================

ExcessiveClassComplexity
========================

Since: PHPMD 0.2.5

The WMC of a class is a good indicator of how much time and effort is required to modify and maintain this class. A large number of methods also means that this class has a greater potential impact on derived classes.


Example: ::

  class Foo {
      public function bar()  {
          if ($a == $b)  {
              if ($a1 == $b1) {
                  fiddle();
              } else if ($a2 == $b2) {
                  fiddle();
              }  else {
              }
          }
      }
      public function baz()  {
          if ($a == $b)  {
              if ($a1 == $b1) {
                  fiddle();
              } else if ($a2 == $b2) {
                  fiddle();
              }  else {
              }
          }
      }
      // Several other complex methods
  }

This rule has the following properties:

=================================== =============== ========================================
 Name                                Default Value   Description                            
=================================== =============== ========================================
 maximum                             50              The maximum WMC tolerable for a class. 
=================================== =============== ========================================


Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
        