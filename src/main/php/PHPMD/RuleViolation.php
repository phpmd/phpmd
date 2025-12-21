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

namespace PHPMD;

use PHPMD\Node\NodeInfo;

/**
 * This class is used as container for a single rule violation related to a source
 * node.
 */
class RuleViolation
{
    /**
     * The rule that causes this violation.
     *
     * @var \PHPMD\Rule
     */
    private $rule;

    /**
     * The AST Node information for this rule violation.
     *
     * @var NodeInfo
     */
    private $nodeInfo;

    /**
     * The description/message text that describes the violation.
     *
     * @var string
     */
    private $description;

    /**
     * The arguments for the description/message text or <b>null</b>
     * when the arguments are unknown.
     *
     * @var array|null
     */
    private $args = null;

    /**
     * The raw metric value which caused this rule violation.
     *
     * @var mixed
     */
    private $metric;

    /**
     * Constructs a new rule violation instance.
     *
     * @param \PHPMD\Rule $rule
     * @param NodeInfo $nodeInfo
     * @param string|array $violationMessage
     * @param mixed $metric
     */
    public function __construct(Rule $rule, NodeInfo $nodeInfo, $violationMessage, $metric = null)
    {
        $this->rule = $rule;
        $this->metric = $metric;
        $this->nodeInfo = $nodeInfo;

        if (is_array($violationMessage) === true) {
            $search = array();
            $replace = array();
            foreach ($violationMessage['args'] as $index => $value) {
                $search[] = '{' . $index . '}';
                $replace[] = $value;
            }

            $this->args = $violationMessage['args'];
            $this->description = str_replace($search, $replace, $violationMessage['message']);
        } else {
            $this->description = $violationMessage;
        }
    }

    /**
     * Returns the rule that causes this violation.
     *
     * @return \PHPMD\Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Returns the description/message text that describes the violation.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the arguments for the description/message text or <b>null</b>
     * when the arguments are unknown.
     *
     * @return array|null
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Returns the raw metric value which caused this rule violation.
     *
     * @return mixed|null
     */
    public function getMetric()
    {
        return $this->metric;
    }

    /**
     * Returns the file name where this rule violation was detected.
     *
     * @return string|null
     */
    public function getFileName()
    {
        return $this->nodeInfo->fileName;
    }

    /**
     * Returns the first line of the node that causes this rule violation.
     *
     * @return integer
     */
    public function getBeginLine()
    {
        return $this->nodeInfo->beginLine;
    }

    /**
     * Returns the last line of the node that causes this rule violation.
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->nodeInfo->endLine;
    }

    /**
     * Returns the name of the package that contains this violation.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->nodeInfo->namespaceName;
    }

    /**
     * Returns the name of the parent class or interface or <b>null</b> when there
     * is no parent class.
     *
     * @return string|null
     */
    public function getClassName()
    {
        return $this->nodeInfo->className;
    }

    /**
     * Returns the name of a method or <b>null</b> when this violation has no
     * method context.
     *
     * @return string|null
     */
    public function getMethodName()
    {
        return $this->nodeInfo->methodName;
    }

    /**
     * Returns the name of a function or <b>null</b> when this violation has no
     * function context.
     *
     * @return string|null
     */
    public function getFunctionName()
    {
        return $this->nodeInfo->functionName;
    }
}
