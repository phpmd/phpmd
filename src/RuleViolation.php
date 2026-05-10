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
    private ?array $args = null;

    /**
     * Constructs a new rule violation instance.
     *
     * @param Rule $rule The rule that causes this violation.
     * @param NodeInfo $nodeInfo The AST Node information for this rule violation.
     * @param array{args: array<int, string>, message: string}|string $violationMessage
     * @param ?numeric $metric The raw metric value which caused this rule violation.
     */
    public function __construct(
        private readonly Rule $rule,
        private readonly NodeInfo $nodeInfo,
        array|string $violationMessage,
        private readonly mixed $metric = null,
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
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * Returns the description/message text that describes the violation.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns the arguments for the description/message text or <b>null</b>
     * when the arguments are unknown.
     *
     * @return array<int, string>|null
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * Returns the raw metric value which caused this rule violation.
     *
     * @return ?numeric
     */
    public function getMetric(): mixed
    {
        return $this->metric;
    }

    /**
     * Returns the file name where this rule violation was detected.
     */
    public function getFileName(): ?string
    {
        return $this->nodeInfo->fileName;
    }

    /**
     * Returns the first line of the node that causes this rule violation.
     */
    public function getBeginLine(): int
    {
        return $this->nodeInfo->beginLine;
    }

    /**
     * Returns the last line of the node that causes this rule violation.
     */
    public function getEndLine(): int
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
     */
    public function getClassName(): ?string
    {
        return $this->nodeInfo->className;
    }

    /**
     * Returns the name of a method or <b>null</b> when this violation has no
     * method context.
     */
    public function getMethodName(): ?string
    {
        return $this->nodeInfo->methodName;
    }

    /**
     * Returns the name of a function or <b>null</b> when this violation has no
     * function context.
     */
    public function getFunctionName(): ?string
    {
        return $this->nodeInfo->functionName;
    }
}
