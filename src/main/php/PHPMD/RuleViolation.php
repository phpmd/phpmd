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

use PHPMD\Node\AbstractTypeNode;
use PHPMD\Node\FunctionNode;
use PHPMD\Node\MethodNode;

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
     * The context code node for this rule violation.
     *
     * @var \PHPMD\AbstractNode
     */
    private $node;

    /**
     * The description/message text that describes the violation.
     *
     * @var string
     */
    private $description;

    /**
     * The raw metric value which caused this rule violation.
     *
     * @var mixed
     */
    private $metric;

    /**
     * Name of the owning/context class or interface of this violation.
     *
     * @var string
     */
    private $className = null;

    /**
     * The name of a method or <b>null</b> when this violation has no method
     * context.
     *
     * @var string
     */
    private $methodName = null;

    /**
     * The name of a function or <b>null</b> when this violation has no function
     * context.
     *
     * @var string
     */
    private $functionName = null;

    /**
     * Constructs a new rule violation instance.
     *
     * @param \PHPMD\Rule $rule
     * @param \PHPMD\AbstractNode $node
     * @param string $violationMessage
     * @param mixed $metric
     */
    public function __construct(Rule $rule, AbstractNode $node, $violationMessage, $metric = null)
    {
        $this->rule = $rule;
        $this->node = $node;
        $this->metric = $metric;
        $this->description = $violationMessage;

        if ($node instanceof AbstractTypeNode) {
            $this->className = $node->getName();
        } elseif ($node instanceof MethodNode) {
            $this->className = $node->getParentName();
            $this->methodName = $node->getName();
        } elseif ($node instanceof FunctionNode) {
            $this->functionName = $node->getName();
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
     * @return string
     */
    public function getFileName()
    {
        return $this->node->getFileName();
    }

    /**
     * Returns the first line of the node that causes this rule violation.
     *
     * @return integer
     */
    public function getBeginLine()
    {
        return $this->node->getBeginLine();
    }

    /**
     * Returns the last line of the node that causes this rule violation.
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->node->getEndLine();
    }

    /**
     * Returns the name of the package that contains this violation.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->node->getNamespaceName();
    }

    /**
     * Returns the name of the parent class or interface or <b>null</b> when there
     * is no parent class.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Returns the name of a method or <b>null</b> when this violation has no
     * method context.
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Returns the name of a function or <b>null</b> when this violation has no
     * function context.
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }
}
