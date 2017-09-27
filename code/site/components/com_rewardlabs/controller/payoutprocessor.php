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
        $data = $context->request->data;

        $merchantTxnId = $data->get('merchantTxnId', 'int');
        $refNo         = $data->get('refNo', 'cmd');
        $status        = $data->get('status', 'cmd');
        $message       = $data->get('message', 'string');

        $payout = $this->getObject('com://site/rewardlabs.model.payouts')->id($merchantTxnId)->fetch();

        // Validate payout
        if (!count($payout)) {
            throw new Exception('INVALID_TRANSACTION');
        }

        // Validate digest from dragonpay
        $config     = $this->getObject('com://site/rewardlabs.model.configs')->item('dragonpay')->fetch();
        $dragonpay  = $config->getJsonValue();
        $password   = getenv('APP_ENV') == 'production' ? $dragonpay->password : $dragonpay->password_test;
        $parameters = array(
            'merchantTxnId' => $merchantTxnId,
            'refNo'         => $refNo,
            'status'        => $status,
            'message'       => $message,
            'password'      => $password
        );
        $digestStr = implode(':', $parameters);
        $digest    = sha1($digestStr);

        if ($data->get('digest', 'cmd') !== $digest)
        {
            if (getenv('APP_ENV') != 'production') {
                var_dump($digest);
            }

            throw new KControllerExceptionRequestInvalid('FAIL_DIGEST_MISMATCH');
        }
    }

    protected function _actionUpdatepayoutstatus(KControllerContextInterface $context)
    {
        $data          = $context->request->data;
        $merchantTxnId = $data->get('merchantTxnId', 'int');
        $status        = $data->get('status', 'cmd');

        // Record dragonpay payout status
        $this->_recordPayoutStatus($data);

        switch ($status) {
            case ComDragonpayModelEntityPayout::STATUS_SUCCESSFUL:
                $payout = $this->getObject('com://site/rewardlabs.model.payouts')
                    ->id($merchantTxnId)
                    ->fetch()
                ;

                $payout->status = ComRewardlabsModelEntityPayout::PAYOUT_STATUS_DISBURSED;
                $payout->save();
                
                $this->_sendMail($payout);

                return $payout;

                break;
        }
    }

    protected function _recordPayoutStatus($data)
    {
        $merchantTxnId = $data->get('merchantTxnId', 'int');
        $model         = $this->getObject('com://admin/dragonpay.model.payout');
        $payout        = $model->txnid($merchantTxnId)->fetch();

        if (count($payout))
        {
            $data = array(
                'status' => $data->get('status', 'cmd'),
                'message' => $data->get('message', 'string'),
                'refno' => $data->get('refNo', 'string'),
            );

            $payout->setProperties($data);
            $payout->save();
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
            $this->getContext()->response->addMessage(JText::_('COM_REWARDLABS_PAYOUT_EMAIL_SEND_MAIL_FAILED'), 'error');
        }
    }
}
