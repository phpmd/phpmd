Type: (bugfix / feature / refactoring / documentation update)  
Issue: Resolves #.. the corresponding issue for this PR (if exist)
Breaking change: yes/no (if yes explain why)

<!--
Explain what the PR does and also why. If you have parts you are not sure about, please explain. 

Please check this points before submitting your PR.
 - Add test to cover the changes you made on the code.
 - If you have a change on the documentation, please link to the page that you change.
 - If you add a new feature please update the documentation in the same PR.
 - If you really need to add a breaking change, explain why it is needed. Understand that this result in a lower change to get the PR accepted.
 - Any PR need 2 approvals before it get merged, sometimes this can take some time. Please be patient.
  
 ## Adding a New Rule

- Add the new rule to the matching rule set XML, e.g. ``resources/rulesets/naming.xml``
- Add documentation for the new rule, e.g. ``public/rst/rules/naming.rst``
- Implement the new rule, e.g. ``src/Rule/Naming/LongVariable.php``
- Cover cases for the new rule in the rule test, e.g. ``tests/php/PHPMD/Rule/Naming/LongVariableTest.php``
-- Cover the case when the new rule *should* apply
-- Cover the case when the new rule *should not* apply
-- Cover edge cases of the new rule

## Adding a New Rule Property

- Add the new property to rule set XML, e.g. ``resources/rulesets/naming.xml``
- Add documentation for the new property, e.g. ``public/rst/rules/naming.rst``
- Implement new property in rule, e.g. ``src/Rule/Naming/LongVariable.php``
- Cover cases for the new property in rule test, e.g. ``tests/php/PHPMD/Rule/Naming/LongVariableTest.php``
-- Cover the case when the new property is not set and the rule *should not* apply
-- Cover the case when the new property is not set and the rule *should* apply
-- Cover case when the new property is set and the rule *should not* apply
-- Cover case when the new property is set and the rule *should* apply
-- Cover edge cases of the new property, if any
-->
