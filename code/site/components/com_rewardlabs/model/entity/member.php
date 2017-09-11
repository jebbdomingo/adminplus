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
     * @var ComRewardlabsAccountingServiceMemberInterface
     */
    protected $_member_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $identifier = $this->getIdentifier($config->member_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComRewardlabsAccountingMemberInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComRewardlabsAccountingMemberInterface"
            );
        }
        else $this->_member_service = $service;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'member_service' => 'com://site/rewardlabs.accounting.member'
        ));

        parent::_initialize($config);
    }

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

            if(!$user->bind($data)) {
                throw new KControllerExceptionActionFailed("Could not bind data. Error: " . $user->getError());
            }

            if (!$user->save()) {
                throw new KControllerExceptionActionFailed("Could not save user. Error: " . $user->getError());
            }

            $this->id         = $user->id;
            $account          = $this->_createAccount($user->id);
            $this->account_id = $account->id;
        }
        else
        {
            $user = JFactory::getUser($member->id);

            $member->remove('password');
            $data = $member->toArray();

            if(!$user->bind($data)) {
                throw new KControllerExceptionActionFailed("Could not bind data. Error: " . $user->getError());
            }

            if (!$user->save(true)) {
                throw new KControllerExceptionActionFailed("Could not save user. Error: " . $user->getError());
            }

            $account          = $this->_updateAccount($user->id);
            $this->account_id = $account->id;

            // Only push an update to a synced member/customer to accounting system
            if ($account->CustomerRef) {
                $this->_member_service->pushMember($account, 'update');
            }
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
            'user_id'             => $userId,
            'user_name'           => $this->name,
            'sponsor_id'          => $this->sponsor_id,
            'PrintOnCheckName'    => $this->PrintOnCheckName,
            'status'              => 'pending',
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