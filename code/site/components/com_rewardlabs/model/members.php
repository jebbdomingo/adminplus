<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */
class ComRewardlabsModelMembers extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('email'      , 'email' , null, true)
            ->insert('username'   , 'email' , null, true)
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_account_number'              => '_account.rewardlabs_account_id'))
            ->columns(array('_account_check_name'          => '_account.PrintOnCheckName'))
            ->columns(array('_account_customer_ref'        => '_account.CustomerRef'))
            ->columns(array('_account_status'              => '_account.status'))
            ->columns(array('_account_sponsor_id'          => '_account.sponsor_id'))
            ->columns(array('_account_bank_name'           => '_account.bank_name'))
            ->columns(array('_account_bank_account_number' => '_account.bank_account_number'))
            ->columns(array('_account_bank_account_name'   => '_account.bank_account_name'))
            ->columns(array('_account_bank_account_type'   => '_account.bank_account_type'))
            ->columns(array('_account_bank_account_branch' => '_account.bank_account_branch'))
            ->columns(array('_account_phone'               => '_account.phone'))
            ->columns(array('_account_mobile'              => '_account.mobile'))
            ->columns(array('_account_street'              => '_account.street'))
            ->columns(array('_account_postal_code'         => '_account.postal_code'))
            ->columns(array('city_id'                      => '_account.city'))
            ->columns(array('city'                         => "_city.name"))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_account' => 'rewardlabs_accounts'), 'tbl.id = _account.user_id')
            ->join(array('_city' => 'rewardlabs_cities'), '_account.city = _city.rewardlabs_city_id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        $state = $this->getState();

        if ($state->username) {
            $query->where('username = :username')->bind(array('username' => $state->username));
        }

        if ($state->email) {
            $query->where('email = :email')->bind(array('email' => $state->email));
        }

        if ($state->app) {
            $query->where('app = :app')->bind(array('app' => $state->app));
        }

        if ($state->app_entity) {
            $query->where('app_entity = :app_entity')->bind(array('app_entity' => $state->app_entity));
        }

        parent::_buildQueryWhere($query);
    }
}