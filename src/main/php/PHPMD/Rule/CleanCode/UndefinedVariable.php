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

use PDepend\Source\AST\ASTVariable;
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
    private $images = array();

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

        $this->collectPropertyPostfix($node);
        $this->collectClosureParameters($node);
        $this->collectForeachStatements($node);
        $this->collectListExpressions($node);
        $this->collectAssignments($node);
        $this->collectParameters($node);
        $this->collectExceptionCatches($node);
        $this->collectGlobalStatements($node);

        foreach ($node->findChildrenOfType('Variable') as $variable) {
            if (! $this->isNotSuperGlobal($variable)) {
                $this->addVariableDefinition($variable);
            }
            if (! $this->checkVariableDefined($variable, $node)) {
                $this->addViolation($variable, array($variable->getImage()));
            }
        }
    }

    /**
     * Stores the given literal node in an global of found variables.
     *
     * @param \PHPMD\Node\AbstractNode $node
     * @return void
     */
    private function collectGlobalStatements(AbstractNode $node)
    {
        $globalStatements = $node->findChildrenOfType('GlobalStatement');

        foreach ($globalStatements as $globalStatement) {
            foreach ($globalStatement->getChildren() as $variable) {
                $this->addVariableDefinition($variable);
            }
        }
    }

    /**
     * Stores the given literal node in an catch of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    private function collectExceptionCatches(AbstractCallableNode $node)
    {
        $catchStatements = $node->findChildrenOfType('CatchStatement');

        foreach ($catchStatements as $catchStatement) {
            foreach ($catchStatement->getChildren() as $children) {
                if ($children instanceof ASTVariable) {
                    $this->addVariableDefinition($children);
                }
            }
        }
    }

    /**
     * Stores the given literal node in an internal list of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    private function collectListExpressions(AbstractCallableNode $node)
    {
        $lists = $node->findChildrenOfType('ListExpression');

        foreach ($lists as $listExpression) {
            foreach ($listExpression->getChildren() as $variable) {
                $this->addVariableDefinition($variable);
            }
        }
    }


    /**
     * Stores the given literal node in an internal foreach of found variables.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @return void
     */
    private function collectForeachStatements(AbstractCallableNode $node)
    {
        $foreachStatements = $node->findChildrenOfType('ForeachStatement');

        foreach ($foreachStatements as $foreachStatement) {
            foreach ($foreachStatement->getChildren() as $children) {
                if ($children instanceof ASTVariable) {
                    $this->addVariableDefinition($children);
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
    private function collectClosureParameters(AbstractCallableNode $node)
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
    private function checkVariableDefined(ASTNode $variable, AbstractCallableNode $parentNode)
    {
        return isset($this->images[$variable->getImage()]) || $this->isNameAllowedInContext($parentNode, $variable);
    }

    /**
     * Collect parameter names of method/function.
     *
     * @param \PHPMD\Node\AbstractNode $node
     * @return void
     */
    private function collectParameters(AbstractNode $node)
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
    private function collectAssignments(AbstractCallableNode $node)
    {
        foreach ($node->findChildrenOfType('AssignmentExpression') as $assignment) {
            $variable = $assignment->getChild(0);

            $this->addVariableDefinition($variable);
        }
    }

    /**
     * Collect postfix property.
     *
     * @param \PHPMD\Node\AbstractNode $node
     * @return void
     */
    private function collectPropertyPostfix(AbstractNode $node)
    {
        $propertyes = $node->findChildrenOfType('PropertyPostfix');

        foreach ($propertyes as $property) {
            foreach ($property->getChildren() as $children) {
                if ($children instanceof ASTVariable) {
                    $this->addVariableDefinition($children);
                }
            }
        }
    }

    /**
     * Add the variable to images
     *
     * @param mixed $variable
     * @return void
     */
    private function addVariableDefinition($variable)
    {
        if (! isset($this->images[$variable->getImage()])) {
            $this->images[$variable->getImage()] = $variable;
        }
    }

    /**
     * Checks if a short name is acceptable in the current context.
     *
     * @param \PHPMD\Node\AbstractCallableNode $node
     * @param \PHPMD\Node\ASTNode              $variable
     *
     * @return boolean
     */
    private function isNameAllowedInContext(AbstractCallableNode $node, ASTNode $variable)
    {
        return (
            $node instanceof MethodNode &&
            $variable->getImage() === '$this' &&
            ($node->getModifiers() & State::IS_STATIC) === 0
        );
    }
}
