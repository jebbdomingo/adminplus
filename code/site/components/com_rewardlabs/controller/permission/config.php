<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerPermissionConfig extends ComKoowaControllerPermissionAbstract
{
    /**
     * Can add
     *
     * @return boolean
     */
    public function canAdd()
    {
        return $this->canManage();
    }

    /**
     * Can edit
     *
     * @return boolean
     */
    public function canEdit()
    {
        return $this->canManage();
    }

    /**
     * Can delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        return $this->canManage();
    }
}
