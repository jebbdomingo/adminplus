<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class PlgUserRewardlabs extends JPlugin
{
    const _USER_GROUP_REGISTERED_ = 2;

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Hook into onUserBeforeSave user event
     *
     * @param [type] $[name] [<description>]
     *
     * @return boolean
     */
    public function onUserBeforeSave($oldUser, $isNew, $newUser)
    {
        // Existing user (Update)
        // if(!$isNew)
        // {
        //     // On user activation
        //     if(isset($oldUser['activation']) &&
        //        !empty($oldUser['activation']) &&
        //        isset($oldUser['requireReset']) &&
        //        $oldUser['requireReset'] == 0 &&
        //        isset($newUser['activation']) &&
        //        empty($newUser['activation']))
        //     {
        //         if ($customer = $this->_syncAccount($oldUser))
        //         {
        //             $account  = $this->getObject('com://admin/rewardlabs.model.accounts')->id($oldUser['id'])->fetch();
        //             $account->CustomerRef = $customer->CustomerRef;
        //             $account->activate();
        //             $account->save();

        //             // Attempt to send success email
        //             $emailSubject = "Your Nucleon Plus Account has been activated";
        //             $emailBody    = JText::sprintf(
        //                 'PLG_REWARDLABS_EMAIL_ACTIVATION_BODY',
        //                 $account->_name,
        //                 JUri::root()
        //             );

        //             $config = JFactory::getConfig();
        //             $email  = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $account->_email, $emailSubject, $emailBody);
        //             if ($email !== true)
        //             {
        //                 throw new Exception(JText::sprintf('PLG_REWARDLABS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
        //                 return false;
        //             }
        //         }
        //     }
        // }

        // New user (Registration)
        if ($isNew)
        {
            // Validate TOS
            if (isset($newUser['profile']['tos']) && $newUser['profile']['tos'] <> '1') {
                throw new Exception('You must agree to the Terms and Conditions');
                return false;
            }

            // Validate Sponsor ID field
            return $this->_validateSponsorId($newUser);
        }

        return true;
    }

    /**
     * Send success activation email
     *
     * @param  string $name Name of the recipient
     * @param  string $email Email address of the recipient
     *
     * @throws Exception Email exception
     *
     * @return boolean
     */
    protected function sendSuccessActivationEmail($name, $email)
    {
        // Attempt to send success email
        $subject = "Your Nucleon + Rewards Account has been activated";
        $body    = JText::sprintf(
            'PLG_REWARDLABS_EMAIL_ACTIVATION_BODY',
            $name,
            JUri::root()
        );

        $config   = JFactory::getConfig();
        $mailFrom = $config->get('mailfrom');
        $fromName = $config->get('fromname');

        $result = JFactory::getMailer()->sendMail($mailFrom, $fromName, $email, $subject, $body);

        if ($result !== true) {
            throw new Exception(JText::sprintf('PLG_REWARDLABS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
        }
    }

    /**
     * Hook into onUserAfterSave user event
     *
     * @return void
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        // Registration succeeded
        if ($isNew && $success && in_array(self::_USER_GROUP_REGISTERED_, $user['groups']))
        {
            $session = $this->getObject('lib:user.session');

            // To indicate that this user is just recently activated. Used after login.
            $session->set('activated', true);

            $this->sendSuccessActivationEmail($user['name'], $user['email']);
        }
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param   array  $user     Holds the user data
     * @param   array  $options  Array holding options (remember, autoregister, group)
     *
     * @return  boolean  True on success
     *
     * @since   1.5
     */
    public function onUserAfterLogin($options = array())
    {
        $session = $this->getObject('lib:user.session');
        $account = $this->getObject('com://admin/rewardlabs.model.accounts')->user_id($options['user']->id)->fetch();

        if ($session->get('activated') && !$account->sponsor_id)
        {
            // Reset 'activated' session handler
            $session->remove('activated');

            // Redirect to member form
            $app = JFactory::getApplication();
            $app->enqueueMessage('Please enter Sponsor\'s ID', 'warning');
            $app->redirect(JRoute::_('index.php?option=com_rewardlabs&view=member&layout=form', false));
        }

        return true;
    }

    /**
     * Validate Sponsor ID
     *
     * @param array $user
     *
     * @throws Exception on error
     *
     * @return boolean
     */
    protected function _validateSponsorId($user)
    {
        if (isset($user['sponsor_id']) && $user['sponsor_id'])
        {
            $sponsor = $this->getObject('com://admin/rewardlabs.model.accounts')->id($user['sponsor_id'])->fetch();

            if ($sponsor->isNew()) {
                throw new Exception('Invalid Sponsor ID');
            }
        }

        return true;
    }

    /**
     * Create Nucleon Plus Account
     *
     * @param array $user
     *
     * @throws Exception on error
     *
     * @return KModelEntityInterface
     */
    protected function _createAccount($user)
    {
        // Create account
        $session = $this->getObject('lib:user.session');
        $model   = $this->getObject('com://admin/rewardlabs.model.accounts');
        $account = $model->create(array(
            'status'              => 'new',
            'id'                  => $user['id'],
            'user_id'             => $user['id'],
            'user_name'           => $user['name'],
            'PrintOnCheckName'    => $user['name'],
            'sponsor_id'          => $session->get('sponsor_id'),
        ));

        $session->remove('sponsor_id');
        
        if ($account->save() === false)
        {
            $error = $account->getStatusMessage();
            throw new Exception($error ? $error : 'Create Account Failed');
            return false;
        }

        // Fetch newly created account to get joined tables
        $account = $this->getObject('com://admin/rewardlabs.model.accounts')->id($account->id)->fetch();

        return $account;
    }

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array                    $config     An optional associative array of configuration settings.
     * @return KObjectInterface  Return object on success, throws exception on failure.
     */
    final public function getObject($identifier, array $config = array())
    {
        return KObjectManager::getInstance()->getObject($identifier, $config);
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa') && class_exists('KObjectManager') && (bool) JComponentHelper::getComponent('com_rewardlabs', true)->enabled)
        {
            try
            {
                $return = parent::update($args);
            }
            catch (Exception $e)
            {
                if (JDEBUG) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }

        return $return;
    }
}