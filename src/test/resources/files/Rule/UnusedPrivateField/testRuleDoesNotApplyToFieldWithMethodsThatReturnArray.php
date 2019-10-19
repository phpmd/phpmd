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

class testRuleDoesNotApplyToFieldWithMethodsThatReturnArray
{
    /**
     * @var mixed
     */
    private $accountGateway;

    /**
     * @var mixed
     */
    private $transactionGateway;

    /**
     * @param $slug
     * @return string
     * @link https://github.com/phpmd/phpmd/issues/324
     */
    public function testAction($slug)
    {
        $accountId = $this->accountGateway
            ->findBySlug($slug)['id'];

        $this->transactionGateway
            ->addTransaction($accountId, 'foo', 1234, '2015-11-07');

        return 'Fine.';
    }
}
