#!/usr/bin/env php
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once 'PHP/Depend.php';
require_once 'PHP/PMD/RuleSetFactory.php';
require_once 'PHP/PMD/Adapter/Metrics.php';

$ruleSetFactory = new PHP_PMD_RuleSetFactory();
$ruleSets = $ruleSetFactory->createRuleSets(@$argv[2]);

$adapter = new PHP_PMD_Adapter_Metrics();
foreach ($ruleSets as $ruleSet) {
    echo $ruleSet->getName(), PHP_EOL;
    $adapter->addRuleSet($ruleSet);
}

$pdepend = new PHP_Depend();
$pdepend->addDirectory(realpath($argv[1]));
$pdepend->addLogger($adapter);
$pdepend->analyze();
?>
