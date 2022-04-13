<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;

/**
 * This renderer output a textual log with all found violations and suspect
 * software artifacts.
 */
class TextRenderer extends AbstractRenderer
{
    protected $columnSpacing = 2;

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    public function renderReport(Report $report)
    {
        $writer = $this->getWriter();
        $longestLocationLength = 0;
        $longestRuleNameLength = 0;
        $violations = array();

        foreach ($report->getRuleViolations() as $violation) {
            $location = $violation->getFileName().':'.$violation->getBeginLine();
            $ruleName = $violation->getRule()->getName();
            $locationLength = mb_strlen($location);
            $ruleNameLength = mb_strlen($ruleName);
            $longestLocationLength = max($longestLocationLength, $locationLength);
            $longestRuleNameLength = max($longestRuleNameLength, $ruleNameLength);
            $violations[] = array($violation, $location, $ruleName, $locationLength, $ruleNameLength);
        }

        foreach ($violations as $data) {
            list($violation, $location, $ruleName, $locationLength, $ruleNameLength) = $data;
            $writer->write($location);
            $writer->write(str_repeat(' ', $longestLocationLength + $this->columnSpacing - $locationLength));
            $writer->write($ruleName);
            $writer->write(str_repeat(' ', $longestRuleNameLength + $this->columnSpacing - $ruleNameLength));
            $writer->write($violation->getDescription());
            $writer->write(PHP_EOL);
        }

        foreach ($report->getErrors() as $error) {
            $writer->write($error->getFile());
            $writer->write("\t-\t");
            $writer->write($error->getMessage());
            $writer->write(PHP_EOL);
        }
    }
}
