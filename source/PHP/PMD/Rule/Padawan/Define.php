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
 * This rule class will detect define() with variables instead of constants/literals.
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
class PHP_PMD_Rule_Padawan_Define
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    /**
     * Extracts all "define" function nodes from the given node.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        foreach($node->findChildrenOfType('FunctionPostfix') as $match) {
            if ('define' == $match->getImage()) {
                $this->checkArgs($match);
            }
        }
    }

    /**
     * Checks for variables as arguments of the given node.
     * 
     * @param PHP_PMD_AbstractNode $node The current define node.
     * 
     * @return void
     */
    protected function checkArgs(PHP_PMD_AbstractNode $node)
    {
        $a = $node->getFirstChildOfType('Arguments');
        $b = $a->findChildrenOfType('Variable');
        if (count($b) > 0) {
            $this->addViolation($node, array($node->getImage()));
        }
    }
}
?>