<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsModelEntityOrderitem extends KModelEntityRow
{
    public function getPropertyAccountId()
    {
        $order = $this->getObject('com://site/rewardlabs.model.orders')
            ->id($this->order_id)
            ->fetch()
        ;

        return $order->account_id;
    }
}
