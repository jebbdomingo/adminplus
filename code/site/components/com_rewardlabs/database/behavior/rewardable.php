<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

/**
 * Rewardable Database Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Nucleonplus\Database\Behavior
 */
class ComRewardlabsDatabaseBehaviorRewardable extends KDatabaseBehaviorAbstract
{
    /**
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $context->query
            ->columns(array('_reward_id'           => '_reward.rewardlabs_reward_id'))
            ->columns(array('_reward_customer_id'  => '_reward.customer_id'))
            ->columns(array('_reward_status'       => '_reward.status'))
            ->columns(array('_reward_drpv'         => '_reward.drpv'))
            ->columns(array('_reward_irpv'         => '_reward.irpv'))
            ->columns(array('_reward_type'         => '_reward.type'))
            ->join(array('_reward' => 'rewardlabs_rewards'), 'tbl.rewardlabs_order_id = _reward.product_id')
        ;
    }
}
