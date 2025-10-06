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

use PHPMD\Node\ASTNode;

/**
 * Utility class to check and read array possibly representing callable method.
 */
final class CallableArray
{
    /** @var ASTNode|null */
    private $array;

    // TODO: should be (?ASTNode $array) when dropping PHP < 7.1
    private function __construct(ASTNode $array = null)
    {
        $this->array = $array;
    }

    /** @return self */
    public static function fromArray($array)
    {
        if ($array instanceof ASTNode
            && $array->isInstanceOf('Array')
            && count($array->getChildren()) === 2
        ) {
            return new self($array);
        }

        return new self(null);
    }

    /** @return self */
    public static function fromFirstArrayElement($firstArrayElement)
    {
        if ($firstArrayElement instanceof ASTNode && $firstArrayElement->isInstanceOf('ArrayElement')) {
            return self::fromArray($firstArrayElement->getParent());
        }

        return new self(null);
    }

    /**
     * Return represented method name if the given element is a 2-items array
     * and that the second one is a literal static string.
     *
     * @return string|null
     */
    public function getMethodNameFromArraySecondElement()
    {
        if ($this->array === null) {
            return null;
        }

        $secondElement = $this->array->getChild(1)->getChild(0);

        if ($secondElement->isInstanceOf('Literal')) {
            return substr($secondElement->getImage(), 1, -1);
        }

        return null;
    }
}
