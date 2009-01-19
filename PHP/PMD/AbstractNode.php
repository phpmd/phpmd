<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/pmd
 */

/**
 * This is an abstract base class for PHP_PMD code nodes, it is just a wrapper
 * around PHP_Depend's object model.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/pmd
 */
abstract class PHP_PMD_AbstractNode
{
    /**
     *
     * @var PHP_Depend_Code_AbstractItem $node
     */
    private $_node = null;

    /**
     * The collected metrics for this node.
     *
     * @var array(string=>mixed) $_metrics
     */
    private $_metrics = null;

    public function __construct(PHP_Depend_Code_AbstractItem $node)
    {
        $this->_node = $node;
    }

    public function getName()
    {
        return $this->_node->getName();
    }

    public function getBeginLine()
    {
        return $this->_node->getStartLine();
    }

    public function getEndLine()
    {
        return $this->_node->getEndLine();
    }

    /**
     * Returns the name of the declaring source file.
     *
     * @return string
     */
    public function getFileName()
    {
        return (string) $this->_node->getSourceFile();
    }

    public function getTokens()
    {
        return $this->_node->getTokens();
    }

    /**
     * Returns the wrapped PHP_Depend node instance.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    public function getNode()
    {
        return $this->_node;
    }

    /**
     * Returns a textual representation/name for the concrete node type.
     *
     * @return string
     */
    public function getType()
    {
        $type = explode('_', get_class($this));
        return strtolower(array_pop($type));
    }

    /**
     * This method will return the metric value for the given identifier or
     * <b>null</b> when no such metric exists.
     *
     * @param string $name The metric name or abbreviation.
     *
     * @return mixed
     */
    public function getMetric($name)
    {
        if (isset($this->_metrics[$name])) {
            return $this->_metrics[$name];
        }
        return null;
    }

    /**
     * This method will set the metrics for this node.
     *
     * @param array(string=>mixed) $metrics The collected node metrics.
     *
     * @return void
     */
    public function setMetrics(array $metrics)
    {
        if ($this->_metrics === null) {
            $this->_metrics = $metrics;
        }
    }

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     *
     * @return string
     */
    public abstract function getParentName();
    
    public abstract function getPackageName();
}
?>
