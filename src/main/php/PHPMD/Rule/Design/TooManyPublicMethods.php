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
use PHPMD\Node\AbstractTypeNode;
use PHPMD\Rule\ClassAware;

/**
 * This rule class will detect all classes with too much public methods.
 */
class TooManyPublicMethods extends AbstractRule implements ClassAware
{
    /**
     * Regular expression that filters all methods that are ignored by this rule.
     *
     * @var string
     */
    protected $ignoreRegexp;

    /**
     * This method checks the number of public methods with in a given class and checks
     * this number against a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $this->ignoreRegexp = $this->getStringProperty('ignorepattern');

        $threshold = $this->getIntProperty('maxmethods');
        $publicMethodsCount = $node->getMetric('npm'); // NPM stands for Number of Public Methods

        if ($publicMethodsCount !== null && $publicMethodsCount <= $threshold) {
            return;
        }

        /** @var AbstractTypeNode $node */
        $nom = $this->countMethods($node);

        if ($nom <= $threshold) {
            return;
        }

        $this->addViolation(
            $node,
            array(
                $node->getType(),
                $node->getName(),
                $nom,
                $threshold,
            )
        );
    }

    /**
     * Counts public methods within the given class/interface node.
     *
     * @param \PHPMD\Node\AbstractTypeNode $node
     * @return integer
     */
    protected function countMethods(AbstractTypeNode $node)
    {
        $count = 0;
        foreach ($node->getMethods() as $method) {
            if ($method->getNode()->isPublic() && !$this->isIgnoredMethodName($method->getName())) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Return true if the method name is ignored by the current ignoreRegexp pattern.
     *
     * ignoreRegexp is given to the rule using ignorepattern option.
     *
     * @param string $methodName
     * @return bool
     */
    private function isIgnoredMethodName($methodName)
    {
        return !empty($this->ignoreRegexp) &&
            preg_match($this->ignoreRegexp, $methodName) === 1;
    }
}
