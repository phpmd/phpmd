# Upgrading


## From PHPMD 2 to PHPMD 3

Here we try to provide the needed changes to upgrade from version 2 to version 3. These are the breaking changes.

### PHP version

The minimum PHP version is changed from 5.3.9 to 8.1.

### Command line interface

The command signature has changed. In PHPMD 2, format and ruleset were positional arguments:

```
phpmd <source> <report-format> <ruleset>
```

In PHPMD 3, only the source paths are positional. Format and ruleset are now options:

```
phpmd analyze [options] [--] [<paths>...]
```

For example, what was previously:

```
phpmd src/ text codesize,unusedcode
```

Is now:

```
phpmd analyze --ruleset codesize --ruleset unusedcode src/
```

Both `--format` and `--ruleset` are optional. The default format is `text`, and the default ruleset includes all built-in rules. PHPMD will also auto-detect a configuration file in the current directory (see below).

The following CLI options have been removed or renamed:

| PHPMD 2               | PHPMD 3             |
|-----------------------|---------------------|
| `--ignore`            | `--exclude`         |
| `--reportfile`        | `--reportfile-text`, `--reportfile-xml`, etc. |
| `--minimumpriority`   | `--minimum-priority` |
| `--maximumpriority`   | `--maximum-priority` |

Run `phpmd analyze --help` to see all available options.

### New configuration formats

In addition to XML, PHPMD 3 supports rule set configuration in YAML, JSON, and PHP. YAML is the recommended format for new projects. See the [creating a custom rule set](https://phpmd.org/documentation/creating-a-ruleset.html) documentation for details.

Exclude patterns can now also be configured directly in the rule set file using the `exclude-pattern` key, instead of relying on the `--exclude` CLI option.

### Configuration file auto-detection

PHPMD 3 will automatically look for a configuration file in the current working directory. The following file names are detected, in order of priority:

- `phpmd.yml`, `phpmd.yaml`, `phpmd.json`, `phpmd.xml`, `phpmd.php`
- `.phpmd.yml`, `.phpmd.yaml`, `.phpmd.json`, `.phpmd.xml`, `.phpmd.php`
- `phpmd.yml.dist`, `phpmd.yaml.dist`, `phpmd.json.dist`, `phpmd.xml.dist`, `phpmd.php.dist`

This means you can place a `phpmd.yml` in your project root and simply run `phpmd analyze src/` without specifying a ruleset.

### Suppressing warnings

PHPMD 3 introduces PHP attributes as the preferred way to suppress warnings:

```php
use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule\UnusedLocalVariable;

#[SuppressWarnings(UnusedLocalVariable::class)]
public function example() {
    $unused = 42;
}
```

The older `@SuppressWarnings` doc comment annotations from PHPMD 2 are still supported for backward compatibility:

```php
/** @SuppressWarnings(PHPMD.UnusedLocalVariable) */
public function example() {
    $unused = 42;
}
```

However, PHP attributes are recommended going forward, as they are type-safe and supported by IDE autocompletion.

### Exit codes

A new exit code `3` has been added, which indicates that one or more files could not be processed because of an error. Previously this would have been exit code `1`.

### Internal API changes

These changes only affect you if you have written custom rules or extended PHPMD classes.

- The `PHP_PMD_*` class aliases from PHPMD 1.x were already removed in 2.9. If you still use them, update to the `PHPMD\*` namespace.
- `PHPMD\PHPMD::getIgnorePatterns()` and `setIgnorePatterns()` have been removed. Use `getExcludePatterns()` and `addExcludePatterns()` instead.
- All PHPMD exceptions now use a dedicated exception hierarchy under `PHPMD\Exception\`.
- Rule marker interfaces now include `EnumAware` and `TraitAware` in addition to the existing `ClassAware`, `FunctionAware`, `InterfaceAware`, and `MethodAware`.
- PDepend 3.x is now required.
