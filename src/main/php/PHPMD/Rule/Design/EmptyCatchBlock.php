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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule class detects empty catch blocks
 *
 * @author    Grégoire Paris <postmaster@greg0ire.fr>
 * @author    Kamil Szymanski <kamilszymanski@gmail.com>
 */
class EmptyCatchBlock extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a given function or method contains an empty catch block
     * and emits a rule violation when it exists.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('CatchStatement') as $catchBlock) {
            $scope = $catchBlock->getFirstChildOfType('ScopeStatement');
            if (count($scope->getChildren()) == 0) {
                $this->addViolation($catchBlock, array($node->getName()));
            }
        }
    }
}
