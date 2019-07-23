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
 * Checks if class full qualified class name is used
 */
class MissingImport extends AbstractRule implements MethodAware, FunctionAware
{

    /**
     * Method checks for missing class import and warns about it.
     *
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('AllocationExpression') as $allocationNode) {
            if ($allocationNode) {
                $classNode = $allocationNode->getChild(0);

                $selfReferences = ['self', 'static'];
                if (in_array($classNode->getImage(), $selfReferences, true)) {
                    continue;
                }

                if ($classNode->getEndColumn() - $classNode->getStartColumn() + 1 === strlen($classNode->getImage())) {
                    $this->addViolation($classNode, array($classNode->getBeginLine(), $classNode->getStartColumn()));
                }
            }
        }
    }
}
