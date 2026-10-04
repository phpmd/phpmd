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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\AST\ASTTraitReference;
use PDepend\Source\AST\ASTTraitUseStatement;
use PDepend\Source\AST\ASTVariableDeclarator;
use RuntimeException;

/**
 * The static properties that `self::` and `static::` can reach from a class or
 * trait: its own, those of the traits it uses and, unless private, those of its
 * parent classes.
 *
 * @internal
 */
final class StaticPropertyScope
{
    /** @var array<string, ASTVariableDeclarator> */
    private array $declarators = [];

    /** False when a type that could declare one is not in the analyzed sources. */
    private bool $complete = true;

    /** @var array<int, true> */
    private array $visited = [];

    /**
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws RuntimeException
     */
    public function __construct(ASTClass $type)
    {
        // A trait's methods can use the static properties of whichever class uses it.
        if ($type instanceof ASTTrait) {
            $this->complete = false;
        }

        $this->collect($type, true);
    }

    /**
     * @return array<string, ASTVariableDeclarator> Declarators keyed by property name, such as `$name`.
     */
    public function getDeclarators(): array
    {
        return $this->declarators;
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws RuntimeException
     */
    private function collect(ASTClass $type, bool $withPrivate): void
    {
        if (isset($this->visited[spl_object_id($type)])) {
            return;
        }

        $this->visited[spl_object_id($type)] = true;

        foreach ($type->getChildren() as $child) {
            if ($child instanceof ASTFieldDeclaration) {
                $this->collectFields($child, $withPrivate);
            }

            if ($child instanceof ASTTraitUseStatement) {
                $this->collectTraits($child, $withPrivate);
            }
        }

        $this->collectParent($type);
    }

    private function collectFields(ASTFieldDeclaration $declaration, bool $withPrivate): void
    {
        if (!$declaration->isStatic() || (!$withPrivate && $declaration->isPrivate())) {
            return;
        }

        foreach ($declaration->findChildrenOfType(ASTVariableDeclarator::class) as $declarator) {
            $this->declarators[$declarator->getImage()] = $declarator;
        }
    }

    /**
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws RuntimeException
     */
    private function collectTraits(ASTTraitUseStatement $useStatement, bool $withPrivate): void
    {
        foreach ($useStatement->findChildrenOfType(ASTTraitReference::class) as $reference) {
            $trait = $reference->getType();

            if (!$trait->isUserDefined()) {
                $this->complete = false;

                continue;
            }

            $this->collect($trait, $withPrivate);
        }
    }

    /**
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @throws RuntimeException
     */
    private function collectParent(ASTClass $type): void
    {
        if ($type->getParentClassReference() === null) {
            return;
        }

        $parent = $type->getParentClass();

        if (!($parent instanceof ASTClass) || !$parent->isUserDefined()) {
            $this->complete = false;

            return;
        }

        // A parent's private static properties are not reachable from the child.
        $this->collect($parent, false);
    }
}
