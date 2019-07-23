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

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Checks that all classes are imported
 *
 * This rule can be used to prevent use of fully qualified class names.
 */
class MissingImport extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * @var array Self reference class names.
     */
    protected $selfReferences = ['self', 'static'];

    /**
     * Checks for missing class imports and warns about it
     *
     * @param AbstractNode $node The node to check upon.
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('AllocationExpression') as $allocationNode) {
            if ($allocationNode) {
                $classNode = $allocationNode->getChild(0);

                if (in_array($classNode->getImage(), $this->selfReferences, true)) {
                    continue;
                }

                if ($classNode->getEndColumn() - $classNode->getStartColumn() + 1 === strlen($classNode->getImage())) {
                    $this->addViolation($classNode, array($classNode->getBeginLine(), $classNode->getStartColumn()));
                }
            }
        }
    }
}
