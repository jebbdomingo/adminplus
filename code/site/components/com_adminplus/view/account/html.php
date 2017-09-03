<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComAdminplusViewAccountHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $account = $context->data->account;

        // Rewards summary
        $context->data->rebates            = $account->getRebatesBalance();
        $context->data->direct_referrals   = $account->getDirectReferralBalance();
        $context->data->indirect_referrals = $account->getIndirectReferralBalance();

        $context->data->total = (
            $context->data->rebates +
            $context->data->direct_referrals +
            $context->data->indirect_referrals
        );
    }
}
