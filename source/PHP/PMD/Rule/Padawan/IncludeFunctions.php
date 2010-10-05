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
 * This rule class will detect empty try{} statements.
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
class PHP_PMD_Rule_Padawan_IncludeFunctions
       extends PHP_PMD_Rule_Padawan_IncludeRequires
    implements PHP_PMD_Rule_IClassAware,
               PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    private $_processedVariables = array();
    
    /**
     * Extracts all try nodes from the given node
     * and checks for empty content.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        $functions = array(
            'readfile', 'virtual', 'file', 'file_get_contents', 'fopen', 
            'mysql_query', 'pg_query', 'sqlite_query')
        ;
        $this->resetProcessed();
        foreach($node->findChildrenOfType('FunctionPostfix') as $sub) {
            if (in_array($sub->getImage(), $functions)) {
                $this->checkNode($sub);
            }
        }
        
        $this->resetProcessed();
    }
    
    
}
?>