<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsModelEntityMember extends KModelEntityRow
{
    const _USER_GROUP_REGISTERED_ = 2;

    const STATUS_ACTIVE  = 'active';
    const STATUS_DELETED = 'deleted';

    /**
     * Saves the entity to the data store
     *
     * @return boolean
     */
    public function save()
    {
        jimport( 'joomla.user.helper');

        $member = new KObjectConfig($this->getProperties());

        if ($this->isNew())
        {
            $user = new JUser;

            // Merge the following fields as these are not automatically updated by Nooku
            $member->merge([
                'password'     => JUserHelper::genRandomPassword(),
                'requireReset' => 1,
            ]);

            $data   = $member->toArray();
            $params = JComponentHelper::getParams('com_users');

            // Get the default new user group, Registered if not specified.
            $system = $params->get('new_usertype', 2);
            $data['groups'][] = $system;

            if (!$user->bind($data)) {
                throw new KControllerExceptionActionFailed("Could not bind data. Error: " . $user->getError());
            }

            if (!$user->save()) {
                throw new KControllerExceptionActionFailed("Could not save user. Error: " . $user->getError());
            }

            $this->id         = $user->id;
            $account          = $this->_createAccount($user->id);
            $this->account_id = $account->id;

            // $subject = "Your Nucleon Plus Account has been activated";
            $subject = 'Your Nucleon + Rewards Account has been created';
            $body    = JText::sprintf(
                "Hello %s,\n\nContratulations! Your Nucleon + Rewards Account has been created. You can now login at %s with the following username and password.\n\nUsername: %s\npassword: %s\n\nHave a nice day!",
                $member->name,
                JUri::root(),
                $member->username,
                $member->password
            );

            $config   = JFactory::getConfig();
            $mailFrom = $config->get('mailfrom');
            $fromName = $config->get('fromname');

            $result = JFactory::getMailer()->sendMail($mailFrom, $fromName, $member->email, $subject, $body);
        }
        else
        {
            $user = JFactory::getUser($member->id);

            $member->remove('password');
            $data = $member->toArray();

            if (!$user->bind($data)) {
                throw new KControllerExceptionActionFailed("Could not bind data. Error: " . $user->getError());
            }

            if (!$user->save(true)) {
                throw new KControllerExceptionActionFailed("Could not save user. Error: " . $user->getError());
            }

            $account          = $this->_updateAccount($user->id);
            $this->account_id = $account->id;
        }

        return true;
    }

    /**
     * Mark member as inactive
     *
     * @return boolean If successful return TRUE, otherwise FALSE
     */
    public function delete()
    {
        if ($this->_account_status != self::STATUS_DELETED)
        {
            $account = $this->getObject('com://site/rewardlabs.model.accounts')
                ->user_id($this->id)
                ->fetch()
            ;

            $account->status = self::STATUS_DELETED;
            
            $result = $account->save();
        }
        else $result = parent::delete();
        
        return $result;
    }

    /**
     * Create corresponding account for each member/user
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface|boolean
     */
    protected function _createAccount($userId)
    {
        $model = $this->getObject('com://site/rewardlabs.model.accounts');

        $account = $model->create(array(
            'id'                  => $this->account_number,
            'status'              => $this->status,
            'user_id'             => $userId,
            'user_name'           => $this->name,
            'sponsor_id'          => $this->sponsor_id,
            'PrintOnCheckName'    => $this->PrintOnCheckName,
            'bank_name'           => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_name'   => $this->bank_account_name,
            'bank_account_type'   => $this->bank_account_type,
            'bank_account_branch' => $this->bank_account_branch,
            'phone'               => $this->phone,
            'mobile'              => $this->mobile,
            'street'              => $this->street,
            'city'                => $this->city,
            'state'               => $this->state,
            'postal_code'         => $this->postal_code,
            'app'                 => $this->app,
            'app_entity'          => $this->app_entity,
        ));
        
        $account->save();
        $account = $model->id($account->id)->fetch();
        return $account;
    }

    /**
     * Update Account
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface
     */
    protected function _updateAccount($userId)
    {
        $account = $this->getObject('com://site/rewardlabs.model.accounts')->user_id($userId)->fetch();

        $account->user_name           = $this->name;
        $account->sponsor_id          = $this->sponsor_id;
        $account->PrintOnCheckName    = $this->PrintOnCheckName;
        $account->bank_name           = $this->bank_name;
        $account->bank_account_number = $this->bank_account_number;
        $account->bank_account_name   = $this->bank_account_name;
        $account->bank_account_type   = $this->bank_account_type;
        $account->bank_account_branch = $this->bank_account_branch;
        $account->phone               = $this->phone;
        $account->mobile              = $this->mobile;
        $account->street              = $this->street;
        $account->city                = $this->city;
        $account->state               = $this->state;
        $account->postal_code         = $this->postal_code;

        $account->save();
        return $account;
    }

    /**
     * Get account
     *
     * @return KModelEntityInterface
     */
    public function getAccount()
    {
        return $this->getObject('com://site/rewardlabs.model.accounts')->user_id($this->id)->fetch();
    }
}