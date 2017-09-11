<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerBehaviorCancellable extends ComDragonpayControllerBehaviorCancellable
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'actions' => array('before.cancelorder'),
            'columns' => array(
                'txnId' => 'id'
            )
        ));

        parent::_initialize($config);
    }
}