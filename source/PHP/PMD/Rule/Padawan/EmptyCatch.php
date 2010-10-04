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
 * This rule class will detect empty catch() statements.
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
class PHP_PMD_Rule_Padawan_EmptyCatch
       extends PHP_PMD_AbstractRule
    implements PHP_PMD_Rule_IMethodAware,
               PHP_PMD_Rule_IFunctionAware
{
    /**
     * Extracts all catch nodes from the given node
     * and checks for empty content.
     *
     * @param PHP_PMD_AbstractNode $node The context source code node.
     *
     * @return void
     */
    public function apply(PHP_PMD_AbstractNode $node)
    {
        foreach($node->findChildrenOfType('CatchStatement') as $catch) {
            foreach($catch->getChildren() as $child) {
                if (null === $child->getImage()) {
                    $this->addViolation($catch, array($node->getImage()));
                }
            }
        }
    }
}
?>