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

namespace PHPMD\Node;

use PHPMD\Rule;

/**
 * Wrapper around a PHP_Depend ast node.
 */
class ASTNode extends \PHPMD\AbstractNode
{
    /**
     * The source file of this node.
     *
     * @var string
     */
    private $fileName = null;

    /**
     * Constructs a new ast node instance.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @param string $fileName
     */
    public function __construct(\PDepend\Source\AST\ASTNode $node, $fileName)
    {
        parent::__construct($node);

        $this->fileName = $fileName;
    }

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function hasSuppressWarningsAnnotationFor(Rule $rule)
    {
        return false;
    }

    /**
     * Returns the source name for this node, maybe a class or interface name,
     * or a package, method, function name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getImage();
    }

    /**
     * Returns the image of the underlying node.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getNode()->getImage();
    }

    /**
     * Returns the name of the declaring source file.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Returns the name of the parent type or <b>null</b> when this node has no
     * parent type.
     *
     * @return string
     */
    public function getParentName()
    {
        return null;
    }

    /**
     * Returns the name of the parent namespace.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return null;
    }

    /**
     * Returns the full qualified name of a class, an interface, a method or
     * a function.
     *
     * @return string
     */
    public function getFullQualifiedName()
    {
        return null;
    }
}
