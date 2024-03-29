<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsModelCities extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('province_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'citysearchable' => array('columns' => array('_name'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _beforeFetch(KModelContextInterface $context)
    {
        $model    = $context->getSubject();
        $state    = $context->state;
        $query    = $context->query;
        $category = null;

        $query
            ->columns(array('_name'          => "CONCAT(tbl.name, ', ', _province.name)"))
            ->columns(array('_province_name' => '_province.name'))
            ->columns(array('_province_id'   => '_province.rewardlabs_province_id'))
            ->join(array('_province' => 'rewardlabs_provinces'), 'tbl.province_id = _province.rewardlabs_province_id', 'INNER')
        ;
    }

    protected function _beforeCount(KModelContextInterface $context)
    {
        $this->_beforeFetch($context);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->province_id) {
            $query->where('tbl.province_id = :province_id')->bind(['province_id' => $state->province_id]);
        }
    }
}