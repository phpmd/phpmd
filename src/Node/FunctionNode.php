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
 *
 * @extends AbstractCallableNode<ASTFunction>
 */
class FunctionNode extends AbstractCallableNode
{
    /**
     * Returns the name of the parent package.
     */
    public function getNamespaceName(): ?string
    {
        return $this->getNode()->getNamespace()?->getImage();
    }

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     */
    public function getParentName(): ?string
    {
        return null;
    }

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     */
    public function getFullQualifiedName(): string
    {
        return sprintf('%s\\%s()', $this->getNamespaceName(), $this->getName());
    }
}
