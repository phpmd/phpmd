<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Renderer
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */
require_once 'PHP/PMD/AbstractRenderer.php';

/**
 * This class will render a JSON report.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Renderer
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Renderer_JsonRenderer extends PHP_PMD_AbstractRenderer
{

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param PHP_PMD_Report $report The context violation report.
     *
     * @return void
     */
    public function renderReport(PHP_PMD_Report $report)
    {
        $writer = $this->getWriter();

        $results = array('violations' => array(), 'errors' => array());


        foreach ($report->getRuleViolations() as $violation) {

            $results['violations'][] = array(
                'filename' => $violation->getFileName(),
                'begin_line' => $violation->getBeginLine(),
                'end_line' => $violation->getEndLine(),
                'package' => $violation->getPackageName(),
                'function' => $violation->getFunctionName(),
                'class' => $violation->getClassName(),
                'method' => $violation->getMethodName(),
                'rule' => $violation->getRule()->getName(),
                'ruleset' => $violation->getRule()->getRuleSetName(),
                'priority' => $violation->getRule()->getPriority(),
                'description' => $violation->getDescription(),
            );
        }

        foreach ($report->getErrors() as $error) {
            $results['errors'][] = array(
                'filename' => $error->getFile(),
                'message' => $error->getMessage(),
            );
        }
        
        $writer->write(json_encode($results));
    }
}