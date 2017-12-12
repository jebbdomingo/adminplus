<?php

/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerPermissionAccount extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     *
     * @return boolean
     */
    public function canAdd()
    {
        return false;
    }

    public function canEdit()
    {
        return $this->canAdd();
    }
}
