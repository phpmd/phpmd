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

use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTTrait;
use PHPMD\Rule;

/**
 * Wrapper around a PHP_Depend method node.
 *
 * Methods available on $node via PHPMD\AbstractNode::__call
 *
 * @method bool isPrivate() Returns true if this node is marked as private.
 */
class MethodNode extends AbstractCallableNode
{
    /**
     * Constructs a new method wrapper.
     *
     * @param \PDepend\Source\AST\ASTMethod $node
     */
    public function __construct(ASTMethod $node)
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
        return $this->getNode()->getParent()->getNamespace()->getName();
    }

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->getNode()->getParent()->getName();
    }

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     *
     * @return string
     */
    public function getFullQualifiedName()
    {
        return sprintf(
            '%s\\%s::%s()',
            $this->getNamespaceName(),
            $this->getParentName(),
            $this->getName()
        );
    }

    /**
     * Returns <b>true</b> when the underlying method is declared as abstract or
     * is declared as child of an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->getNode()->isAbstract();
    }

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    public function hasSuppressWarningsAnnotationFor(Rule $rule)
    {
        if (parent::hasSuppressWarningsAnnotationFor($rule)) {
            return true;
        }

        return $this->getParentType()->hasSuppressWarningsAnnotationFor($rule);
    }

    /**
     * Returns the parent class or interface instance.
     *
     * @return \PHPMD\Node\AbstractTypeNode
     */
    public function getParentType()
    {
        $parentNode = $this->getNode()->getParent();

        if ($parentNode instanceof ASTTrait) {
            return new TraitNode($parentNode);
        }

        if ($parentNode instanceof ASTClass) {
            return new ClassNode($parentNode);
        }

        return new InterfaceNode($parentNode);
    }

    /**
     * Returns <b>true</b> when this method is the initial method declaration.
     * Otherwise this method will return <b>false</b>.
     *
     * @return boolean
     * @since 1.2.1
     */
    public function isDeclaration()
    {
        if ($this->isPrivate()) {
            return true;
        }

        $methodName = strtolower($this->getName());

        $parentNode = $this->getNode()->getParent();
        foreach ($parentNode->getInterfaces() as $parentType) {
            $methods = $parentType->getAllMethods();
            if (isset($methods[$methodName])) {
                return false;
            }
        }

        $parentType = $parentNode->getParentClass();
        if (is_object($parentType)) {
            $methods = $parentType->getAllMethods();
            if (isset($methods[$methodName])) {
                return false;
            }
        }

        return true;
    }
}
