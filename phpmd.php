#!/usr/bin/env php
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once 'PHP/Depend.php';
require_once 'PHP/PMD.php';
require_once 'PHP/PMD/AbstractWriter.php';
require_once 'PHP/PMD/Report.php';
require_once 'PHP/PMD/RuleSetFactory.php';
require_once 'PHP/PMD/Adapter/Metrics.php';
require_once 'PHP/PMD/Renderer/XMLRenderer.php';

class MyWriter extends PHP_PMD_AbstractWriter {
    public function write($data) {
        echo $data;
    }
}

$ruleSetFactory = new PHP_PMD_RuleSetFactory();
$ruleSets = $ruleSetFactory->createRuleSets(@$argv[2]);

$report = new PHP_PMD_Report();

$adapter = new PHP_PMD_Adapter_Metrics();
$adapter->setReport($report);

foreach ($ruleSets as $ruleSet) {
    echo $ruleSet->getName(), PHP_EOL;
    $adapter->addRuleSet($ruleSet);
}

$pdepend = new PHP_Depend();
$pdepend->addDirectory(realpath($argv[1]));
$pdepend->addLogger($adapter);
$pdepend->analyze();

$renderer = new PHP_PMD_Renderer_XMLRenderer();
$renderer->setWriter(new MyWriter());
$renderer->start();
$renderer->renderReport($report);
$renderer->end();
?>
