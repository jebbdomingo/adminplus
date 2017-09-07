<?php
/**
 * @package     Nucleon Plus Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsTemplateHelperPayout extends KTemplateHelperUi
{
    const PAYOUT_STATUS_PENDING    = 'pending';
    const PAYOUT_STATUS_PROCESSING = 'processing';
    const PAYOUT_STATUS_CHECK      = 'checkgenerated';
    const PAYOUT_STATUS_DISBURSED  = 'disbursed';

    public static $payout_status_messages = array(
        self::PAYOUT_STATUS_PENDING    => 'Pending',
        self::PAYOUT_STATUS_PROCESSING => 'Processing',
        self::PAYOUT_STATUS_CHECK      => 'Check genereated',
        self::PAYOUT_STATUS_DISBURSED  => 'Disbursed',
    );
}
