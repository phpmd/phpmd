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

namespace PHPMD\Utility;

use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTType;
use PHPMD\Node\ASTNode;

/**
 * Utility class to do some more advanced searches from an ASTNode.
 */
final class Seeker
{
    /** @var ASTNode */
    private $node;

    private function __construct(ASTNode $node)
    {
        $this->node = $node;
    }

    /** @return self */
    public static function fromNode(ASTNode $node)
    {
        return new self($node);
    }

    /** @return ASTNode|null */
    public function getParentOfType($type)
    {
        $scope = $this->node->getParent();

        while ($scope && !$scope->isInstanceOf($type)) {
            $scope = $scope->getParent();
        }

        return $scope;
    }

    /** @return ASTNode|null */
    public function getChildIfExist($index)
    {
        try {
            return $this->node->getChild($index);
        } catch (OutOfBoundsException $e) {
            // fallback to null
        }

        return null;
    }
}
