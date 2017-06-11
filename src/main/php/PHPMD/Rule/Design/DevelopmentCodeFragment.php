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
use PHPMD\Node\MethodNode;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * This rule class detects possible development code fragments that were left
 * into the code.
 *
 * @link https://github.com/phpmd/phpmd/issues/265
 * @since 2.3.0
 */
class DevelopmentCodeFragment extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a given function or method contains an eval-expression
     * and emits a rule violation when it exists.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('FunctionPostfix') as $postfix) {
            $image = strtolower($postfix->getImage());
            if (false === in_array($image, $this->getSuspectImages())) {
                continue;
            }

            $image = $node->getImage();
            if ($node instanceof MethodNode) {
                $image = sprintf('%s::%s', $node->getParentName(), $node->getImage());
            }

            $this->addViolation($postfix, array($node->getType(), $image, $postfix->getImage()));
        }
    }

    /**
     * Returns an array with function images that are normally only used during
     * development.
     *
     * @return array
     */
    private function getSuspectImages()
    {
        return array_map(
            'strtolower',
            array_map(
                'trim',
                explode(
                    ',',
                    $this->getStringProperty('unwanted-functions')
                )
            )
        );
    }
}
