<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */
class ComRewardlabsModelPayouts extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('account', 'string')
            ->insert('type', 'string')
            ->insert('status', 'string')
            ->insert('search', 'string')
            ->insert('created_on', 'string')
            ->insert('payout_method', 'string')
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_account_bank_name'         => '_account.bank_name'))
            ->columns(array('_account_bank_account'      => '_account.bank_account_number'))
            ->columns(array('_account_bank_account_name' => '_account.bank_account_name'))
            ->columns(array('_account_mobile'            => '_account.mobile'))
            ->columns('_account.rewardlabs_account_id')
            ->columns('_account.status AS account_status')
            ->columns('_account.created_on AS account_created_on')
            ->columns('_user.name')
            ->columns('_user.email')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_account' => 'rewardlabs_accounts'), 'tbl.account = _account.rewardlabs_account_id')
            ->join(array('_user' => 'users'), '_account.user_id = _user.id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->account) {
            $query->where('tbl.account = :account')->bind(['account' => $state->account]);
        }

        if ($state->type) {
            $query->where('tbl.type IN :type')->bind(array('type' => (array) $state->type));
        }

        if ($state->status) {
            $query->where('tbl.status IN :status')->bind(array('status' => (array) $state->status));
        }

        if ($state->payout_method) {
            $query->where('tbl.payout_method = :payout_method')->bind(['payout_method' => $state->payout_method]);
        }

        if ($state->created_on) {
            $query->where('DATE_FORMAT(tbl.created_on,"%Y-%m-%d") = :created_on')->bind(['created_on' => $state->created_on]);
        }

        if ($state->search)
        {
            $conditions = array(
                '_account.account LIKE :keyword',
                '_user.name LIKE :keyword',
            );
            $query->where('(' . implode(' OR ', $conditions) . ')')->bind(['keyword' => "%{$state->search}%"]);
        }
    }

    /**
     * Set default sorting
     *
     * @param KModelContextInterface $context A model context object
     *
     * @return void
     */
    protected function _beforeFetch(KModelContextInterface $context)
    {
        if (is_null($context->state->sort)) {
            $context->query->order('_user.name', 'asc');
        }
    }

    public function getTotal()
    {
        $state = $this->getState();

        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('tbl.rewardlabs_payout_id, SUM(tbl.amount) AS total')
            ->group('tbl.payout_method')
        ;

        $this->_buildQueryWhere($query);

        $entities = $table->select($query);

        return (float) $entities->total;
    }

    public function hasOutstandingRequest()
    {
        $state = $this->getState();

        $status = array(
            ComRewardlabsModelEntityPayout::PAYOUT_STATUS_PENDING,
            ComRewardlabsModelEntityPayout::PAYOUT_STATUS_PROCESSING,
            ComRewardlabsModelEntityPayout::PAYOUT_TRANSFER_STATUS_PENDING,
            ComRewardlabsModelEntityPayout::PAYOUT_TRANSFER_STATUS_INPROGRESS,
        );

        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('tbl.rewardlabs_payout_id, COUNT(tbl.rewardlabs_payout_id) AS count')
            ->where('tbl.status IN :status')->bind(['status' => $status])
            ->where('tbl.account = :account')->bind(['account' => $state->account])
        ;

        $result = $table->select($query);
        $count  = (int) $result->count;

        return ($count > 0) ? true : false;
    }

    /**
     * Get direct referral payout per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getDirectReferralPayout($account)
    {
        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('SUM(tbl.direct_referrals) AS total, tbl.rewardlabs_payout_id')
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get indirect referral payout per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getIndirectReferralPayout($account)
    {
        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('SUM(tbl.indirect_referrals) AS total, tbl.rewardlabs_payout_id')
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get rebates payout per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getRebatesPayout($account)
    {
        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('SUM(tbl.rebates) AS total, tbl.rewardlabs_payout_id')
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    public function getRebates()
    {
        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('SUM(tbl.rebates) AS total, tbl.rewardlabs_payout_id')
        ;

        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);

        $row = $table->select($query);

        return (float) $row->total;
    }

    public function getDirectReferrals()
    {
        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('SUM(tbl.direct_referrals) AS total, tbl.rewardlabs_payout_id')
        ;

        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);

        $row = $table->select($query);

        return (float) $row->total;
    }

    public function getIndirectReferrals()
    {
        $table = $this->getObject('com://site/rewardlabs.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_payouts AS tbl')
            ->columns('SUM(tbl.indirect_referrals) AS total, tbl.rewardlabs_payout_id')
        ;

        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);

        $row = $table->select($query);

        return (float) $row->total;
    }
}