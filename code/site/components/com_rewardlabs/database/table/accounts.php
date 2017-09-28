<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsDatabaseTableAccounts extends KDatabaseTableAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'modifiable',
                'creatable'
            ),
            'filters' => array(
                'user_id' => 'digit'
            )
        ));
        
        parent::_initialize($config);
    }
}
