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

use PDepend\Source\AST\ASTTrait;

/**
 * Wrapper around PHP_Depend's interface objects.
 */
class TraitNode extends AbstractTypeNode
{
    /**
     * Constructs a new interface wrapper instance.
     *
     * @param \PDepend\Source\AST\ASTTrait $node
     */
    public function __construct(ASTTrait $node)
    {
        parent::__construct($node);
    }
}
