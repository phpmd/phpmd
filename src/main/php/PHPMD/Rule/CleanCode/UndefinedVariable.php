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

namespace PHPMD\Rule\CleanCode;

use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTUnaryExpression;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\AST\State;
use PHPMD\AbstractNode;
use PHPMD\Node\AbstractCallableNode;
use PHPMD\Node\ASTNode;
use PHPMD\Node\MethodNode;
use PHPMD\Rule\AbstractLocalVariable;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule collects all undefined variables within a given function or method
 * that are used by any code in the analyzed source artifact.
 */
class UndefinedVariable extends AbstractLocalVariable implements FunctionAware, MethodAware
{
    /**
     * Found variable images within a single method or function.
     *
     * @var array(string)
     */
    protected $images = array();

    /**
     * This method checks that all local variables within the given function or
     * method are used at least one time.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $this->images = array();

        if ($node instanceof MethodNode) {
            $this->collectProperties($this->getNode($node->getNode()->getParent()));
        }

        $this->collect($node);

        foreach ($node->findChildrenOfType('Class') as $class) {
            /** @var ASTClass $class */

            $this->collectProperties($class);

            foreach ($class->getMethods() as $method) {
                $this->collect(new MethodNode($method));
            }
        }

        foreach ($node->findChildrenOfTypeVariable() as $variable) {
            if ($this->isSuperGlobal($variable) || $this->isPassedByReference($variable)) {
                $this->addVariableDefinition($variable);
            } elseif (!$this->checkVariableDefined($variable, $node)) {
                $this->addViolation($variable, array($this->getVariableImage($variable)));
            }
        }
    }

    /**
     * Collect variables defined inside a PHPMD entry node (such as MethodNode).
     *
     * @param AbstractNode $node
     */
    protected function collect(AbstractNode $node)
    {
        $this->collectPropertyPostfix($node);
        $this->collectClosureParameters($node);
        $this->collectForeachStatements($node);
        $this->collectListExpressions($node);
        $this->collectAssignments($node);
        $this->collectParameters($node);
        $this->collectExceptionCatches($node);
        $this->collectGlobalStatements($node);
    }

    protected function collectProperties($node)
    {
        if (!($node instanceof ASTClass)) {
            return;
        }

        foreach ($node->getProperties() as $property) {
            if ($property->isStatic()) {
                $this->images['::'.$property->getName()] = $property;
            }
        }
    }

    /**
     * Stores the given literal node in an global of found variables.
     *
     * @param \PHPMD\Node\AbstractNode $node
     * @return void
     */
    protected function collectGlobalStatements(AbstractNode $node)
    {
        foreach ($node->findChildrenWithParentType('GlobalStatement') as $variable) {
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Stores the given literal node in an catch of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    protected function collectExceptionCatches(AbstractCallableNode $node)
    {
        foreach ($node->findChildrenWithParentType('CatchStatement') as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }
        }
    }

    /**
     * Stores the given literal node in an internal list of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    protected function collectListExpressions(AbstractCallableNode $node)
    {
        foreach ($node->findChildrenWithParentType('ListExpression') as $variable) {
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Stores the given literal node in an internal foreach of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    protected function collectForeachStatements(AbstractCallableNode $node)
    {
        foreach ($node->findChildrenWithParentType('ForeachStatement') as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }

            if (!($child instanceof ASTUnaryExpression)) {
                continue;
            }

            foreach ($child->getChildren() as $refChildren) {
                if ($refChildren instanceof ASTVariable) {
                    $this->addVariableDefinition($refChildren);
                }
            }
        }
    }

    /**
     * Stores the given literal node in an internal closure of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    protected function collectClosureParameters(AbstractCallableNode $node)
    {
        $closures = $node->findChildrenOfType('Closure');

        foreach ($closures as $closure) {
            $this->collectParameters($closure);
        }
    }

    /**
     * Check if the given variable was defined in the current context before usage.
     *
     * @param \PHPMD\Node\ASTNode $variable
     * @param \PHPMD\Node\AbstractCallableNode $parentNode
     * @return bool
     */
    protected function checkVariableDefined(ASTNode $variable, AbstractCallableNode $parentNode)
    {
        $image = $this->getVariableImage($variable);

        return isset($this->images[$image]) || $this->isNameAllowedInContext($parentNode, $variable);
    }

    /**
     * Collect parameter names of method/function.
     *
     * @param \PHPMD\Node\AbstractNode $node
     * @return void
     */
    protected function collectParameters(AbstractNode $node)
    {
        // Get formal parameter container
        $parameters = $node->getFirstChildOfType('FormalParameters');

        // Now get all declarators in the formal parameters container
        $declarators = $parameters->findChildrenOfType('VariableDeclarator');

        foreach ($declarators as $declarator) {
            $this->addVariableDefinition($declarator);
        }
    }

    /**
     * Collect assignments of variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    protected function collectAssignments(AbstractCallableNode $node)
    {
        foreach ($node->findChildrenOfType('AssignmentExpression') as $assignment) {
            $variable = $assignment->getChild(0);

            if ($variable->getNode() instanceof ASTArray) {
                foreach ($variable->findChildrenOfTypeVariable() as $unpackedVariable) {
                    $this->addVariableDefinition($unpackedVariable);
                }

                continue;
            }

            $this->addVariableDefinition($variable);
        }

        foreach ($node->findChildrenOfType('StaticVariableDeclaration') as $static) {
            $variable = $static->getChild(0);
            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Collect postfix property.
     *
     * @param \PHPMD\Node\AbstractNode $node
     * @return void
     */
    protected function collectPropertyPostfix(AbstractNode $node)
    {
        foreach ($node->findChildrenWithParentType('PropertyPostfix') as $child) {
            if ($child instanceof ASTVariable) {
                $this->addVariableDefinition($child);
            }
        }
    }

    /**
     * Add the variable to images.
     *
     * @param ASTVariable|ASTPropertyPostfix|ASTVariableDeclarator $variable
     * @return void
     */
    protected function addVariableDefinition($variable)
    {
        $image = $this->getVariableImage($variable);

        if (!isset($this->images[$image])) {
            $this->images[$image] = $variable;
        }
    }

    /**
     * Checks if a short name is acceptable in the current context.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @param \PHPMD\Node\ASTNode $variable
     *
     * @return boolean
     */
    protected function isNameAllowedInContext(AbstractCallableNode $node, ASTNode $variable)
    {
        return (
            $node instanceof MethodNode &&
            $variable->getImage() === '$this' &&
            ($node->getModifiers() & State::IS_STATIC) === 0
        );
    }
}
