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

use PDepend\Source\AST\AbstractASTClassOrInterface;

/**
 * Abstract base class for classes and interfaces.
 *
 * @template-covariant TNode of AbstractASTClassOrInterface
 *
 * @extends AbstractNode<TNode>
 */
abstract class AbstractTypeNode extends AbstractNode
{
    /**
     * Returns an <b>array</b> with all methods defined in the context class or
     * interface.
     *
     * @return list<MethodNode>
     */
    public function getMethods(): array
    {
        $methods = [];
        foreach ($this->getNode()->getMethods() as $method) {
            $methods[] = new MethodNode($method);
        }

        return $methods;
    }

    /**
     * Returns an array with the names of all methods within this class or
     * interface node.
     *
     * @return list<string>
     */
    public function getMethodNames(): array
    {
        $names = [];
        foreach ($this->getNode()->getMethods() as $method) {
            $names[] = $method->getImage();
        }

        return $names;
    }

    /**
     * Returns the number of constants declared in this type.
     */
    public function getConstantCount(): int
    {
        return count($this->getNode()->getConstants());
    }

    /**
     * Returns the name of the parent namespace.
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
        return sprintf('%s\\%s', $this->getNamespaceName(), $this->getName());
    }
}
