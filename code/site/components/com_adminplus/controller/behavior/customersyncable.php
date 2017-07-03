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
    /**
     * Record sales transaction in the accounting system 
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    protected function _afterAdd(KControllerContext $context)
    {
        $entity = $this->getObject('com://admin/nucleonplus.model.members')->id($context->result->id)->fetch();

        // Salesreceipt data
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
            $account->status = 'Active';
            $account->save();
        } catch(Exception $e) {
            $this->getResponse()->addMessage($e->getMessage(), KControllerResponse::FLASH_WARNING);
        }
    }
}
