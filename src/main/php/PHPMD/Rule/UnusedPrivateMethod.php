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

namespace PHPMD\Rule;

use PDepend\Source\AST\ASTMethodPostfix;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Node\ClassNode;
use PHPMD\Node\MethodNode;

/**
 * This rule collects all private methods in a class that aren't used in any
 * method of the analyzed class.
 */
class UnusedPrivateMethod extends AbstractRule implements ClassAware
{
    /**
     * This method checks that all private class methods are at least accessed
     * by one method.
     *
     * @param AbstractNode $class
     * @return void
     */
    public function apply(AbstractNode $class)
    {
        /** @var ClassNode $node */
        foreach ($this->collectUnusedPrivateMethods($class) as $node) {
            $this->addViolation($node, array($node->getImage()));
        }
    }

    /**
     * This method collects all methods in the given class that are declared
     * as private and are not used in the same class' context.
     *
     * @param ClassNode $class
     * @return ASTMethodPostfix[]
     */
    protected function collectUnusedPrivateMethods(ClassNode $class)
    {
        $methods = $this->collectPrivateMethods($class);

        return $this->removeUsedMethods($class, $methods);
    }

    /**
     * Collects all private methods declared in the given class node.
     *
     * @param ClassNode $class
     * @return AbstractNode[]
     */
    protected function collectPrivateMethods(ClassNode $class)
    {
        $methods = array();

        foreach ($class->getMethods() as $method) {
            if ($this->acceptMethod($class, $method)) {
                $methods[strtolower($method->getImage())] = $method;
            }
        }

        return $methods;
    }

    /**
     * Returns <b>true</b> when the given method should be used for this rule's
     * analysis.
     *
     * @param ClassNode $class
     * @param MethodNode $method
     * @return boolean
     */
    protected function acceptMethod(ClassNode $class, MethodNode $method)
    {
        return (
            $method->isPrivate() &&
            false === $method->hasSuppressWarningsAnnotationFor($this) &&
            strcasecmp($method->getImage(), $class->getImage()) !== 0 &&
            strcasecmp($method->getImage(), '__construct') !== 0 &&
            strcasecmp($method->getImage(), '__destruct') !== 0 &&
            strcasecmp($method->getImage(), '__clone') !== 0
        );
    }

    /**
     * This method removes all used methods from the given methods array.
     *
     * @param ClassNode $class
     * @param MethodNode[] $methods
     * @return ASTMethodPostfix[]
     */
    protected function removeUsedMethods(ClassNode $class, array $methods)
    {
        foreach ($class->findChildrenOfType('MethodPostfix') as $postfix) {
            /** @var $postfix ASTNode */
            if ($this->isClassScope($class, $postfix)) {
                unset($methods[strtolower($postfix->getImage())]);
            }
        }

        return $methods;
    }

    /**
     * This method checks that the given method postfix is accessed on an
     * instance or static reference to the given class.
     *
     * @param ClassNode $class
     * @param ASTNode $postfix
     * @return boolean
     */
    protected function isClassScope(ClassNode $class, ASTNode $postfix)
    {
        $owner = $postfix->getParent()->getChild(0);

        return (
            $owner->isInstanceOf('MethodPostfix') ||
            $owner->isInstanceOf('SelfReference') ||
            $owner->isInstanceOf('StaticReference') ||
            strcasecmp($owner->getImage(), '$this') === 0 ||
            strcasecmp($owner->getImage(), $class->getImage()) === 0
        );
    }
}
