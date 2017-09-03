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

class ComAdminplusServiceReward extends KControllerBehaviorAbstract
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
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_unilevel_count = $config->unilevel_count;
        $this->_controller     = $this->getObject($config->controller);
        $this->_journal        = $this->getObject('com://admin/nucleonplus.accounting.service.journal');
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
            'unilevel_count' => 20,
            'controller'     => 'com://admin/nucleonplus.controller.rewards',
        ));

        parent::_initialize($config);
    }

    /**
     * Encode referral reward
     *
     * $data = array(
     *    'referrer' => 0,
     *    'items'    => array(
     *        array('id' => 0, 'drpv' => 0.00, 'irpv' => 0.00, 'quantity' => 0),
     *        array('id' => 0, 'drpv' => 0.00, 'irpv' => 0.00, 'quantity' => 0),
     *    )
     * )
     *
     * @param array $data
     * @return void
     */
    public function encode($data)
    {
        // if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        // {
        // }
        
        foreach ($data['items'] as $item)
        {
            $quantity = $item['quantity'];
            $points   = $item['drpv'] * $quantity;

            // Record direct referral reward
            $this->_controller->add(array(
                'item'    => $item['id'],
                'account' => $data['referrer'],
                'type'    => 'direct_referral', // Direct Referral
                'points'  => $points,
            ));

            // Post direct referral reward to accounting system
            $this->_journal->recordDirectReferralExpense($item['id'], $points);

            // Try to get the 1st indirect referrer
            $referrer  = $this->getObject('com://admin/nucleonplus.model.accounts')
                ->id($data['referrer'])
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
     * @param string $account Sponsor/indirect referrer account number
     * @param array  $item
     *
     * @return void
     */
    private function _recordIndirectReferrals($account, array $item)
    {
        $quantity = $item['quantity'];
        $points   = ($item['irpv'] / $this->_unilevel_count) * $quantity;
        $x        = 0;

        $ir_bonus_alloc         = array();
        $ir_surplus_bonus_alloc = array();

        // Try to get referrers up to the _unilevel_count level
        while ($x < $this->_unilevel_count)
        {
            $x++;

            $indirect_referrer = $this->getObject('com://admin/nucleonplus.model.accounts')
                ->id($account)
                ->fetch()
            ;

            $this->_controller->add(array(
                'item'    => $item['id'],
                'account' => $indirect_referrer->id,
                'type'    => 'indirect_referral', // Indirect Referral
                'points'  => $points
            ));

            @$ir_bonus_alloc[$item['id']] += $points;

            // Terminate execution if the current indirect referrer has no sponsor/referrer i.e. there are no other indirect referrers to pay
            if (is_null($indirect_referrer->sponsor_id))
            {
                if ($x < $this->_unilevel_count)
                {
                    $points = ($this->_unilevel_count - $x) * $points;
                    @$ir_surplus_bonus_alloc[$item['id']] += $points;
                    break;
                }

                break;
            }

            $account = $indirect_referrer->sponsor_id;
        }

        if (isset($ir_bonus_alloc[$item['id']])) {
            $this->_journal->recordIndirectReferralExpense($item['id'], $ir_bonus_alloc[$item['id']]);
        }
    }
}
