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

use PDepend\Source\AST\ASTFunction;

/**
 * Wrapper around a PDepend function node.
 */
class FunctionNode extends AbstractCallableNode
{
    /**
     * Constructs a new function wrapper.
     *
     * @param \PDepend\Source\AST\ASTFunction $node
     */
    public function __construct(ASTFunction $node)
    {
        parent::__construct($node);
    }

    /**
     * Returns the name of the parent package.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->getNode()->getNamespace()->getName();
    }

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     *
     * @return string
     */
    public function getParentName()
    {
        return null;
    }

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     *
     * @return string
     */
    public function getFullQualifiedName()
    {
        return sprintf('%s\\%s()', $this->getNamespaceName(), $this->getName());
    }
}
