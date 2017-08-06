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
     * Number of levels for direct referrals
     *
     * @param integer
     */
    protected $_unilevel_count;

    /**
     * Referral bonus controller.
     *
     * @param KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Accounting Service
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_unilevel_count = $config->unilevel_count;
        $this->_controller     = $this->getObject($config->controller);
        $this->_accounting     = $this->getObject($config->accounting);
    }

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
            'priority'       => static::PRIORITY_LOW, // low priority so that rewardable runs first
            'unilevel_count' => 20,
            'controller'     => 'com://admin/nucleonplus.controller.rewards',
            'accounting'     => 'com://admin/nucleonplus.accounting.service.transfer',
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
        $this->encode($context->result);
    }

    /**
     * Encode referral reward points
     *
     * @return void
     */
    public function encode($order)
    {
        // if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        // {
        // }
        
        $items = $order->getOrderItems();

        foreach ($items as $item)
        {
            if (is_null($order->_account_sponsor_id))
            {
                // No direct referrer sponsor, flushout direct and indirect referral bonus
                $this->_accounting->allocateSurplusDRBonus($item->id, $item->drpv);
                $this->_accounting->allocateSurplusIRBonus($item->id, $item->irpv);
            }
            else
            {
                // Record direct referral reward
                $data = array(
                    'item'    => $item->id,
                    'account' => $order->_account_sponsor_id,
                    'type'    => 'direct_referral', // Direct Referral
                    'points'  => $item->drpv,
                );
                $this->_controller->add($data);

                // Post direct referral reward to accounting system
                $this->_accounting->allocateDRBonus($item->id, $item->drpv);

                // Try to get the 1st indirect referrer
                $indirect_referrer = $this->getObject('com:nucleonplus.model.accounts')
                    ->account_number($order->_account_sponsor_id)
                    ->fetch()
                ;

                // Check if the first indirect referrer has sponsor as well
                if ($indirect_referrer->isNew())
                {
                    // There's a direct referrer sponsor but no indirect referrer sponsor, flushout indirect referral bonus
                    $this->_accounting->allocateSurplusIRBonus($item->id, $item->irpv);
                }
                else $this->_recordIndirectReferrals($indirect_referrer->account_number, $item);
            }
        }
    }

    /**
     * Record indirect referrals
     *
     * @param string                $account_number Sponsor/indirect referrer account number
     * @param KModelEntityInterface $item
     *
     * @return void
     */
    private function _recordIndirectReferrals($account_number, KModelEntityInterface $item)
    {
        $points = $item->irpv / $this->_unilevel_count;
        $x      = 0;

        $ir_bonus_alloc         = array();
        $ir_surplus_bonus_alloc = array();

        // Try to get referrers up to the _unilevel_count level
        while ($x < $this->_unilevel_count)
        {
            $x++;

            $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')
                ->account_number($account_number)
                ->fetch()
            ;

            $data = array(
                'item'    => $item->id,
                'account' => $indirectReferrer->account_number,
                'type'    => 'indirect_referral', // Indirect Referral
                'points'  => $points
            );

            $this->_controller->add($data);

            @$ir_bonus_alloc[$item->id] += $points;

            // Terminate execution if the current indirect referrer has no sponsor/referrer i.e. there are no other indirect referrers to pay
            if (is_null($indirectReferrer->sponsor_id))
            {
                if ($x < $this->_unilevel_count)
                {
                    $points = ($this->_unilevel_count - $x) * $points;
                    @$ir_surplus_bonus_alloc[$item->id] += $points;
                    break;
                }

                break;
            }

            $account_number = $indirectReferrer->sponsor_id;
        }

        if (isset($ir_bonus_alloc[$item->id])) {
            $this->_accounting->allocateIRBonus($item->id, $ir_bonus_alloc[$item->id]);
        }

        if (isset($ir_surplus_bonus_alloc[$item->id])) {
            $this->_accounting->allocateSurplusIRBonus($item->id, $ir_surplus_bonus_alloc[$item->id]);
        }
    }
}