<?php
/**
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Padawan
 * @author     Florian Anderiasch <florian.anderiasch@mayflower.de>
 * @copyright  2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once 'PHP/PMD/AbstractRule.php';
require_once 'PHP/PMD/Rule/IClassAware.php';
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * This rule class will detect potentially exploitable include/requires.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Padawan
 * @author     Florian Anderiasch <florian.anderiasch@mayflower.de>
 * @copyright  2010 Mayflower GmbH
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_Padawan_IncludeRequires
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IClassAware,
               PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    private $_processedVariables = array();
    
    /**
     * Extracts all Require-/IncludeExpressions from the given node
     * and checks for Superglobals as arguments.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $this->resetProcessed();
        foreach($node->findChildrenOfType('RequireExpression') as $sub) {
            $this->checkNode($sub);
        }
        foreach($node->findChildrenOfType('IncludeExpression') as $sub) {
            $this->checkNode($sub);
        }
        $this->resetProcessed();
    }
    
    protected function checkNode(PHP_PMD_AbstractNode $node)
    {
        
        #var_dump($node);
        foreach($node->findChildrenOfType('ArrayIndexExpression') as $arg) {
            foreach($node->findChildrenOfType('Variable') as $arg2) {
                $this->checkNodeImage($arg2);
            }
        }
    }

    /**
     * Checks if the variable name of the given node is matches a known 
     * superglobal.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    protected function checkNodeImage(PHP_PMD_AbstractNode $node)
    {
        $names = array('$_GET', '$_POST', '$_REQUEST', '$_COOKIE', '$_SESSION', '$_ENV');
        
        if ($this->isNotProcessed($node)) {
            $this->addProcessed($node);
            if (in_array($node->getImage(), $names)) {
                $this->addViolation($node, array($node->getImage()));
            }
        }
    }

    /**
     * Resets the already processed nodes.
     *
     * @return void
     */
    protected function resetProcessed()
    {
        $this->_processedVariables = array();
    }

    /**
     * Flags the given node as already processed.
     *
     * @param PHP_PMD_AbstractNode $node The node to add.
     *
     * @return void
     */
    protected function addProcessed(PHP_PMD_AbstractNode $node)
    {
        $index = $node->getParent()->getChild(1)->getImage();
        $key = sprintf("%s_%s_%s", $node->getImage(), $index, $node->getBeginLine());
        
        $this->_processedVariables[$key] = true;
    }

    /**
     * Checks if the given node was already processed.
     *
     * @param PHP_PMD_AbstractNode $node The node to check.
     *
     * @return boolean
     */
    protected function isNotProcessed(PHP_PMD_AbstractNode $node)
    {
        $index = $node->getParent()->getChild(1)->getImage();
        $key = sprintf("%s_%s_%s", $node->getImage(), $index, $node->getBeginLine());
        
        return !isset($this->_processedVariables[$key]);
    }
}
?>