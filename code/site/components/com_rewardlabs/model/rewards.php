<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2917 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */
class ComRewardlabsModelRewards extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('type', 'string')
            ->insert('account', 'string')
            ->insert('item', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                //'searchable' => array('columns' => array('product_id'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->type) {
            $query->where('tbl.type IN :type')->bind(array('type' => (array) $state->type));
        }

        if ($state->account) {
            $query->where('tbl.account IN :account')->bind(array('account' => (array) $state->account));
        }

        if ($state->item) {
            $query->where('tbl.item IN :item')->bind(array('item' => (array) $state->item));
        }
    }

    /**
     * Get direct referral bonus per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getDirectReferralBonus($account)
    {
        $state = $this->getState();

        $table = $this->getObject('com://site/rewardlabs.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_rewards AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.rewardlabs_reward_id')
            ->where('tbl.type IN :type')->bind(array('type' => array('direct_referral')))
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get indirect referral bonus per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getIndirectReferralBonus($account)
    {
        $state = $this->getState();

        $table = $this->getObject('com://site/rewardlabs.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_rewards AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.rewardlabs_reward_id')
            ->where('tbl.type IN :type')->bind(array('type' => array('indirect_referral')))
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get rebates per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getRebates($account)
    {
        $state = $this->getState();

        $table = $this->getObject('com://site/rewardlabs.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_rewards AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.rewardlabs_reward_id')
            ->where('tbl.type IN :type')->bind(array('type' => array('rebates')))
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }
}