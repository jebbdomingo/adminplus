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
        $model   = $this->getModel();
        $account = $model->fetch();
        $model->account_number($account->account_number);

        // Rewards summary
        $context->data->total_referral_bonus   = $model->getTotalAvailableReferralBonus()->total;
        $context->data->total_rebates          = $model->getTotalAvailableRebates()->total;

        $context->data->total_bonus = (
            $context->data->total_referral_bonus +
            $context->data->total_rebates
        );

        // Rewards payout details
        $context->data->dr_bonuses = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
            ->account($account->account_number)
            ->type('direct_referral')
            ->payout_id(0)
            ->fetch()
        ;

        $context->data->ir_bonuses = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
            ->account($account->account_number)
            ->type('indirect_referral')
            ->payout_id(0)
            ->fetch()
        ;

        $context->data->rebates = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
            ->account($account->account_number)
            ->type('rebates')
            ->payout_id(0)
            ->fetch()
        ;

        parent::_fetchData($context);
    }
}