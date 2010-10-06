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
require_once 'PHP/PMD/Rule/Padawan/IncludeRequires.php';

/**
 * This rule class will detect occurences of 'SELECT *'.
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
class PHP_PMD_Rule_Padawan_SelectAll
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    
    /**
     * Extracts occurrences of Literals.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        foreach($node->findChildrenOfType('Literal') as $sub) {
             $this->checkNode($sub);
        }
    }

    /**
     * Checks child nodes for occurrences of 'SELECT *'.
     * 
     * @param PHP_PMD_AbstractNode $node The node to check.
     * 
     * @return void
     */
    protected function checkNode(PHP_PMD_AbstractNode $node)
    {
        $pattern = '(select\s+(([`"\'])?\w+([`"\'])?\.)?\*)i';
        if (preg_match($pattern, $node->getImage())) {
            $this->addViolation($node, array($node->getImage()));
        }
    }
}
?>