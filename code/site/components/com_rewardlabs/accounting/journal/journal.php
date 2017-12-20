<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

/**
 * @todo Implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
 */
class ComRewardlabsAccountingJournal extends KObject implements ComRewardlabsAccountingJournalInterface
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_service = $this->getObject($config->journal_service);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'journal_service' => 'com://admin/qbsync.service.journalentry',
        ));

        parent::_initialize($config);
    }

    /**
     * Record rebates allocation
     *
     * @param  mixed $entityId
     * @param  float $amount
     * @return mixed
     */
    public function recordRebatesExpense($entityId, $amount)
    {
        $data = $this->getObject('com://site/rewardlabs.accounting.data');

        return $this->_service->create(array(
            'DocNumber'         => $entityId,
            'Date'              => date('Y-m-d'),
            'DebitDescription'  => 'Rebate allocation',
            'DebitAmount'       => $amount,
            'DebitAccount'      => $data->ACCOUNT_REBATES_EXPENSE,
            'CreditDescription' => 'Rebate allocation',
            'CreditAmount'      => $amount,
            'CreditAccount'     => $data->ACCOUNT_REBATES_LIABILITY,
        ));
    }

    /**
     * Record direct referral allocation
     *
     * @param  mixed $entityId
     * @param  float $amount
     * @return mixed
     */
    public function recordDirectReferralExpense($entityId, $amount)
    {
        $data = $this->getObject('com://site/rewardlabs.accounting.data');

        return $this->_service->create(array(
            'DocNumber'         => $entityId,
            'Date'              => date('Y-m-d'),
            'DebitDescription'  => 'Direct referral bonus allocation',
            'DebitAmount'       => $amount,
            'DebitAccount'      => $data->ACCOUNT_DIRECT_REFERRAL_EXPENSE,
            'CreditDescription' => 'Direct referral bonus allocation',
            'CreditAmount'      => $amount,
            'CreditAccount'     => $data->ACCOUNT_DIRECT_REFERRAL_LIABILITY,
        ));
    }

    /**
     * Record indirect referral allocation
     *
     * @param  mixed $entityId
     * @param  float $amount
     * @return mixed
     */
    public function recordIndirectReferralExpense($entityId, $amount)
    {
        $data = $this->getObject('com://site/rewardlabs.accounting.data');

        return $this->_service->create(array(
            'DocNumber'         => $entityId,
            'Date'              => date('Y-m-d'),
            'DebitDescription'  => 'Indirect referral bonus allocation',
            'DebitAmount'       => $amount,
            'DebitAccount'      => $data->ACCOUNT_INDIRECT_REFERRAL_EXPENSE,
            'CreditDescription' => 'Indirect referral bonus allocation',
            'CreditAmount'      => $amount,
            'CreditAccount'     => $data->ACCOUNT_INDIRECT_REFERRAL_LIABILITY,
        ));
    }

    /**
     * Record charges allocation
     *
     * @param  mixed $entityId
     * @param  float $amount
     * @return mixed
     */
    public function recordChargesExpense($entityId, $amount)
    {
        $data = $this->getObject('com://site/rewardlabs.accounting.data');

        return $this->_service->create(array(
            'DocNumber'         => $entityId,
            'Date'              => date('Y-m-d'),
            'DebitDescription'  => 'Charges allocation',
            'DebitAmount'       => $amount,
            'DebitAccount'      => $data->ACCOUNT_CHARGES_EXPENSE,
            'CreditDescription' => 'Charges allocation',
            'CreditAmount'      => $amount,
            'CreditAccount'     => $data->ACCOUNT_CHARGES_LIABILITY,
        ));
    }
}
