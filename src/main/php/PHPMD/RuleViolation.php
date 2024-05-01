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
    /** The description/message text that describes the violation. */
    private string $description;

    /**
     * The arguments for the description/message text or <b>null</b>
     * when the arguments are unknown.
     *
     * @var array<int, string>|null
     */
    private $args = null;

    /**
     * Constructs a new rule violation instance.
     *
     * @param Rule $rule The rule that causes this violation.
     * @param NodeInfo $nodeInfo The AST Node information for this rule violation.
     * @param array<string, mixed>|string $violationMessage
     * @param ?numeric $metric The raw metric value which caused this rule violation.
     */
    public function __construct(
        private Rule $rule,
        private NodeInfo $nodeInfo,
        array|string $violationMessage,
        private mixed $metric = null,
    ) {
        if (is_array($violationMessage)) {
            $search = [];
            $replace = [];
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
     * @return Rule
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
     * @return array<int, string>|null
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
     * @return int
     */
    public function getBeginLine()
    {
        return $this->nodeInfo->beginLine;
    }

    /**
     * Returns the last line of the node that causes this rule violation.
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->nodeInfo->endLine;
    }

    /**
     * Returns the name of the package that contains this violation.
     */
    public function getNamespaceName(): ?string
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
