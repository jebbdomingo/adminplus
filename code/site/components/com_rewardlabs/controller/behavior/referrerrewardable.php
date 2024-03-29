<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerBehaviorReferrerrewardable extends KControllerBehaviorAbstract
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => static::PRIORITY_LOW, // low priority so that rewardable runs first
        ));

        parent::_initialize($config);
    }

    /**
     * Hook to after add event
     *
     * @param KControllerContext $context
     * @return void
     */
    protected function _afterAdd(KControllerContext $context)
    {
        // if ($order->payment_method == ComRewardlabsModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        // {
        // }

        $order       = $context->result;
        $order_items = $order->getOrderItems();
        $items       = array();

        foreach ($order_items as $item)
        {
            $items[] = array(
                'id'       => $item->id,
                'drpv'     => $item->drpv,
                'irpv'     => $item->irpv,
                'quantity' => $item->quantity
            );
        }

        if ($order->_account_sponsor_id)
        {
            $data = array(
                'referrer' => $order->_account_sponsor_id,
                'items'    => $items,
            );

            $this->getObject('com://site/rewardlabs.service.reward')->encode($data);
        }
    }

    /**
     * Hook to after sync event
     *
     * @param KControllerContext $context
     * @return void
     */
    protected function _afterSync(KControllerContext $context)
    {
        $order       = $context->result;
        $order_items = $order->getOrderItems();
        $items       = array();

        foreach ($order_items as $item)
        {
            $items[] = array(
                'id'       => $item->id,
                'drpv'     => $item->drpv,
                'irpv'     => $item->irpv,
                'quantity' => $item->quantity
            );
        }

        if ($order->_account_sponsor_id)
        {
            $data = array(
                'referrer' => $order->_account_sponsor_id,
                'items'    => $items,
            );

            $this->getObject('com://site/rewardlabs.service.reward')->encode($data, true);
        }
    }
}
