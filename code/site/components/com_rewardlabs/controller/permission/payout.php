<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerPermissionPayout extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     *
     * @return boolean
     */
    public function canProcessing()
    {
        $result = false;
        $config = $this->getObject('com://site/rewardlabs.model.configs')
            ->item(ComRewardlabsModelEntityConfig::PAYOUT_RUN_DATE_NAME)
            ->fetch()
        ;

        if ((JFactory::getUser()->id || $this->getObject('user')->isAuthentic()) && $config->value)
        {
            $result = true;
        }

        return $result;
    }
}
