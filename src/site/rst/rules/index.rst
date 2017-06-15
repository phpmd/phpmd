================
Current Rulesets
================

List of rulesets and rules contained in each ruleset.

- `Clean Code Rules`__: The Clean Code ruleset contains rules that enforce a clean code base. This includes rules from SOLID and object calisthenics.
- `Code Size Rules`__: The Code Size Ruleset contains a collection of rules that find code size related problems.
- `Controversial Rules`__: This ruleset contains a collection of controversial rules.
- `Design Rules`__: The Design Ruleset contains a collection of rules that find software design related problems.
- `Naming Rules`__: The Naming Ruleset contains a collection of rules about names - too long, too short, and so forth.
- `Unused Code Rules`__: The Unused Code Ruleset contains a collection of rules that find unused code.

__ index.html#clean-code-rules
__ index.html#code-size-rules
__ index.html#controversial-rules
__ index.html#design-rules
__ index.html#naming-rules
__ index.html#unused-code-rules

Clean Code Rules
================

- `BooleanArgumentFlag`__: A boolean flag argument is a reliable indicator for a violation of the Single Responsibility Principle (SRP). You can fix this problem by extracting the logic in the boolean flag into its own class or method.
- `ElseExpression`__: An if expression with an else branch is basically not necessary. You can rewrite the conditions in a way that the else clause is not necessary and the code becomes simpler to read. To achieve this, use early return statements, though you may need to split the code it several smaller methods. For very simple assignments you could also use the ternary operations.
- `StaticAccess`__: Static access causes unexchangeable dependencies to other classes and leads to hard to test code. Avoid using static access at all costs and instead inject dependencies through the constructor. The only case when static access is acceptable is when used for factory methods.
- `DuplicateArrayKey`__: Defining another value for the same key in an array literal overrides the previous key/value, which makes it effectively an unused code. If it's known from the beginning that the key will have different value, there is usually no point in defining first one.

__ cleancode.html#booleanargumentflag
__ cleancode.html#elseexpression
__ cleancode.html#staticaccess
__ cleancode.html#duplicatearraykey

Code Size Rules
===============

- `CyclomaticComplexity`__: Complexity is determined by the number of decision points in a method plus one for the method entry. The decision points are 'if', 'while', 'for', and 'case labels'. Generally, 1-4 is low complexity, 5-7 indicates moderate complexity, 8-10 is high complexity, and 11+ is very high complexity.
- `NPathComplexity`__: The NPath complexity of a method is the number of acyclic execution paths through that method. A threshold of 200 is generally considered the point where measures should be taken to reduce complexity.
- `ExcessiveMethodLength`__: Violations of this rule usually indicate that the method is doing too much. Try to reduce the method size by creating helper methods and removing any copy/pasted code.
- `ExcessiveClassLength`__: Long Class files are indications that the class may be trying to do too much. Try to break it down, and reduce the size to something manageable.
- `ExcessiveParameterList`__: Long parameter lists can indicate that a new object should be created to wrap the numerous parameters. Basically, try to group the parameters together.
- `ExcessivePublicCount`__: A large number of public methods and attributes declared in a class can indicate the class may need to be broken up as increased effort will be required to thoroughly test it.
- `TooManyFields`__: Classes that have too many fields could be redesigned to have fewer fields, possibly through some nested object grouping of some of the information. For example, a class with city/state/zip fields could instead have one Address field.
- `TooManyMethods`__: A class with too many methods is probably a good suspect for refactoring, in order to reduce its complexity and find a way to have more fine grained objects. By default it ignores methods starting with 'get' or 'set'. The default was changed from 10 to 25 in PHPMD 2.3.
- `TooManyPublicMethods`__: A class with too many public methods is probably a good suspect for refactoring, in order to reduce its complexity and find a way to have more fine grained objects. By default it ignores methods starting with 'get' or 'set'.
- `ExcessiveClassComplexity`__: The Weighted Method Count (WMC) of a class is a good indicator of how much time and effort is required to modify and maintain this class. The WMC metric is defined as the sum of complexities of all methods declared in a class. A large number of methods also means that this class has a greater potential impact on derived classes.

__ codesize.html#cyclomaticcomplexity
__ codesize.html#npathcomplexity
__ codesize.html#excessivemethodlength
__ codesize.html#excessiveclasslength
__ codesize.html#excessiveparameterlist
__ codesize.html#excessivepubliccount
__ codesize.html#toomanyfields
__ codesize.html#toomanymethods
__ codesize.html#toomanypublicmethods
__ codesize.html#excessiveclasscomplexity

Controversial Rules
===================

- `Superglobals`__: Accessing a super-global variable directly is considered a bad practice. These variables should be encapsulated in objects that are provided by a framework, for instance.
- `CamelCaseClassName`__: It is considered best practice to use the CamelCase notation to name classes.
- `CamelCasePropertyName`__: It is considered best practice to use the camelCase notation to name attributes.
- `CamelCaseMethodName`__: It is considered best practice to use the camelCase notation to name methods.
- `CamelCaseParameterName`__: It is considered best practice to use the camelCase notation to name parameters.
- `CamelCaseVariableName`__: It is considered best practice to use the camelCase notation to name variables.

__ controversial.html#superglobals
__ controversial.html#camelcaseclassname
__ controversial.html#camelcasepropertyname
__ controversial.html#camelcasemethodname
__ controversial.html#camelcaseparametername
__ controversial.html#camelcasevariablename

Design Rules
============

- `ExitExpression`__: An exit-expression within regular code is untestable and therefore it should be avoided. Consider to move the exit-expression into some kind of startup script where an error/exception code is returned to the calling environment.
- `EvalExpression`__: An eval-expression is untestable, a security risk and bad practice. Therefore it should be avoided. Consider to replace the eval-expression with regular code.
- `GotoStatement`__: Goto makes code harder to read and it is nearly impossible to understand the control flow of an application that uses this language construct. Therefore it should be avoided. Consider to replace Goto with regular control structures and separate methods/function, which are easier to read.
- `NumberOfChildren`__: A class with an excessive number of children is an indicator for an unbalanced class hierarchy. You should consider to refactor this class hierarchy.
- `DepthOfInheritance`__: A class with many parents is an indicator for an unbalanced and wrong class hierarchy. You should consider to refactor this class hierarchy.
- `CouplingBetweenObjects`__: A class with too many dependencies has negative impacts on several quality aspects of a class. This includes quality criteria like stability, maintainability and understandability
- `DevelopmentCodeFragment`__: Functions like var_dump(), print_r() etc. are normally only used during development and therefore such calls in production code are a good indicator that they were just forgotten.
- `IfStatementAssignment`__: Assignments in if clauses and the like are considered a code smell. Assignments in PHP return the right operand as their result. In many cases, this is an expected behavior, but can lead to many difficult to spot bugs, especially when the right operand could result in zero, null or an empty string.
- `EmptyCatchBlock`__: Usually empty try-catch is a bad idea because you are silently swallowing an error condition and then continuing execution. Occasionally this may be the right thing to do, but often it's a sign that a developer saw an exception, didn't know what to do about it, and so used an empty catch to silence the problem.
- `CountInLoopExpression`__: Using count/sizeof in loops expressions is considered bad practice and is a potential source of
many bugs, especially when the loop manipulates an array, as count happens on each iteration.

__ design.html#exitexpression
__ design.html#evalexpression
__ design.html#gotostatement
__ design.html#numberofchildren
__ design.html#depthofinheritance
__ design.html#couplingbetweenobjects
__ design.html#developmentcodefragment
__ design.html#ifstatementassignment
__ design.html#emptycatchblock
__ design.html#countinloopexpression

Naming Rules
============

- `ShortVariable`__: Detects when a field, local, or parameter has a very short name.
- `LongVariable`__: Detects when a field, formal or local variable is declared with a long name.
- `ShortMethodName`__: Detects when very short method names are used.
- `ConstructorWithNameAsEnclosingClass`__: A constructor method should not have the same name as the enclosing class, consider to use the PHP 5 __construct method.
- `ConstantNamingConventions`__: Class/Interface constant names should always be defined in uppercase.
- `BooleanGetMethodName`__: Looks for methods named 'getX()' with 'boolean' as the return type. The convention is to name these methods 'isX()' or 'hasX()'.

__ naming.html#shortvariable
__ naming.html#longvariable
__ naming.html#shortmethodname
__ naming.html#constructorwithnameasenclosingclass
__ naming.html#constantnamingconventions
__ naming.html#booleangetmethodname

Unused Code Rules
=================

- `UnusedPrivateField`__: Detects when a private field is declared and/or assigned a value, but not used.
- `UnusedLocalVariable`__: Detects when a local variable is declared and/or assigned, but not used.
- `UnusedPrivateMethod`__: Unused Private Method detects when a private method is declared but is unused.
- `UnusedFormalParameter`__: Avoid passing parameters to methods or constructors and then not using those parameters.

__ unusedcode.html#unusedprivatefield
__ unusedcode.html#unusedlocalvariable
__ unusedcode.html#unusedprivatemethod
__ unusedcode.html#unusedformalparameter

Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
