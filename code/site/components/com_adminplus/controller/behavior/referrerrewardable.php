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

class ComAdminplusControllerBehaviorReferrerrewardable extends KControllerBehaviorAbstract
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
     *
     * @return void
     */
    protected function _afterAdd(KControllerContext $context)
    {
        // if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        // {
        // }

        $entity      = $context->result;
        $order_items = $entity->getOrderItems();
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

        $data = array(
            'referrer' => $entity->_account_sponsor_id,
            'items'    => $items,
        );

        $this->getObject('com://site/adminplus.service.reward')->encode($data);
    }
}
