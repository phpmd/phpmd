==========================
PHPMD Suppressing Warnings
==========================

You can use PHP attributes to exclude methods or classes from PHPMD or to
suppress specific rules for certain code elements.

The ``#[SuppressWarnings]`` attribute without arguments will suppress all
PHPMD warnings for the annotated class, method, or function.

.. code-block:: php

  use PHPMD\Attribute\SuppressWarnings;

  #[SuppressWarnings]
  class Bar {
      function foo() {
          $baz = 23;
      }
  }

You can also suppress a single rule by passing the rule class as argument.

.. code-block:: php

  use PHPMD\Attribute\SuppressWarnings;
  use PHPMD\Rule\UnusedLocalVariable;

  class Bar {
      #[SuppressWarnings(UnusedLocalVariable::class)]
      public function foo() {
          $baz = 42;
      }
  }

The attribute is repeatable, so you can suppress multiple rules on the same
element.

.. code-block:: php

  use PHPMD\Attribute\SuppressWarnings;
  use PHPMD\Rule\Naming\LongVariable;
  use PHPMD\Rule\UnusedLocalVariable;

  #[SuppressWarnings(LongVariable::class)]
  #[SuppressWarnings(UnusedLocalVariable::class)]
  class Bar {
      public function foo($thisIsALongAndUnusedVariable)
      {

      }
  }

.. note::

   The older ``@SuppressWarnings`` doc comment annotations from PHPMD 2.x are
   still supported for backward compatibility. However, PHP attributes are the
   preferred approach going forward.
