<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerPayoutprocessor extends ComKoowaControllerModel
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.updatepayoutstatus', '_validatePayout');
    }

    protected function _validatePayout(KControllerContextInterface $context)
    {
        $query         = $context->request->query;
        $merchantTxnId = $query->get('merchantTxnId', 'int');
        $refNo         = $query->get('refNo', 'cmd');
        $status        = $query->get('status', 'cmd');
        $message       = $query->get('message', 'string');

        $payout = $this->getObject('com://site/rewardlabs.model.payouts')->id($merchantTxnId)->fetch();

        // Validate payout
        if (!count($payout)) {
            throw new Exception('INVALID_TRANSACTION');
        }

        // Validate digest from dragonpay
        $config     = $this->getObject('com://site/rewardlabs.model.configs')->item('dragonpay')->fetch();
        $dragonpay  = $config->getJsonValue();
        $parameters = array(
            'merchantTxnId' => $merchantTxnId,
            'refNo'         => $refNo,
            'status'        => $status,
            'message'       => $message,
            'password'      => $dragonpay->password
        );
        $digestStr = implode(':', $parameters);
        $digest    = sha1($digestStr);

        if ($query->get('digest', 'cmd') !== $digest)
        {
            if (getenv('APP_ENV') != 'production') {
                var_dump($digest);
            }

            throw new KControllerExceptionRequestInvalid('FAIL_DIGEST_MISMATCH');
        }
    }

    protected function _actionUpdatepayoutstatus(KControllerContextInterface $context)
    {
        $query         = $context->request->query;
        $merchantTxnId = $query->get('merchantTxnId', 'int');
        $status        = $query->get('status', 'cmd');

        // Record dragonpay payout status
        $this->_recordPayoutStatus($query);

        switch ($status) {
            case ComDragonpayModelEntityPayout::STATUS_SUCCESSFUL:
                $payout = $this->getObject('com://site/rewardlabs.model.payouts')
                    ->id($merchantTxnId)
                    ->fetch()
                ;

                $payout->status = ComNucleonplusModelEntityPayout::PAYOUT_STATUS_DISBURSED;
                $payout->save();
                
                $this->_sendMail($payout);

                return $payout;

                break;
        }
    }

    protected function _recordPayoutStatus($query)
    {
        $merchantTxnId = $query->get('merchantTxnId', 'int');
        $controller    = $this->getObject('com://admin/dragonpay.controller.payout');
        $payout        = $controller->getModel()->txnid($merchantTxnId)->count();

        if ($payout)
        {
            $controller
                ->txnid($merchantTxnId)
                ->edit($query->toArray())
            ;
        }
    }

    protected function _sendMail($payout)
    {
        // Send email notification
        $emailSubject = JText::sprintf('COM_REWARDLABS_PAYOUT_EMAIL_FUNDS_TRANSFER_SUCCESSFUL_SUBJECT', $payout->id);
        $emailBody    = JText::sprintf(
            'COM_REWARDLABS_PAYOUT_EMAIL_FUNDS_TRANSFER_SUCCESSFUL_BODY',
            $payout->name,
            'PHP 15.00',
            JUri::root()
        );

        $config = JFactory::getConfig();
        $mail   = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $payout->email, $emailSubject, $emailBody);

        // Check for an error.
        if ($mail !== true) {
            $context->response->addMessage(JText::_('COM_REWARDLABS_PAYOUT_EMAIL_SEND_MAIL_FAILED'), 'error');
        }
    }
}
