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

class ComAdminplusControllerBehaviorAccountable extends KControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_department_ref          = $config->department_ref;
        $this->_online_payments_account = $config->online_payments_account;
        $this->_bank_account_ref        = $config->bank_account_ref;
        $this->_undeposited_account_ref = $config->undeposited_account_ref;
        $this->_shipping_account        = $config->shipping_account;
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
        $data = $this->getObject('com://admin/nucleonplus.accounting.service.data');

        $config->append(array(
            'department_ref'               => $data->store_angono,
            'online_payments_account'      => $data->ACCOUNT_ONLINE_PAYMENTS, // Online payment processor account
            'bank_account_ref'             => $data->account_bank_ref, // Bank Account
            'undeposited_account_ref'      => $data->account_undeposited_ref, // Undeposited Funds Account
            'shipping_account'             => $data->ACCOUNT_INCOME_SHIPPING
        ));

        parent::_initialize($config);
    }

    /**
     * Record sales transaction in the accounting system 
     *
     * @param KControllerContext $context
     *
     * @return void
     */
    protected function _afterAdd(KControllerContext $context)
    {
        $order = $context->result;

        // Salesreceipt data
        $salesreceipt = array(
            'DocNumber'    => $order->id,
            'TxnDate'      => date('Y-m-d'),
            'CustomerRef'  => $order->_account_customer_ref,
            'CustomerMemo' => 'Thank you for your business and have a great day!',
        );

        if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        {
            // Online payment
            $salesreceipt['DepartmentRef']       = $this->_department_ref; // Angono EC Valle store
            $salesreceipt['DepositToAccountRef'] = $this->_online_payments_account; // Online payment processor account
            $salesreceipt['transaction_type']    = 'online'; // Customer ordered thru website
        }
        else
        {
            // Cash
            $user     = $this->getObject('user');
            $employee = $this->getObject('com:nucleonplus.model.employeeaccounts')->user_id($user->getId())->fetch();
            
            $salesreceipt['DepartmentRef']       = $employee->DepartmentRef; // Store branch
            $salesreceipt['DepositToAccountRef'] = $this->_undeposited_account_ref; // Undeposited Funds Account
            $salesreceipt['transaction_type']    = 'offline'; // Order placed via onsite POS
        }

        // Shipping address
        $salesreceipt['ShipAddr'] = array(
            'Line1' => $order->address,
            'Line2' => $order->city,
            'Line3' => $order->country
        );

        // Line items
        $lines = array();

        foreach ($order->getOrderItems() as $orderItem)
        {
            $lines[] = array(
                'Description' => $orderItem->item_name,
                'Amount'      => ($orderItem->item_price * $orderItem->quantity),
                'Qty'         => $orderItem->quantity,
                'ItemRef'     => $orderItem->ItemRef,
            );
        }

        // Shipping charge line item
        if ($order->shipping_method == 'xend' && $order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        {
            // Delivery charge
            if ($shippingCost = $order->shipping_cost)
            {
                $lines[] = array(
                    'Description' => 'Shipping',
                    'Amount'      => $shippingCost,
                    'Qty'         => $orderItem->quantity,
                    'ItemRef'     => $this->_shipping_account,
                );
            }
        }

        $salesreceipt['lines'] = $lines;

        $resp = $this->getObject('com:qbsync.service.salesreceipt')->create($salesreceipt);
        $order->SalesReceiptRef = $resp;
        $order->save();
    }
}