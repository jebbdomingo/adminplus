<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

/**
 * @todo Implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
 */
class ComRewardlabsAccountingTransfer extends KObject implements ComRewardlabsAccountingTransferInterface
{
    /**
     * Is queue
     *
     * @var boolean
     */
    protected $_queue = false;

    /**
     * Is disabled
     *
     * @var boolean
     */
    protected $_disabled = false;

    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_controller;

    /**
     *
     * @var ComQbsyncServiceTransfer
     */
    protected $_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $this->getObject($config->transfer_controller);
        $this->_service    = $this->getObject($config->transfer_service);

        // Accounts
        $this->_online_payments_account = $config->online_payments_account;
        $this->_savings_account         = $config->savings_account;
        $this->_checking_account        = $config->checking_account;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options
     * 
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $data = $this->getObject('com://site/rewardlabs.accounting.data');

        $config->append(array(
            'transfer_service'        => 'com://admin/qbsync.service.transfer',
            'transfer_controller'     => 'com://admin/qbsync.controller.transfer',
            'online_payments_account' => $data->ACCOUNT_ONLINE_PAYMENTS,
            'savings_account'         => $data->ACCOUNT_BANK_REF,
            'checking_account'        => $data->ACCOUNT_CHECKING_REF,
        ));

        parent::_initialize($config);
    }

    /**
     * Record online payment remittance
     * 
     * @param integer $entity_id
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function depositOnlinePayment($entity_id, $amount)
    {
        $sourceAccount = $this->_online_payments_account;
        $targetAccount = $this->_savings_account;
        $note          = 'Deposit from online payment processing network';

        return $this->_transfer('payout', $entity_id, $sourceAccount, $targetAccount, $amount, $note);
    }

    // /**
    //  * @param integer $entityId
    //  * @param decimal $amount
    //  *
    //  * @return KModelEntityInterface
    //  */
    // public function allocateDeliveryExpense($entityId, $amount)
    // {
    //     $sourceAccount = $this->_savings_account;
    //     $targetAccount = $this->_delivery_expense_account;
    //     $note          = 'Delivery Expense';

    //     return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    // }

    /**
     * @param integer $entity_id
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function rebatesCheck($entity_id, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Rebates Check';

        return $this->_transfer('payout', $entity_id, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entity_id
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function directReferralCheck($entity_id, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Unilevel Direct Referral Check';

        return $this->_transfer('payout', $entity_id, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entity_id
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function indirectReferralCheck($entity_id, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Unilevel Indirect Referral Check';

        return $this->_transfer('payout', $entity_id, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * Transfer funds
     * 
     * @param string  $entity
     * @param integer $entity_id
     * @param integer $fromAccount
     * @param integer $toAccount
     * @param decimal $amount
     * @param string  $note [optional]
     *
     * @throws Exception API error
     *
     * @return KModelEntityInterface|string
     */
    protected function _transfer($entity, $entity_id, $fromAccount, $toAccount, $amount, $note = null)
    {
        if ($this->_disabled) {
            return false;
        }

        if ($this->_queue)
        {
            return $this->_controller->add(array(
                'entity'         => $entity,
                'entity_id'      => $entity_id,
                'FromAccountRef' => $fromAccount,
                'ToAccountRef'   => $toAccount,
                'Amount'         => $amount,
                'TxnDate'        => date('Y-m-d'),
                'PrivateNote'    => "{$entity_id}_{$note}"
            ));
        }
        else
        {
            return $this->_service->create(array(
                'FromAccountRef' => $fromAccount,
                'ToAccountRef'   => $toAccount,
                'Amount'         => $amount,
                'TxnDate'        => date('Y-m-d'),
                'PrivateNote'    => "{$entity_id}_{$note}"
            ));
        }
    }
}