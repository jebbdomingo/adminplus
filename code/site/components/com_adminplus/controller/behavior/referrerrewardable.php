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
     * Accounting journal Service
     *
     * @var ComNucleonplusAccountingServiceJournalInterface
     */
    protected $_journal;

    /**
     * Account field name of the entity to process
     *
     * @var mixed
     */
    protected $_account_field;

    /**
     * Items field name of the entity to process
     *
     * @var mixed
     */
    protected $_items_field;

    /**
     * Item's quantity field name of the entity to process
     *
     * @var mixed
     */
    protected $_item_quantity_field;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_unilevel_count      = $config->unilevel_count;
        $this->_controller          = $this->getObject($config->controller);
        $this->_account_field       = $config->account_field;
        $this->_items_field         = $config->items_field;
        $this->_item_quantity_field = $config->item_quantity_field;
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
            'priority'            => static::PRIORITY_LOW, // low priority so that rewardable runs first
            'unilevel_count'      => 20,
            'controller'          => 'com://admin/nucleonplus.controller.rewards',
            'account_field'       => 'account_id',
            'items_field'         => 'items',
            'item_quantity_field' => 'quantity',
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
        $entity  = $context->result;
        $account = $entity->{$this->_account_field};
        $items   = $entity->{$this->_items_field};
        // $items = $entity->getOrderItems();

        $this->encode($account, $items);
    }

    /**
     * Encode referral reward points
     *
     * @param mixed                 $account Account ID
     * @param KModelEntityInterface $items
     * @return void
     */
    public function encode($account, $items)
    {
        // if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        // {
        // }
        
        $this->_journal = $this->getObject('com://admin/nucleonplus.accounting.service.journal');

        foreach ($items as $item)
        {
            $quantity = $item->{$this->_item_quantity_field};
            $points   = $item->drpv * $quantity

            // Record direct referral reward
            $data = array(
                'item'    => $item->id,
                'account' => $account,
                'type'    => 'direct_referral', // Direct Referral
                'points'  => $points,
            );
            $this->_controller->add($data);

            // Post direct referral reward to accounting system
            $this->_journal->recordDirectReferralExpense($item->id, $points);

            // Try to get the 1st indirect referrer
            $referrer = $this->getObject('com:nucleonplus.model.accounts')
                ->id($account)
                ->fetch()
            ;

            // Check if the referrer has sponsor as well (i.e. indirect referrer)
            if (!$referrer->isNew() && $referrer->sponsor_id) {
                $this->_recordIndirectReferrals($referrer->sponsor_id, $item);
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
    private function _recordIndirectReferrals($account, KModelEntityInterface $item)
    {
        $quantity = $item->{$this->_item_quantity_field};
        $points   = ($item->irpv / $this->_unilevel_count) * $quantity;
        $x        = 0;

        $ir_bonus_alloc         = array();
        $ir_surplus_bonus_alloc = array();

        // Try to get referrers up to the _unilevel_count level
        while ($x < $this->_unilevel_count)
        {
            $x++;

            $indirect_referrer = $this->getObject('com:nucleonplus.model.accounts')
                ->id($account)
                ->fetch()
            ;

            $data = array(
                'item'    => $item->id,
                'account' => $indirect_referrer->id,
                'type'    => 'indirect_referral', // Indirect Referral
                'points'  => $points
            );

            $this->_controller->add($data);

            @$ir_bonus_alloc[$item->id] += $points;

            // Terminate execution if the current indirect referrer has no sponsor/referrer i.e. there are no other indirect referrers to pay
            if (is_null($indirect_referrer->sponsor_id))
            {
                if ($x < $this->_unilevel_count)
                {
                    $points = ($this->_unilevel_count - $x) * $points;
                    @$ir_surplus_bonus_alloc[$item->id] += $points;
                    break;
                }

                break;
            }

            $account = $indirect_referrer->sponsor_id;
        }

        if (isset($ir_bonus_alloc[$item->id])) {
            $this->_journal->recordIndirectReferralExpense($item->id, $ir_bonus_alloc[$item->id]);
        }
    }
}
