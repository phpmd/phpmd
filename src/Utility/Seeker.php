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

use OutOfBoundsException;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTNode as PDependNode;
use PDepend\Source\AST\ASTVariableDeclarator;
use PHPMD\AbstractNode;

/**
 * Utility class to do some more advanced searches from an ASTNode.
 */
final class Seeker
{
    /** @var AbstractNode<PDependNode> */
    private $node;

    /**
     * @param AbstractNode<PDependNode> $node
     */
    private function __construct(AbstractNode $node)
    {
        $this->node = $node;
    }

    /**
     * @param AbstractNode<PDependNode> $node
     */
    public static function fromNode(AbstractNode $node): self
    {
        return new self($node);
    }

    /**
     * @param class-string<PDependNode> $type
     * @return AbstractNode<PDependNode>|null
     */
    public function getParentOfType($type): ?AbstractNode
    {
        /** @var AbstractNode<PDependNode>|null $scope */
        $scope = $this->node->getParent();

        while ($scope !== null && !$scope->isInstanceOf($type)) {
            /** @var AbstractNode<PDependNode>|null $scope */
            $scope = $scope->getParent();
        }

        return $scope;
    }

    /**
     * @return AbstractNode<PDependNode>|null
     */
    public function getChildIfExist(int $index): ?AbstractNode
    {
        try {
            return $this->node->getChild($index);
        } catch (OutOfBoundsException $e) {
            // fallback to null
        }

        return null;
    }

    /**
     * Finds the nearest callable owning a variable, skipping closures/arrow
     * functions unless a same-named parameter shadows it.
     *
     * @return AbstractNode<PDependNode>|null
     */
    public function getOwningCallable(string $image): ?AbstractNode
    {
        for ($scope = $this->node->getParent(); $scope !== null; $scope = $scope->getParent()) {
            if ($scope->isInstanceOf(AbstractASTCallable::class)) {
                return $scope;
            }

            if ($scope->isInstanceOf(ASTClosure::class) && self::declaresParameter($scope, $image)) {
                return $scope;
            }
        }

        return null;
    }

    /**
     * @param AbstractNode<PDependNode> $closure
     */
    private static function declaresParameter(AbstractNode $closure, string $image): bool
    {
        $parameters = $closure->getFirstChildOfType(ASTFormalParameters::class);
        $declarators = $parameters?->findChildrenOfType(ASTVariableDeclarator::class) ?: [];

        foreach ($declarators as $declarator) {
            if ($declarator->getImage() === $image) {
                return true;
            }
        }

        return false;
    }
}
