# Upgrading


## From PHPMD 2 to PHPMD3

Here we try to provide the needed changes to upgrade from version 2 to version 3. These are the breaking changes.

- Upgrade your PHP version. The minimum version is changed from 5.3.9 to 8.1.
- The command signature is changed from `phpmd <source> <report-format> <ruleset>`  to `phpmd analyze [options] [--] [<paths>...]`. You can run `phpmd --help` to see all the options.
  - The default format is text
  - The default ruleset is `"cleancode","codesize","controversial","design","naming","unusedcode","cleancode","codesize","controversial","design","naming","unusedcode"]`
  - The ` --ignore` option is replaced by the already existing `--exclude` option.

It is advised to run phpmd --help to see all provided options, and visit the rules documentation on [https://phpmd.org/rules/index.html](https://phpmd.org/rules/index.html).