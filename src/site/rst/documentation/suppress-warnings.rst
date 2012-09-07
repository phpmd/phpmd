=========================
PHPMD Supressing Warnings
=========================

You can use doc comment annotations to exclude methods or classes 
from PHPMD or to suppress special rules for some software artifacts. ::

  /**
   * This will suppress all the PMD warnings in
   * this class.
   *
   * @SuppressWarnings(PHPMD)
   */
  class Bar {
      function  foo() {
          $baz = 23;
      }
  }

Or you can suppress one rule with an annotation like this: ::

  /**
   *
   */
  class Bar {
      /**
       * This will suppress UnusedLocalVariable
       * warnings in this method
       *
       * @SuppressWarnings(PHPMD.UnusedLocalVariable)
       */
      public function foo() {
          $baz = 42;
      }
  }

The ``@SuppressWarnings`` annotation of PHPMD also supports some
wildcard exclusion, so that you can suppress several warnings with
a single annotation. ::

  /**
   * Suppress all rules containing "unused" in this
   * class
   *
   * @SuppressWarnings("unused")
   */
  class Bar {
      private $unusedPrivateField = 42;
      public function foo($unusedFormalParameter = 23)
      {
          $unusedLocalVariable = 17;
      }
      private function unusedPrivateMethod() {
      }
  }

A doc comment can contain multiple ``@SuppressWarnings`` annotations,
so that you can exclude multiple rules by name. ::

  /**
   * Suppress all warnings from these two rules.
   *
   * @SuppressWarnings(PHPMD.LongVariable)
   * @SuppressWarnings(PHPMD.UnusedLocalVariable)
   */
  class Bar {
      public function foo($thisIsALongAndUnusedVariable)
      {

      }
  }
