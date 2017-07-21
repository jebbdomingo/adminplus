<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerPermissionProduct extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     *
     * @return boolean
     */
    public function canSync()
    {
        return parent::canAdd();
    }
}