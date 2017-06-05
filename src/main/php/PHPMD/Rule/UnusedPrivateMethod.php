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
     * @param \PHPMD\AbstractNode $class
     * @return void
     */
    public function apply(AbstractNode $class)
    {
        /** @var $class ClassNode */
        foreach ($this->collectUnusedPrivateMethods($class) as $node) {
            $this->addViolation($node, array($node->getImage()));
        }
    }

    /**
     * This method collects all methods in the given class that are declared
     * as private and are not used in the same class' context.
     *
     * @param \PHPMD\Node\ClassNode $class
     * @return \PHPMD\AbstractNode[]
     */
    private function collectUnusedPrivateMethods(ClassNode $class)
    {
        $methods = $this->collectPrivateMethods($class);
        return $this->removeUsedMethods($class, $methods);
    }

    /**
     * Collects all private methods declared in the given class node.
     *
     * @param \PHPMD\Node\ClassNode $class
     * @return \PHPMD\AbstractNode[]
     */
    private function collectPrivateMethods(ClassNode $class)
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
     * @param \PHPMD\Node\ClassNode $class
     * @param \PHPMD\Node\MethodNode $method
     * @return boolean
     */
    private function acceptMethod(ClassNode $class, MethodNode $method)
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
     * @param \PHPMD\Node\ClassNode $class
     * @param \PHPMD\Node\MethodNode[] $methods
     * @return \PHPMD\AbstractNode[]
     */
    private function removeUsedMethods(ClassNode $class, array $methods)
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
     * @param \PHPMD\Node\ClassNode $class
     * @param \PHPMD\Node\ASTNode $postfix
     * @return boolean
     */
    private function isClassScope(ClassNode $class, ASTNode $postfix)
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
