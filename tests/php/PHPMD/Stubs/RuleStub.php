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

namespace PHPMD\Stubs;

use PDepend\Source\AST\ASTNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

/**
 * Simple rule stub implementation
 */
class RuleStub extends AbstractRule implements ClassAware
{
    /** @var ?AbstractNode<ASTNode> */
    public ?AbstractNode $node = null;

    /**
     * Constructs a new rule stub instance.
     *
     * @param string $ruleName The rule name.
     * @param string $ruleSetName The rule-set name.
     */
    public function __construct(string $ruleName = 'RuleStub', string $ruleSetName = 'TestRuleSet')
    {
        $this->setName($ruleName);
        $this->setExternalInfoUrl('https://phpmd.org/rules/index.html');
        $this->setRuleSetName($ruleSetName);
        $this->setSince('42.23');
        $this->setDescription('Simple rule stub');
        $this->setMessage('Test description');
    }

    /**
     * This method should implement the violation analysis algorithm of concrete
     * rule implementations. All extending classes must implement this method.
     */
    public function apply(AbstractNode $node): void
    {
        $this->node = $node;
    }
}
