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
 */
abstract class AbstractCallableNode extends AbstractNode
{
    /**
     * Constructs a new callable wrapper.
     *
     * @param \PDepend\Source\AST\AbstractASTCallable $node
     */
    public function __construct(AbstractASTCallable $node)
    {
        parent::__construct($node);
    }

    /**
     * Returns the number of parameters in the callable signature.
     *
     * @return integer
     */
    public function getParameterCount()
    {
        return count($this->getNode()->getParameters());
    }
}
