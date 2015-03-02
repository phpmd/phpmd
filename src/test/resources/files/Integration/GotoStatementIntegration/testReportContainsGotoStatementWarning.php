<?php
function testReportContainsGotoStatementWarning()
{
    LABEL:
        echo 'You goto ' . __LINE__;

    if (time() % 42 === 23) {
        goto LABEL;
    }
}