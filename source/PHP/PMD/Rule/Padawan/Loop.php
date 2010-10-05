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
require_once 'PHP/PMD/Rule/IMethodAware.php';
require_once 'PHP/PMD/Rule/IFunctionAware.php';

/**
 * This rule class will detect function calls in loop expressions.
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
class PHP_PMD_Rule_Padawan_Loop
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    /**
     * Extracts all nodes from the given node
     * and checks for function calls in loop expressions.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $checks = array(
            // ForStatement has child nodes ForInit, Expression, ForUpdate, [ScopeStatement]
            'ForStatement' => array(0, 2), 
            // WhileStatement has child nodes Expression, [ScopeStatement]
            'WhileStatement' => array(0, 0), 
            // DoWhileStatement has child nodes [ScopeStatement], Expression
            'DoWhileStatement' => array(1,1),
        );
        foreach($checks as $key => $chk) {
            foreach($node->findChildrenOfType($key) as $match) {
                $this->checkNode($match, $chk);
            }
        }
    }
    
    /**
     * Checks child nodes for occurrences of function calls.
     * 
     * @param PHP_PMD_AbstractNode $node The node to check.
     * @param array $check An array of start/end values.
     * 
     * @return void
     */
    protected function checkNode(PHP_PMD_AbstractNode $node, $check)
    {
        for($i=$check[0]; $i<=$check[1]; $i++) {
            $match = $node->getChild($i);
            if (count($match->findChildrenOfType('FunctionPostfix')) > 0) {
                $this->addViolation($match, array($match->getImage()));
            }
        if (count($match->findChildrenOfType('MethodPostfix')) > 0) {
                $this->addViolation($match, array($match->getImage()));
            }
        }
    }
}
?>