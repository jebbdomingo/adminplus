<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsModelEntityOrder extends KModelEntityRow
{
    const STATUS_PENDING      = 'pending';
    const STATUS_PROCESSING   = 'processing';
    const STATUS_PAYMENT      = 'awaiting_payment';
    const STATUS_VERIFIED     = 'verified';
    const STATUS_SHIPPED      = 'shipped';
    const STATUS_DELIVERED    = 'delivered';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_CANCELLED    = 'cancelled';

    const INVOICE_STATUS_SENT = 'sent';
    const INVOICE_STATUS_PAID = 'paid';

    const SHIPPING_METHOD_NA   = 'na';
    const SHIPPING_METHOD_XEND = 'Xend';

    const PAYMENT_METHOD_CASH      = 'cash';
    const PAYMENT_METHOD_DRAGONPAY = 'dragonpay';
    const PAYMENT_METHOD_COD       = 'dragonpay_cod';
    
    /**
     * Prevent deletion of order
     * An order can only be void but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Save action
     *
     * @return boolean
     */
    public function save()
    {
        $account = $this->getObject('com:rewardlabs.model.accounts')->id($this->account_id)->fetch();

        switch ($account->status)
        {
            case 'new':
            case 'pending':
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account is currently inactive'));
                return false;
                break;

            case 'terminated':
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account was terminated'));
                return false;
                break;
            
            default:
                return parent::save();
                break;
        }
    }

    /**
     * Calculate order totals
     *
     * @return KModelEntityInterface
     */
    public function calculate()
    {
        // Calculate total
        $this->sub_total = $this->getAmount();
        $this->total     = $this->sub_total + (float) $this->shipping_cost + (float) $this->payment_charge;

        return $this;
    }

    /**
     * Get order items
     *
     * @return array
     */
    public function getOrderItems()
    {
        return $this->getObject('com://site/rewardlabs.model.orderitems')->order_id($this->id)->fetch();
    }


    /**
     * Get the rewards details
     *
     * @return array
     */
    public function getRewards()
    {
        return $this->getObject('com://site/rewardlabs.model.rewards')->product_id($this->id)->fetch();
    }

    public function getAmount()
    {
        return (float) $this->getObject('com://site/rewardlabs.model.orders')
            ->id($this->id)
            ->getAmount()
        ;
    }

    public function getCouriers()
    {
        return json_decode($this->couriers);
    }

    /**
     * Get customer
     *
     * @return array
     */
    public function getCustomer()
    {
        return $this->getObject('com://site/rewardlabs.model.accounts')->user_id($this->account_id)->fetch();
    }
}
