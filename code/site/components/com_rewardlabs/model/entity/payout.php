<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsModelEntityPayout extends KModelEntityRow
{
    const PAYOUT_METHOD_PICKUP         = 'pickup';
    const PAYOUT_METHOD_FUNDS_TRANSFER = 'funds_transfer';

    const PAYOUT_STATUS_PENDING         = 'pending';
    const PAYOUT_STATUS_PROCESSING      = 'processing';
    const PAYOUT_STATUS_CHECK_GENERATED = 'checkgenerated';
    const PAYOUT_STATUS_DISBURSED       = 'disbursed';

    public static $payout_status = array(
        self::PAYOUT_STATUS_PENDING         => 'Pending',
        self::PAYOUT_STATUS_PROCESSING      => 'Processing',
        self::PAYOUT_STATUS_CHECK_GENERATED => 'Check Generated',
        self::PAYOUT_STATUS_DISBURSED       => 'Disbursed',
    );
    
    const PAYOUT_TRANSFER_STATUS_SUCCESS    = 'S';
    const PAYOUT_TRANSFER_STATUS_FAILED     = 'F';
    const PAYOUT_TRANSFER_STATUS_PENDING    = 'P';
    const PAYOUT_TRANSFER_STATUS_INPROGRESS = 'G';
    const PAYOUT_TRANSFER_STATUS_VOIDED     = 'V';

    /**
     * Prevent deletion of payout
     * A payout can only be voided but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Get total payouts
     *
     * @return float
     */
    public function getPropertyAmount()
    {
        $direct_referrals   = (float) $this->direct_referrals;
        $indirect_referrals = (float) $this->indirect_referrals;
        $rebates            = (float) $this->rebates;

        return $direct_referrals + $indirect_referrals + $rebates;
    }

    /**
     * On processing failure
     * Revert processing changes when processing failure in other systems. This is used in Dragonpay payout
     *
     * @return void
     */
    public function onProcessingError()
    {
        $this->status         = self::PAYOUT_STATUS_PROCESSING;
        $this->date_processed = null;
        $this->run_date       = null;
        $this->save();
    }
}
