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
 */
abstract class AbstractTypeNode extends AbstractNode
{
    /**
     * @var \PDepend\Source\AST\AbstractASTClassOrInterface
     */
    private $node;

    /**
     * Constructs a new generic class or interface node.
     *
     * @param \PDepend\Source\AST\AbstractASTClassOrInterface $node
     */
    public function __construct(AbstractASTClassOrInterface $node)
    {
        parent::__construct($node);

        $this->node = $node;
    }

    /**
     * Returns an <b>array</b> with all methods defined in the context class or
     * interface.
     *
     * @return \PHPMD\Node\MethodNode[]
     */
    public function getMethods()
    {
        $methods = array();
        foreach ($this->node->getMethods() as $method) {
            $methods[] = new MethodNode($method);
        }

        return $methods;
    }

    /**
     * Returns an array with the names of all methods within this class or
     * interface node.
     *
     * @return string[]
     */
    public function getMethodNames()
    {
        $names = array();
        foreach ($this->node->getMethods() as $method) {
            $names[] = $method->getName();
        }

        return $names;
    }

    /**
     * Returns the number of constants declared in this type.
     *
     * @return integer
     */
    public function getConstantCount()
    {
        return count($this->node->getConstants());
    }

    /**
     * Returns the name of the parent namespace.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->node->getNamespace()->getName();
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
        return sprintf('%s\\%s', $this->getNamespaceName(), $this->getName());
    }
}
