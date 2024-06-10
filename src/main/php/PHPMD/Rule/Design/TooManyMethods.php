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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ClassNode;
use PHPMD\Rule\ClassAware;

/**
 * This rule class will detect all classes with too many methods.
 */
final class TooManyMethods extends AbstractRule implements ClassAware
{
    /**
     * Regular expression that filters all methods that are ignored by this rule.
     *
     * @var string
     */
    private $ignoreRegexp;

    /**
     * This method checks the number of methods with in a given class and checks
     * this number against a configured threshold.
     */
    public function apply(AbstractNode $node): void
    {
        if (!$node instanceof ClassNode) {
            return;
        }

        $this->ignoreRegexp = $this->getStringProperty('ignorepattern');

        $threshold = $this->getIntProperty('maxmethods');
        if ($node->getMetric('nom') <= $threshold) {
            return;
        }
        $nom = $this->countMethods($node);
        if ($nom <= $threshold) {
            return;
        }
        $this->addViolation(
            $node,
            [
                $node->getType(),
                $node->getName(),
                (string) $nom,
                (string) $threshold,
            ]
        );
    }

    /**
     * Counts all methods within the given class/interface node.
     */
    private function countMethods(ClassNode $node): int
    {
        $count = 0;
        foreach ($node->getMethodNames() as $name) {
            if (preg_match($this->ignoreRegexp, $name) === 0) {
                ++$count;
            }
        }

        return $count;
    }
}
