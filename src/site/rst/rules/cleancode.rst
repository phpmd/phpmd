================
Clean Code Rules
================

The Clean Code ruleset contains a collection of rules that enforce
software design principles such as the SOLID Principles and Object
Callisthenics.

They are very strict and cannot easily be followed without any violations.
If you use this ruleset you should:

1. Select important packages that should follow this ruleset and others that
   don't
2. Set a treshold for failures and not fail at the first occurance.

ElseExpression
==============

Since: PHPMD 1.5

An if expression with an else branch is never necessary. You can rewrite the
conditions in a way that the else is not necessary and the code becomes simpler
to read.  To achieve this use early return statements. To achieve this you may
need to split the code it several smaller methods. For very simple assignments
you could also use the ternary operations.

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

BooleanArgumentFlag
===================

A boolean flag argument is a reliable indicator for a violation of
the Single Responsibility Principle (SRP). You can fix this problem
by extracting the logic in the boolean flag into its own class
or method.

Example: ::

    class Foo {
        public function bar($flag = true) {
        }
    }

StaticAccess
============

Static acccess causes inexchangable dependencies to other classes and leads to
hard to test code. Avoid using static access at all costs and instead inject
dependencies through the constructor. The only case when static access is
acceptable is when used for factory methods.

Example: ::

    class Foo
    {
        public function bar()
        {
            Bar::baz();
        }
    }
