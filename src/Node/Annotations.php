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

use PDepend\Source\AST\ASTAnonymousClass;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTNode;
use PHPMD\AbstractNode;
use PHPMD\Rule;

/**
 * Collection of code annotations.
 *
 * @internal
 */
final class Annotations
{
    /**
     * Detected annotations.
     *
     * @var list<Annotation>
     */
    private array $annotations = [];

    /** Regexp used to extract code annotations. */
    private string $regexp = '(@([a-z_][a-z0-9_]+)\(([^\)]+)\))i';

    /**
     * Constructs a new collection instance.
     *
     * @param AbstractNode<ASTNode> $node
     */
    public function __construct(AbstractNode $node)
    {
        $comment = $node->getComment();
        if (!$comment && $node->isInstanceOf(ASTAnonymousClass::class)) {
            $comment = $this->getAnonymousClassComment($node->getNode());
        }
        if (!$comment) {
            return;
        }

        preg_match_all($this->regexp, $comment, $matches);
        foreach (array_keys($matches[0]) as $i) {
            $name = $matches[1][$i];
            $value = trim($matches[2][$i], '" ');

            $this->annotations[] = new Annotation($name, $value);
        }
    }

    /**
     * Checks if one of the annotations suppresses the given rule.
     */
    public function suppresses(Rule $rule): bool
    {
        foreach ($this->annotations as $annotation) {
            if ($annotation->suppresses($rule)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Finds a doc comment placed immediately before the statement declaring an
     * anonymous class, since PDepend does not attach it to the class node.
     */
    private function getAnonymousClassComment(ASTAnonymousClass $node): ?string
    {
        $statement = $node;
        while (
            ($parent = $statement->getParent()) !== null &&
            $parent->getStartLine() === $node->getStartLine()
        ) {
            $statement = $parent;
        }

        $previous = null;
        foreach ($statement->getParent()?->getChildren() ?? [] as $child) {
            if ($child === $statement) {
                break;
            }
            $previous = $child;
        }

        if ($previous instanceof ASTComment && str_starts_with($previous->getImage(), '/**')) {
            return $previous->getImage();
        }

        return null;
    }
}
