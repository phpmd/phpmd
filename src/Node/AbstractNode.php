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
 * Abstract base class for all code nodes.
 */
abstract class AbstractNode extends \PHPMD\AbstractNode
{
    /**
     * Annotations associated with node instance.
     *
     * @var \PHPMD\Node\Annotations
     */
    private $annotations = null;

    /**
     * Checks if this node has a suppressed annotation for the given rule
     * instance.
     *
     * @param \PHPMD\Rule $rule
     * @return boolean
     */
    public function hasSuppressWarningsAnnotationFor(Rule $rule)
    {
        if ($this->annotations === null) {
            $this->annotations = new Annotations($this);
        }

        return $this->annotations->suppresses($rule);
    }
}
