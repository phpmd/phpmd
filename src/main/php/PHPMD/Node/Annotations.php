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

use PHPMD\Rule;

/**
 * Collection of code annotations.
 */
class Annotations
{
    /**
     * Detected annotations.
     *
     * @var \PHPMD\Node\Annotation[]
     */
    private $annotations = array();

    /**
     * Regexp used to extract code annotations.
     *
     * @var string
     */
    private $regexp = '(@([a-z_][a-z0-9_]+)\(([^\)]+)\))i';

    /**
     * Constructs a new collection instance.
     *
     * @param \PHPMD\AbstractNode $node
     */
    public function __construct(\PHPMD\AbstractNode $node)
    {
        preg_match_all($this->regexp, $node->getDocComment(), $matches);
        foreach (array_keys($matches[0]) as $i) {
            $name = $matches[1][$i];
            $value = trim($matches[2][$i], '" ');

            $this->annotations[] = new Annotation($name, $value);
        }
    }

    /**
     * Checks if one of the annotations suppresses the given rule.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    public function suppresses(Rule $rule)
    {
        foreach ($this->annotations as $annotation) {
            if ($annotation->suppresses($rule)) {
                return true;
            }
        }

        return false;
    }
}
