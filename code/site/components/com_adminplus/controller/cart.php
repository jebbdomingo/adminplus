<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerCart extends ComKoowaControllerModel
{
    /**
     * Sales Receipt Service
     *
     * @var ComNucleonplusAccountingServiceInventoryInterface
     */
    protected $_inventory_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        @ini_set('max_execution_time', 300);

        parent::__construct($config);

        $this->addCommandCallback('before.checkout', '_validateCheckout');
        $this->addCommandCallback('before.checkout', '_checkInventory');
        $this->addCommandCallback('after.updatecart', '_checkInventory');
        $this->addCommandCallback('after.add', '_checkInventory');
        $this->addCommandCallback('before.add', '_validateAdd');

        // Inventory service
        $identifier = $this->getIdentifier($config->inventory_service);
        $service    = $this->getObject($identifier);
        if (!($service instanceof ComNucleonplusAccountingServiceInventoryInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceInventoryInterface"
            );
        }
        else $this->_inventory_service = $service;

        // Reward service
        $this->_reward = $config->reward;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model'             => 'com://admin/nucleonplus.model.carts',
            'inventory_service' => 'com://admin/nucleonplus.accounting.service.inventory',
        ));

        parent::_initialize($config);
    }

    protected function _validateAdd(KControllerContextInterface $context)
    {
        $data           = $context->request->data;
        $data->row      = $data->ItemRef;
        $data->quantity = $data->form_quantity;

        $translator = $this->getObject('translator');
        $result     = false;

        try
        {
            $cart     = $this->getModel()->fetch();
            $quantity = (int) $data->quantity;

            if (empty($data->row) || !$quantity) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please select an item and specify its quantity'));
            }

            $result = true;
        }
        catch(Exception $e)
        {
            $context->response->setRedirect($context->request->getReferrer(), $e->getMessage(), KControllerResponse::FLASH_ERROR);
            $context->response->send();
        }

        return $result;
    }

    protected function _validateCheckout(KControllerContextInterface $context)
    {
        $translator = $this->getObject('translator');
        $result     = false;

        try
        {
            $cart = $this->getModel()->fetch();

            if (count($cart->getItems()) == 0) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please add an item to checkout'));
            }

            $result = true;
        }
        catch(Exception $e)
        {
            $context->response->setRedirect($context->request->getReferrer(), $e->getMessage(), KControllerResponse::FLASH_ERROR);
            $context->response->send();
        }

        return $result;
    }

    protected function _checkInventory(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $cart = $this->getModel()->fetch();
        } else {
            $cart = $context->result;
        }

        $translator = $this->getObject('translator');
        $error      = false;

        if (count($cart))
        {
            $itemQty = $cart->getItemQuantities();

            foreach ($itemQty as $id => $qty)
            {
                $result = $this->_inventory_service->getQuantity($id, true);

                if ($result['available'] < $qty)
                {
                    $error  = "Insufficient stock of {$result['Name']}, only ({$result['available']}) item/s left in stock and you already have ({$qty}) in your shopping cart";
                    
                    if (JDEBUG)
                    {
                        $error .= '<pre>' . print_r($itemQty, true) . '</pre>';
                        $error .= '<pre>' . print_r($result, true) . '</pre>';
                    }
                }
            }
        }

        if ($error)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $translator->translate($error), KControllerResponse::FLASH_ERROR);
            $context->getResponse()->send();
        }
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $data      = $context->request->data;
        $cart      = $this->getModel()->fetch();
        $cartItems = array();

        if (count($cart))
        {
            // Add item(s) to the cart
            if ($items = $cart->getItems())
            {
                foreach ($items as $item)
                {
                    $cartItems[] = $item->row;

                    // Existing item, update quantity instead
                    if ($item->row == $data->ItemRef)
                    {
                        $item->quantity += $data->form_quantity;
                        $item->save();
                    }
                }
            }

            if (!in_array($data->ItemRef, $cartItems))
            {
                // New item
                $cartItemData = array(
                    'cart_id'  => $cart->id,
                    'row'      => $data->ItemRef,
                    'quantity' => $data->form_quantity,
                );

                $item = $this->getObject('com:cart.model.items')->create($cartItemData);
                $item->save();
            }
        }

        $response = $context->getResponse();
        $response->addMessage('Item added to the shopping cart');

        return $cart;
    }

    protected function _actionUpdatecart(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $cart = $this->getModel()->fetch();
        } else {
            $cart = $context->result;
        }

        if (count($cart))
        {
            $cart->setProperties($context->request->data->toArray());
            $cart->save();

            if (in_array($cart->getStatus(), array(KDatabase::STATUS_FETCHED, KDatabase::STATUS_UPDATED)))
            {
                foreach ($cart->getItems() as $item)
                {
                    $quantity = (int) $context->request->data->quantity[$item->id];

                    if ($quantity)
                    {
                        $item->quantity = (int) $context->request->data->quantity[$item->id];
                        $item->save();
                    }
                    else $item->delete();
                }

                $context->response->addMessage('You shopping cart has been updated');
            }
            else $context->response->addMessage($cart->getStatusMessage(), KControllerResponse::FLASH_ERROR);
        }

        return $cart;
    }

    protected function _actionCheckout(KControllerContextInterface $context)
    {
        $data = array(
            'account_id' => $context->request->data->account_id,
            'cart_id'    => $context->request->data->cart_id,
        );

        $controller = $this->getObject('com://site/adminplus.controller.order');
        $controller->add($data);
        
        $result = $controller->getResponse()->getMessages();

        if (isset($result['success']))
        {
            foreach ($result['success'] as $message) {
                $context->response->addMessage($message);
            }
        }
    }

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        $data  = $context->request->data;
        $ids   = $data->id;
        $items = $this->getObject('com://admin/cart.model.items')->id($ids)->fetch();

        foreach ($items as $item) {
            $item->delete();
        }

        $context->response->addMessage('Item has been deleted from the shopping cart');
    }
}
