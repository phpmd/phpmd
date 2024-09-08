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
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTTrait;
use PHPMD\Rule;
use RuntimeException;

/**
 * Wrapper around a PHP_Depend method node.
 *
 * @extends AbstractCallableNode<ASTMethod>
 *
 * Methods available on $node via PHPMD\AbstractNode::__call
 *
 * @method bool isPrivate() Returns true if this node is marked as private.
 */
class MethodNode extends AbstractCallableNode
{
    /**
     * Returns the name of the parent package.
     */
    public function getNamespaceName(): ?string
    {
        return $this->getNode()->getParent()?->getNamespace()?->getImage();
    }

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     */
    public function getParentName(): ?string
    {
        return $this->getNode()->getParent()?->getImage();
    }

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     */
    public function getFullQualifiedName(): string
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
     */
    public function isAbstract(): bool
    {
        return $this->getNode()->isAbstract();
    }

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     *
     * @throws RuntimeException
     */
    public function hasSuppressWarningsAnnotationFor(Rule $rule): bool
    {
        if (parent::hasSuppressWarningsAnnotationFor($rule)) {
            return true;
        }

        return $this->getParentType()->hasSuppressWarningsAnnotationFor($rule);
    }

    /**
     * Returns the parent class or interface instance.
     *
     * @return AbstractTypeNode<AbstractASTClassOrInterface>
     * @throws RuntimeException
     */
    public function getParentType(): AbstractTypeNode
    {
        $parentNode = $this->getNode()->getParent();

        if ($parentNode instanceof ASTTrait) {
            return new TraitNode($parentNode);
        }

        if ($parentNode instanceof ASTClass) {
            return new ClassNode($parentNode);
        }

        if ($parentNode instanceof ASTEnum) {
            return new EnumNode($parentNode);
        }

        if ($parentNode instanceof ASTInterface) {
            return new InterfaceNode($parentNode);
        }

        $name = $parentNode ? $parentNode::class : 'null';

        throw new RuntimeException('Unexpected method parent type: ' . $name);
    }

    /**
     * Returns <b>true</b> when this method is the initial method declaration.
     * Otherwise this method will return <b>false</b>.
     *
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @since 1.2.1
     */
    public function isDeclaration(): bool
    {
        if ($this->isPrivate()) {
            return true;
        }

        $methodName = strtolower($this->getName());

        $parentNode = $this->getNode()->getParent();
        foreach ($parentNode?->getInterfaces() ?? [] as $parentType) {
            $methods = $parentType->getAllMethods();
            if (isset($methods[$methodName])) {
                return false;
            }
        }

        $parentType = $parentNode?->getParentClass();
        if ($parentType) {
            $methods = $parentType->getAllMethods();
            if (isset($methods[$methodName])) {
                return false;
            }
        }

        return true;
    }
}
