<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerBehaviorCustomersyncable extends KControllerBehaviorAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.add', '_add');
        $this->addCommandCallback('after.edit', '_update');
        $this->addCommandCallback('after.delete', '_update');
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
        $entity = $this->getObject('com://admin/nucleonplus.model.members')->id($context->result->id)->fetch();

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
            'State'            => $entity->province,
            'PostalCode'       => $entity->_account_postal_code,
            'Country'          => 'Philippines',
        );

        try {
            $resp = $this->getObject('com:qbsync.service.customer')->create($customer);
            $account = $entity->getAccount();
            $account->CustomerRef = $resp;
            $account->status      = ComNucleonplusModelEntityMember::STATUS_ACTIVE;
            $account->save();
        } catch(Exception $e) {
            $this->getResponse()->addMessage($e->getMessage(), KControllerResponse::FLASH_WARNING);
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
        $entity  = $this->getObject('com://admin/nucleonplus.model.members')->id($context->result->id)->fetch();
        $service = $this->getObject('com:qbsync.service.customer');

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
                'State'            => $entity->province,
                'PostalCode'       => $entity->_account_postal_code,
                'Country'          => 'Philippines',
                'Active'           => $entity->_account_status == ComNucleonplusModelEntityMember::STATUS_DELETED ? false : true
            );

            try {
                $service->update($customer);
            } catch(Exception $e) {
                $this->getResponse()->addMessage(
                    $e->getMessage(),
                    KControllerResponse::FLASH_WARNING
                );
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