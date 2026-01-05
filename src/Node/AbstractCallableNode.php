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

namespace PHPMD\Node;

use PDepend\Source\AST\AbstractASTCallable;

/**
 * Abstract base class for PHP_Depend function and method wrappers.
 *
 * @template-covariant TNode of AbstractASTCallable
 *
 * @extends AbstractNode<TNode>
 */
abstract class AbstractCallableNode extends AbstractNode
{
    /**
     * Returns the number of parameters in the callable signature.
     */
    public function getParameterCount(): int
    {
        return count($this->getNode()->getParameters());
    }
}
