<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <http://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerPayout extends ComKoowaControllerModel
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        @ini_set('max_execution_time', 300);
        
        parent::__construct($config);

        $this->addCommandCallback('before.processing', '_validateProcessing');
        $this->addCommandCallback('before.generatecheck', '_validateCheckgenerated');
        $this->addCommandCallback('before.disburse', '_validateDisburse');
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'com://admin/dragonpay.controller.behavior.masspayable' => array(
                    'actions'        => array('after.processing'),
                    'onErrorCallack' => 'onProcessingError', // Entity method to call when dragonpay payout failed
                    'columns'        => array(
                        'merchantTxnId' => 'id',
                        'userName'      => '_account_bank_account_name',
                        'amount'        => 'amount',
                        'procId'        => '_account_bank_name',
                        'procDetail'    => '_account_bank_account',
                        'email'         => 'email',
                        'mobileNo'      => '_account_mobile',
                        'runDate'       => 'run_date',
                    )
                ),
                'payoutable' => array(
                    'actions'    => array('after.processing'),
                    'columns'    => array(
                        'id'                 => 'id',
                        'rebates'            => 'rebates',
                        'direct_referrals'   => 'direct_referrals',
                        'indirect_referrals' => 'indirect_referrals',
                    )
                ),
                'com://admin/dragonpay.controller.behavior.connectable' => array(
                    'actions' => array('before.processing')
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Validate processing action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateProcessing(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                if ($payout->status <> ComRewardlabsModelEntityPayout::PAYOUT_STATUS_PENDING) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Only pending payouts can be processed"));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate check generated action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCheckgenerated(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                if ($payout->status <> ComRewardlabsModelEntityPayout::PAYOUT_STATUS_PROCESSING) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Payout # {$payout->id} is not in processing"));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate disbursed action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateDisburse(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                if ($payout->status <> ComRewardlabsModelEntityPayout::PAYOUT_STATUS_CHECK_GENERATED) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Check for Payout # {$payout->id} is not yet generated"));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Processing
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionProcessing(KControllerContextInterface $context)
    {
        try
        {
            if (!$context->result instanceof KModelEntityInterface) {
                $payouts = $this->getModel()->fetch();
            } else {
                $payouts = $context->result;
            }

            if (count($payouts))
            {
                $config = $this->getObject('com://site/rewardlabs.model.configs')
                    ->item(ComRewardlabsModelEntityConfig::PAYOUT_RUN_DATE_NAME)
                    ->fetch()
                ;

                $data = array(
                    'status'         => ComRewardlabsModelEntityPayout::PAYOUT_STATUS_PROCESSING,
                    'date_processed' => date('Y-m-d H:i:s'),
                );

                foreach($payouts as $payout)
                {
                    if ($payout->payout_method == ComRewardlabsModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER) {
                        $data['run_date'] = date('Y-m-d', strtotime($config->value));
                    }

                    $payout->setProperties($data);
                }

                // Only set the reset content status if the action explicitly succeeded
                if ($payouts->save() === true) {
                    $context->response->setStatus(KHttpResponse::RESET_CONTENT);
                }
            }
            else throw new KControllerExceptionResourceNotFound('Resource could not be found');
        }
        catch (Exception $e)
        {
            $context->response->addMessage($e->getMessage(), 'exception');
        }

        return $payouts;
    }

    /**
     * Generate check
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionGeneratecheck(KControllerContextInterface $context)
    {
        $config = JFactory::getConfig();
        $context->getRequest()->setData(array(
            'status'               => ComRewardlabsModelEntityPayout::PAYOUT_STATUS_CHECK_GENERATED,
            'date_check_generated' => gmdate('Y-m-d H:i:s')
        ));

        $payouts = parent::_actionEdit($context);

        foreach ($payouts as $payout)
        {
            // Send email notification
            $emailSubject = "A check has been generated for your Claim #{$payout->id}";
            $emailBody    = JText::sprintf(
                'COM_REWARDLABS_PAYOUT_EMAIL_CHECK_GENERATED_BODY',
                $payout->name,
                $payout->id,
                JUri::root()
            );

            $mail = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $payout->email, $emailSubject, $emailBody);
            // Check for an error.
            if ($mail !== true) {
                $context->response->addMessage(JText::_('COM_REWARDLABS_PAYOUT_EMAIL_SEND_MAIL_FAILED'), 'error');
            }
        }

        return $payouts;
    }

    /**
     * Disburse
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionDisburse(KControllerContextInterface $context)
    {
        $context->getRequest()->setData(array(
            'status'         => ComRewardlabsModelEntityPayout::PAYOUT_STATUS_DISBURSED,
            'date_disbursed' => gmdate('Y-m-d H:i:s')
        ));

        $payouts = parent::_actionEdit($context);

        foreach ($payouts as $payout)
        {
            $reward = $this->getObject('com:rewardlabs.model.rewards')
                ->payout_id($payout->id)
                ->status('processing')
                ->fetch()
            ;

            if ($reward->id)
            {
                $reward->status = ComRewardlabsModelEntityReward::ACTIVE_CLAIMED;
                $reward->save();
            }
        }

        return $payouts;
    }

    /**
     * Toggle claim request
     * We disable claim request on cut-off time (i.e. every Thursday 1PM) while we are processing checks
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionToggleclaimrequest(KControllerContextInterface $context)
    {
        $claimRequest = $this->getObject('com:rewardlabs.model.configs')->item('claim_request')->fetch();
        
        $claimRequest->value = ($claimRequest->value != ComRewardlabsModelEntityConfig::CLAIM_REQUEST_ENABLED) ? ComRewardlabsModelEntityConfig::CLAIM_REQUEST_ENABLED : ComRewardlabsModelEntityConfig::CLAIM_REQUEST_DISABLED;
        $claimRequest->save();

        if (!$context->result instanceof KModelEntityInterface) {
            $claims = $this->getModel()->fetch();
        } else {
            $claims = $context->result;
        }

        return $claims;
    }
}