#!/usr/bin/env php
<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Input/ExcludePathFilter.php';
require_once 'PHP/Depend/Input/ExtensionFilter.php';

require_once 'PHP/PMD.php';
require_once 'PHP/PMD/Report.php';
require_once 'PHP/PMD/RuleSetFactory.php';
require_once 'PHP/PMD/Adapter/Metrics.php';
require_once 'PHP/PMD/Renderer/XMLRenderer.php';
require_once 'PHP/PMD/Writer/Stream.php';

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
    $adapter->addRuleSet($ruleSet);
}

$pdepend = new PHP_Depend();
$pdepend->addDirectory(realpath($argv[1]));
$pdepend->addFileFilter(new PHP_Depend_Input_ExcludePathFilter(array('.git', '.svn', 'CVS')));
$pdepend->addFileFilter(new PHP_Depend_Input_ExtensionFilter(array('php', 'php3', 'php4', 'php5', 'inc')));
$pdepend->addLogger($adapter);
$pdepend->analyze();

$renderer = new PHP_PMD_Renderer_XMLRenderer();
$renderer->setWriter(new PHP_PMD_Writer_Stream(STDOUT));
$renderer->start();
$renderer->renderReport($report);
$renderer->end();
?>
