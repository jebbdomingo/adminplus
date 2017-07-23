<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerBehaviorRewardable extends KControllerBehaviorAbstract
{
    /**
     * Record referral reward points on point of sale 
     *
     * @param KControllerContext $context
     *
     * @return void
     */
    protected function _afterAdd(KControllerContext $context)
    {
        $order = $context->result;

        foreach ($order->getOrderItems() as $item) {
            $order_items[$item->ItemRef] = $item;
        }

        // Avoid n+1 database query issue
        $ids   = array_keys($order_items);
        $items = $this->getObject('com://admin/qbsync.model.items')->ItemRef($ids)->fetch();

        foreach ($items as $item)
        {
            $order_item = $order_items[$item->ItemRef];

            $order_item->drpv     = $item->drpv;
            $order_item->irpv     = $item->irpv;
            $order_item->stockist = $item->stockist;
            $order_item->rebates  = $item->rebates;
            $order_item->charges  = $item->charges;

            $order_item->save();
        }
    }
}