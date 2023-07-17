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

namespace PHPMD\Rule\Naming;

use PDepend\Source\AST\ASTTrait;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\InterfaceNode;
use PHPMD\Rule\MethodAware;

/**
 * This rule class will detect methods that define a php4 style constructor
 * method while has the same name as the enclosing class.
 */
class ConstructorWithNameAsEnclosingClass extends AbstractRule implements MethodAware
{
    /**
     * Is method has the same name as the enclosing class
     * (php4 style constructor).
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        if ($node->getNode()->getParent() instanceof ASTTrait) {
            return;
        }
        if (strcasecmp($node->getName(), $node->getParentName()) !== 0) {
            return;
        }
        if ($node->getParentType() instanceof InterfaceNode) {
            return;
        }
        if ($node->getNamespaceName() !== '+global') {
            return;
        }

        $this->addViolation($node);
    }
}
