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
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTNode as PDependNode;
use PHPMD\AbstractNode;

/**
 * Utility class to check and read array possibly representing callable method.
 */
final class CallableArray
{
    /** @var AbstractNode<PDependNode>|null */
    private $array;

    /**
     * @param AbstractNode<PDependNode>|null $array
     */
    private function __construct(?AbstractNode $array = null)
    {
        $this->array = $array;
    }

    /**
     * @param AbstractNode<PDependNode>|mixed $array
     * @return self
     */
    public static function fromArray($array)
    {
        if (
            $array instanceof AbstractNode
            && $array->isInstanceOf(ASTArray::class)
            && count($array->getChildren()) === 2
        ) {
            return new self($array);
        }

        return new self(null);
    }

    /**
     * @param AbstractNode<PDependNode>|null $firstArrayElement
     */
    public static function fromFirstArrayElement(?AbstractNode $firstArrayElement = null): self
    {
        if ($firstArrayElement instanceof AbstractNode && $firstArrayElement->isInstanceOf(ASTArrayElement::class)) {
            return self::fromArray($firstArrayElement->getParent());
        }

        return new self(null);
    }

    /**
     * Return represented method name if the given element is a 2-items array
     * and that the second one is a literal static string.
     *
     * @throws OutOfBoundsException
     */
    public function getMethodNameFromArraySecondElement(): ?string
    {
        if ($this->array === null) {
            return null;
        }

        $secondElement = $this->array->getChild(1)->getChild(0);

        if ($secondElement->isInstanceOf(ASTLiteral::class)) {
            return substr($secondElement->getImage(), 1, -1);
        }

        return null;
    }
}
