<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerBehaviorCustomersyncable extends KControllerBehaviorAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $actions = KObjectConfig::unbox($config->actions);

        foreach ($actions as $event => $action) {
            $this->addCommandCallback($event, "_{$action}");
        }
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
            'actions' => array(
                'after.add'    => 'add',
                'after.edit'   => 'update',
                'after.delete' => 'update',
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Sync customer to QBO
     *
     * @param KControllerContextInterface $context
     * @throws Exception
     *
     * @return void
     */
    protected function _add(KControllerContextInterface $context)
    {
        $entity = $this->getObject('com://site/rewardlabs.model.members')->id($context->result->id)->fetch();

        // Customer data
        $customer = array(
            'DisplayName'      => "{$entity->name} - {$entity->_account_number}",
            'PrintOnCheckName' => $entity->_account_check_name,
            'PrimaryPhone'     => $entity->_account_phone,
            'CustomerRef'      => $entity->_account_customer_ref,
            'Mobile'           => $entity->_account_mobile,
            'PrimaryEmailAddr' => $entity->email,
            'Line1'            => $entity->_account_street,
            'City'             => $entity->city,
            'PostalCode'       => $entity->_account_postal_code,
            'Country'          => 'Philippines',
        );

        try {
            $resp = $this->getObject('com://admin/qbsync.service.customer')->create($customer);
            $account = $entity->getAccount();
            $account->CustomerRef = $resp;
            $account->status      = ComRewardlabsModelEntityMember::STATUS_ACTIVE;
            $account->save();
        } catch(Exception $e) {
            throw new KControllerExceptionActionFailed($e->getMessage());
        }
    }

    /**
     * Sync customer to QBO
     *
     * @param KControllerContextInterface $context
     * @throws Exception
     *
     * @return void
     */
    protected function _update(KControllerContextInterface $context)
    {
        $entity  = $this->getObject('com://site/rewardlabs.model.members')->id($context->result->id)->fetch();
        $service = $this->getObject('com://admin/qbsync.service.customer');

        if ($service->get($entity->_account_customer_ref) !== false)
        {
            // Customer data
            $customer = array(
                'DisplayName'      => "{$entity->name} - {$entity->_account_number}",
                'PrintOnCheckName' => $entity->_account_check_name,
                'PrimaryPhone'     => $entity->_account_phone,
                'CustomerRef'      => $entity->_account_customer_ref,
                'Mobile'           => $entity->_account_mobile,
                'PrimaryEmailAddr' => $entity->email,
                'Line1'            => $entity->_account_street,
                'City'             => $entity->city,
                'PostalCode'       => $entity->_account_postal_code,
                'Country'          => 'Philippines',
                'Active'           => $entity->_account_status == ComRewardlabsModelEntityMember::STATUS_DELETED ? false : true
            );

            try {
                $service->update($customer);
            } catch(Exception $e) {
                throw new KControllerExceptionActionFailed($e->getMessage());
            }
        }
        else $this->_add($context);
    }

    /**
     * Wraps controller edit action and sync customer
     *
     * @param  KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionActivate(KControllerContextInterface $context)
    {
        $entity = $context->getSubject()->execute('edit', $context);

        if ($entity->getStatus() === KModelEntityInterface::STATUS_FAILED)
        {
            $error = $entity->getStatusMessage();
            throw new KControllerExceptionActionFailed($error ? $error : "{$action} Action Failed");
        }
        else $context->response->setRedirect($this->getReferrer($context));

        return $entity;
    }
}
