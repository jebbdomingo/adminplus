<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

/**
 * Journal Interface.
 *
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComRewardlabsAccountingJournalInterface
{
    /**
     * Record rebates expense
     * 
     * @param  integer $orderId
     * @param  decimal $amount
     * @return mixed
     */
    public function recordRebatesExpense($orderId, $amount);

    /**
     * Record direct referral reward expense
     * 
     * @param  integer $orderId
     * @param  decimal $amount
     * @return mixed
     */
    public function recordDirectReferralExpense($orderId, $amount);

    /**
     * Record indirect referral reward expense
     * 
     * @param  integer $orderId
     * @param  decimal $amount
     * @return mixed
     */
    public function recordIndirectReferralExpense($orderId, $amount);

    /**
     * Reord charges expense
     * 
     * @param  integer $orderId
     * @param  decimal $amount
     * @return mixed
     */
    public function recordChargesExpense($orderId, $amount);
}
