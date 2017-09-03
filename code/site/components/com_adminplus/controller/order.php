<?php

/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */


/**
 * Order Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComAdminplusControllerOrder extends ComKoowaControllerModel
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

        $this->addCommandCallback('before.add', '_validate');
        $this->addCommandCallback('before.processing', '_validateProcessing');
        $this->addCommandCallback('before.ship', '_validateShip');
        $this->addCommandCallback('before.markdelivered', '_validateDelivered');
        $this->addCommandCallback('before.markcompleted', '_validateCompleted');
        $this->addCommandCallback('before.cancelorder', '_validateCancelorder');
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
                'com://admin/nucleonplus.controller.behavior.cancellable',
                'com://admin/nucleonplus.controller.behavior.processable',
                'com://admin/nucleonplus.controller.behavior.shippable',
                'com:xend.controller.behavior.shippable',
                'rewardable',
                'accountable',
                'chargeable',
                'referrerrewardable',
                'rebatable',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Validate add
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validate(KControllerContextInterface $context)
    {
        // Inventory service
        $identifier = $this->getIdentifier('com://admin/nucleonplus.accounting.service.inventory');
        $service    = $this->getObject($identifier);
        if (!($service instanceof ComNucleonplusAccountingServiceInventoryInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceInventoryInterface"
            );
        }

        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $account    = $this->getObject('com://admin/nucleonplus.model.accounts')->id($data->account_id)->fetch();
        $cart       = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();
        $error      = false;
        $result     = false;

        // Validate account
        if (count($account) === 0)
        {
            $error = 'Invalid Account';
        }
        else
        {
            if (count($cart) && count($cart->getItems()))
            {
                $itemQty = $cart->getItemQuantities();
                
                foreach ($itemQty as $id => $qty)
                {
                    $result = $service->getQuantity($id, true);

                    if ($result['available'] < $qty)
                    {
                        $error  = "Insufficient stock of {$result['Name']}, only ({$result['available']}) item/s left in stock and you already have ({$qty}) in the cart";
                        
                        if (JDEBUG)
                        {
                            $error .= '<pre>' . print_r($itemQty, true) . '</pre>';
                            $error .= '<pre>' . print_r($result, true) . '</pre>';
                        }
                    }
                }
            }
            else
            {
                $error = 'Cart System Error';
            }
        }

        if ($error)
        {
            throw new Exception($error);
        }
        else
        {
            $order_status    = ComNucleonplusModelEntityOrder::STATUS_COMPLETED;
            $invoice_status  = 'paid';
            $payment_method  = 'cash';
            $shipping_method = 'na';

            $data = new KObjectConfig([
                'account_id'      => $account->id,
                'cart_id'         => $cart->id,
                'order_status'    => $order_status,
                'invoice_status'  => $invoice_status,
                'payment_method'  => $payment_method,
                'shipping_method' => $shipping_method
            ]);

            $context->getRequest()->setData($data->toArray());

            $result = true;
        }

        return $result;
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
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($orders as $order)
            {
                $order->setProperties($context->request->data->toArray());

                if (!$this->canProcess()) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only "Verified" Orders can be processed'));
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
     * Validate ship action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateShip(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($orders as $order)
            {
                $order->setProperties($context->request->data->toArray());

                if (!$this->hasTrackingNumber()) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Please enter shipment tracking reference'));
                }

                if (!$this->canShip()) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Processing" status can be shipped'));
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
     * Validate delivered action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateDelivered(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($entities as $entity)
            {
                if ($entity->order_status <> ComNucleonplusModelEntityOrder::STATUS_SHIPPED) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Shipped" status can be marked as "Delivered"'));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }

    /**
     * Validate completed action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCompleted(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($entities as $entity)
            {
                if ($entity->order_status <> ComNucleonplusModelEntityOrder::STATUS_DELIVERED) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Delivered" status can be marked as "Completed"'));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }

    /**
     * Validate cancellation of order
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCancelorder(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($orders as $order)
            {
                $order->setProperties($context->request->data->toArray());

                if (!in_array($order->order_status, array(ComNucleonplusModelEntityOrder::STATUS_PAYMENT, ComNucleonplusModelEntityOrder::STATUS_PENDING))) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Pending" or "Awaiting Payment" status can be cancelled'));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $user       = $this->getObject('user');
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $cart       = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();

        $order = $this->getModel()->create(array(
            'account'         => $data->account_id,
            'order_status'    => $data->order_status,
            'invoice_status'  => $data->invoice_status,
            'payment_method'  => $data->payment_method,
            'shipping_method' => $data->shipping_method,
            'address'         => $cart->address,
            'city_id'         => $cart->city_id,
            'postal_code'     => $cart->postal_code
        ));

        if ($order->save())
        {
            // Fetch newly created order to get the joined columns
            $order = $this->getObject('com://admin/nucleonplus.model.orders')->id($order->id)->fetch();

            foreach ($cart->getItems() as $item)
            {
                $orderItem = $this->getObject('com://admin/nucleonplus.model.orderitems')->create(array(
                    'order_id'   => $order->id,
                    'ItemRef'    => $item->_item_ref,
                    'item_name'  => $item->_item_name,
                    'item_price' => $item->_item_price,
                    'item_image' => $item->_item_image,
                    'quantity'   => $item->quantity,
                ));
                $orderItem->save();
            }

            // Calculate order totals based on order items
            $order->calculate()->save();

            /**
             * @todo Move cart operation to com:cart behavior
             */
            // Delete the cart
            $cart->delete();

            $context->response->addMessage('Order completed');
        }

        return $order;
    }

    /**
     * Disallow direct editing
     *
     * @param KControllerContextInterface $context
     *
     * @throws KControllerExceptionRequestNotAllowed
     *
     * @return void
     */
    protected function _actionEdit(KControllerContextInterface $context)
    {
        throw new KControllerExceptionRequestNotAllowed('Direct editing of order is not allowed');
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionProcessing(KControllerContextInterface $context)
    {
        $context->request->data->order_status = ComNucleonplusModelEntityOrder::STATUS_PROCESSING;

        $order = parent::_actionEdit($context);

        $context->response->addMessage("Order #{$order->id} has been marked on-processing.");

        return $order;
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionShip(KControllerContextInterface $context)
    {
        $context->request->data->order_status       = ComNucleonplusModelEntityOrder::STATUS_SHIPPED;
        $context->request->data->tracking_reference = $context->request->data->tracking_reference;
        $context->request->data->_couriers          = array_keys((array) $this->getModel()->fetch()->getCouriers());

        $order = parent::_actionEdit($context);

        $context->response->addMessage("Order #{$order->id} has been marked as shipped.");

        return $order;
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionMarkdelivered(KControllerContextInterface $context)
    {
        $context->getRequest()->setData([
            'order_status' => ComNucleonplusModelEntityOrder::STATUS_DELIVERED
        ]);

        $order = parent::_actionEdit($context);

        $context->response->addMessage("Order #{$order->id} has been marked as delivered.");

        return $order;
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionMarkcompleted(KControllerContextInterface $context)
    {
        $context->getRequest()->setData([
            'order_status' => ComNucleonplusModelEntityOrder::STATUS_COMPLETED
        ]);

        $order = parent::_actionEdit($context);

        $context->response->addMessage("Order #{$order->id} has been marked as completed.");

        return $order;
    }

    /**
     * Cancel Order
     *
     * @param KControllerContextInterface $context
     *
     * @return entity
     */
    protected function _actionCancelorder(KControllerContextInterface $context)
    {
        // Mark as Paid
        $context->getRequest()->setData([
            'order_status' => ComNucleonplusModelEntityOrder::STATUS_CANCELLED
        ]);

        $order = parent::_actionEdit($context);

        $context->response->addMessage("Order #{$order->id} has been cancelled.");

        return $order;
    }

    /**
     * Activates the reward
     *
     * @param   KModelEntityInterface $order
     * 
     * @throws  KControllerExceptionRequestInvalid
     * @throws  KControllerExceptionResourceNotFound
     * 
     * @return  void
     */
    protected function _activateReward(KModelEntityInterface $order)
    {
        $translator = $this->getObject('translator');

        // Check order status if its reward can be activated
        if ($order->order_status != ComNucleonplusModelEntityOrder::STATUS_COMPLETED) {
            throw new KControllerExceptionRequestInvalid($translator->translate("Unable to activate corresponding reward: Order #{$order->id} should be in \"Completed\" status"));
        }

        // Try to activate reward
        $rewards = $order->getRewards();
        
        foreach ($rewards as $reward) {
            $this->getObject('com:nucleonplus.controller.reward')->id($reward->id)->activate();
        }
    }
}
