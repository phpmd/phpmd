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

use PDepend\Source\AST\ASTClass;

/**
 * Wrapper around PHP_Depend's class objects.
 */
class ClassNode extends AbstractTypeNode
{
    /**
     * The type of this class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Constructs a new class wrapper node.
     *
     * @param \PDepend\Source\AST\ASTClass $node
     */
    public function __construct(ASTClass $node)
    {
        parent::__construct($node);
    }
}
