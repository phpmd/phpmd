================
Current Rulesets
================

List of rulesets and rules contained in each ruleset.

- `Clean Code Rules <#clean-code-rules>`_: The Clean Code ruleset contains rules that enforce a clean code base. This includes rules from SOLID and object calisthenics.
- `Code Size Rules <#code-size-rules>`_: The Code Size Ruleset contains a collection of rules that find code size related problems.
- `Controversial Rules <#controversial-rules>`_: This ruleset contains a collection of controversial rules.
- `Design Rules <#design-rules>`_: The Design Ruleset contains a collection of rules that find software design related problems.
- `Naming Rules <#naming-rules>`_: The Naming Ruleset contains a collection of rules about names - too long, too short, and so forth.
- `Unused Code Rules <#unused-code-rules>`_: The Unused Code Ruleset contains a collection of rules that find unused code.

Clean Code Rules
================

- `BooleanArgumentFlag <cleancode.html#booleanargumentflag>`_: A boolean flag argument is a reliable indicator for a violation of the Single Responsibility Principle (SRP). You can fix this problem by extracting the logic in the boolean flag into its own class or method.
- `ElseExpression <cleancode.html#elseexpression>`_: An if expression with an else branch is basically not necessary. You can rewrite the conditions in a way that the else clause is not necessary and the code becomes simpler to read. To achieve this, use early return statements, though you may need to split the code in several smaller methods. For very simple assignments you could also use the ternary operations.
- `StaticAccess <cleancode.html#staticaccess>`_: Static access causes unexchangeable dependencies to other classes and leads to hard to test code. Avoid using static access at all costs and instead inject dependencies through the constructor. The only case when static access is acceptable is when used for factory methods.
- `DuplicateArrayKey <cleancode.html#duplicatearraykey>`_: Defining another value for the same key in an array literal overrides the previous key/value, which makes it effectively an unused code. If it's known from the beginning that the key will have different value, there is usually no point in defining first one.

Code Size Rules
===============

- `CyclomaticComplexity <codesize.html#cyclomaticcomplexity>`_: Complexity is determined by the number of decision points in a method plus one for the method entry. The decision points are 'if', 'while', 'for', and 'case labels'. Generally, 1-4 is low complexity, 5-7 indicates moderate complexity, 8-10 is high complexity, and 11+ is very high complexity.
- `NPathComplexity <codesize.html#npathcomplexity>`_: The NPath complexity of a method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.
- `ExcessiveMethodLength <codesize.html#excessivemethodlength>`_: Violations of this rule usually indicate that the method is doing too much. Try to reduce the method size by creating helper methods and removing any copy/pasted code.
- `ExcessiveClassLength <codesize.html#excessiveclasslength>`_: Long Class files are indications that the class may be trying to do too much. Try to break it down, and reduce the size to something manageable.
- `ExcessiveParameterList <codesize.html#excessiveparameterlist>`_: Long parameter lists can indicate that a new object should be created to wrap the numerous parameters. Basically, try to group the parameters together.
- `ExcessivePublicCount <codesize.html#excessivepubliccount>`_: A large number of public methods and attributes declared in a class can indicate the class may need to be broken up as increased effort will be required to thoroughly test it.
- `TooManyFields <codesize.html#toomanyfields>`_: Classes that have too many fields could be redesigned to have fewer fields, possibly through some nested object grouping of some of the information. For example, a class with city/state/zip fields could instead have one Address field.
- `TooManyMethods <codesize.html#toomanymethods>`_: A class with too many methods is probably a good suspect for refactoring, in order to reduce its complexity and find a way to have more fine grained objects. By default it ignores methods starting with 'get' or 'set'. The default was changed from 10 to 25 in PHPMD 2.3.
- `TooManyPublicMethods <codesize.html#toomanypublicmethods>`_: A class with too many public methods is probably a good suspect for refactoring, in order to reduce its complexity and find a way to have more fine grained objects. By default it ignores methods starting with 'get' or 'set'.
- `ExcessiveClassComplexity <codesize.html#excessiveclasscomplexity>`_: The Weighted Method Count (WMC) of a class is a good indicator of how much time and effort is required to modify and maintain this class. The WMC metric is defined as the sum of complexities of all methods declared in a class. A large number of methods also means that this class has a greater potential impact on derived classes.

Controversial Rules
===================

- `Superglobals <controversial.html#superglobals>`_: Accessing a super-global variable directly is considered a bad practice. These variables should be encapsulated in objects that are provided by a framework, for instance.
- `CamelCaseClassName <controversial.html#camelcaseclassname>`_: It is considered best practice to use the CamelCase notation to name classes.
- `CamelCasePropertyName <controversial.html#camelcasepropertyname>`_: It is considered best practice to use the camelCase notation to name attributes.
- `CamelCaseMethodName <controversial.html#camelcasemethodname>`_: It is considered best practice to use the camelCase notation to name methods.
- `CamelCaseParameterName <controversial.html#camelcaseparametername>`_: It is considered best practice to use the camelCase notation to name parameters.
- `CamelCaseVariableName <controversial.html#camelcasevariablename>`_: It is considered best practice to use the camelCase notation to name variables.

Design Rules
============

- `ExitExpression <design.html#exitexpression>`_: An exit-expression within regular code is untestable and therefore it should be avoided. Consider to move the exit-expression into some kind of startup script where an error/exception code is returned to the calling environment.
- `EvalExpression <design.html#evalexpression>`_: An eval-expression is untestable, a security risk and bad practice. Therefore it should be avoided. Consider to replace the eval-expression with regular code.
- `GotoStatement <design.html#gotostatement>`_: Goto makes code harder to read and it is nearly impossible to understand the control flow of an application that uses this language construct. Therefore it should be avoided. Consider to replace Goto with regular control structures and separate methods/function, which are easier to read.
- `NumberOfChildren <design.html#numberofchildren>`_: A class with an excessive number of children is an indicator for an unbalanced class hierarchy. You should consider to refactor this class hierarchy.
- `DepthOfInheritance <design.html#depthofinheritance>`_: A class with many parents is an indicator for an unbalanced and wrong class hierarchy. You should consider to refactor this class hierarchy.
- `CouplingBetweenObjects <design.html#couplingbetweenobjects>`_: A class with too many dependencies has negative impacts on several quality aspects of a class. This includes quality criteria like stability, maintainability and understandability
- `DevelopmentCodeFragment <design.html#developmentcodefragment>`_: Functions like var_dump(), print_r() etc. are normally only used during development and therefore such calls in production code are a good indicator that they were just forgotten.
- `IfStatementAssignment <design.html#ifstatementassignment>`_: Assignments in if clauses and the like are considered a code smell. Assignments in PHP return the right operand as their result. In many cases, this is an expected behavior, but can lead to many difficult to spot bugs, especially when the right operand could result in zero, null or an empty string.
- `EmptyCatchBlock <design.html#emptycatchblock>`_: Usually empty try-catch is a bad idea because you are silently swallowing an error condition and then continuing execution. Occasionally this may be the right thing to do, but often it's a sign that a developer saw an exception, didn't know what to do about it, and so used an empty catch to silence the problem.
- `CountInLoopExpression <design.html#countinloopexpression>`_: Using count/sizeof in loops expressions is considered bad practice and is a potential source of
many bugs, especially when the loop manipulates an array, as count happens on each iteration.

Naming Rules
============

- `ShortVariable <naming.html#shortvariable>`_: Detects when a field, local, or parameter has a very short name.
- `LongVariable <naming.html#longvariable>`_: Detects when a field, formal or local variable is declared with a long name.
- `ShortMethodName <naming.html#shortmethodname>`_: Detects when very short method names are used.
- `ConstructorWithNameAsEnclosingClass <naming.html#constructorwithnameasenclosingclass>`_: A constructor method should not have the same name as the enclosing class, consider to use the PHP 5 __construct method.
- `ConstantNamingConventions <naming.html#constantnamingconventions>`_: Class/Interface constant names should always be defined in uppercase.
- `BooleanGetMethodName <naming.html#booleangetmethodname>`_: Looks for methods named 'getX()' with 'boolean' as the return type. The convention is to name these methods 'isX()' or 'hasX()'.

Unused Code Rules
=================

- `UnusedPrivateField <unusedcode.html#unusedprivatefield>`_: Detects when a private field is declared and/or assigned a value, but not used.
- `UnusedLocalVariable <unusedcode.html#unusedlocalvariable>`_: Detects when a local variable is declared and/or assigned, but not used.
- `UnusedPrivateMethod <unusedcode.html#unusedprivatemethod>`_: Unused Private Method detects when a private method is declared but is unused.
- `UnusedFormalParameter <unusedcode.html#unusedformalparameter>`_: Avoid passing parameters to methods or constructors and then not using those parameters.

Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
