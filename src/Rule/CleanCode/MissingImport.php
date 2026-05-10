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

namespace PHPMD\Rule\CleanCode;

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTNode as PDependNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Checks that all classes are imported
 *
 * This rule can be used to prevent use of fully qualified class names.
 */
final class MissingImport extends AbstractRule implements FunctionAware, MethodAware
{
    /** @var list<string> Self reference class names. */
    private array $selfReferences = ['self', 'static'];

    /**
     * Checks for missing class imports and warns about it
     */
    public function apply(AbstractNode $node): void
    {
        $ignoreGlobal = $this->getBooleanProperty('ignore-global');

        foreach ($node->findChildrenOfType(ASTAllocationExpression::class) as $allocationNode) {
            $classNode = $allocationNode->getChild(0);
            if (!$classNode instanceof ASTNode) {
                continue;
            }

            if ($this->isSelfReference($classNode)) {
                continue;
            }

            if ($ignoreGlobal && $this->isGlobalNamespace($classNode)) {
                continue;
            }

            $classNameLength = $classNode->getEndColumn() - $classNode->getStartColumn() + 1;
            $className = $classNode->getImage();
            $fqcnLength = strlen($className);

            if ($classNameLength === $fqcnLength && !str_starts_with($className, '$')) {
                $this->addViolation(
                    $classNode,
                    [(string) $classNode->getBeginLine(), (string) $classNode->getStartColumn()]
                );
            }
        }
    }

    /**
     * Check whether a given class node is a self reference
     *
     * @param AbstractNode<PDependNode> $classNode A class node to check.
     * @return bool Whether the given class node is a self reference.
     */
    private function isSelfReference(AbstractNode $classNode): bool
    {
        return in_array($classNode->getImage(), $this->selfReferences, true);
    }

    /**
     * Check whether a given class node is in the global namespace
     *
     * @param AbstractNode<PDependNode> $classNode A class node to check.
     * @return bool Whether the given class node is in the global namespace.
     */
    private function isGlobalNamespace(AbstractNode $classNode): bool
    {
        return $classNode->getImage() !== '' && !strpos($classNode->getImage(), '\\', 1);
    }
}
