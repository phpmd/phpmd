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

use PDepend\Source\AST\ASTEnum;

/**
 * Wrapper around PHP_Depend's enum objects.
 */
class EnumNode extends AbstractTypeNode
{
    /**
     * The type of this enum.
     */
    const CLAZZ = __CLASS__;

    /**
     * Constructs a new class wrapper node.
     *
     * @param \PDepend\Source\AST\ASTEnum $node
     */
    public function __construct(ASTEnum $node)
    {
        parent::__construct($node);
    }
}
