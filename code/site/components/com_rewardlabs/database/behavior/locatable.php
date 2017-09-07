<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsDatabaseBehaviorLocatable extends KDatabaseBehaviorAbstract
{
    /**
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $context->query
            ->columns(array('city' => "CONCAT(_city.name, ', ', _province.name)"))
            ->columns(array('city_id' => 'tbl.city'))
            ->join(array('_city' => 'rewardlabs_cities'), 'tbl.city = _city.rewardlabs_city_id')
            ->join(array('_province' => 'rewardlabs_provinces'), '_city.province_id = _province.rewardlabs_province_id')
        ;
    }
}
