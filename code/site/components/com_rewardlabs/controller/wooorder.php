<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerWooorder extends ComRewardlabsControllerIntegrationabstract
{
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
            'identifier_column' => 'id',
            'model'             => 'com://site/rewardlabs.model.orders',
            'columns'           => array(),
            'behaviors'         => array(
                'accountable',
                'chargeable',
                'referrerrewardable',
                'rebatable',
            )
        ));

        parent::_initialize($config);
    }

    protected function _validate(KControllerContextInterface $context)
    {
        $request = $context->request;
        $app     = $request->query->get('app', 'cmd');
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());
        $items   = array();

        // Validate status
        if ('completed' != $content->status) {
            throw new Exception('Rewards creation aborted - order is not yet completed');
        }

        if ('add' == $action)
        {
            // Ensure the order is synced once
            $order = $this->getObject('com://site/rewardlabs.model.orders')->app($app)->app_entity($content->id)->count();

            if ($order) {
                throw new KControllerExceptionActionFailed("Order {$content->id} already exists");
            }

            // Validate sponsor id
            foreach ($content->meta_data as $datum)
            {
                if ('sponsor_id' == $datum['key'])
                {
                    $sponsor_id = trim($datum['value']);

                    if (!empty($sponsor_id))
                    {
                        $account = $this->getObject('com://site/rewardlabs.model.accounts')
                            ->id($sponsor_id)
                            ->fetch();

                        if (count($account) == 0) {
                            throw new KControllerExceptionActionFailed('Invalid sponsor id');
                        }
                    }

                    break;
                }
            }

            // Ensure order line items are encoded only once to prevent multiple payments of rewards for single order
            foreach ($content->line_items as $item)
            {
                $id      = $item['id'];
                $rewards = $this->getObject('com://site/rewardlabs.model.rewards')->item($id)->count();

                if ($rewards) {
                    throw new KControllerExceptionActionFailed("Order line item {$id} was already encoded");
                }
            }
        }

        return true;
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $request = $context->request;
        $app     = $request->query->get('app', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());
        
        $data = array(
            'app'             => $app,
            'app_entity'      => $content->id,
            'account'         => $content->customer_id,
            'order_status'    => $content->status,
            'invoice_status'  => $content->status,
            'payment_method'  => $content->payment_method,
            'shipping_method' => count($content->shipping_lines) ? $content->shipping_lines[0]['method_title'] : 'na',
            'shipping_cost'   => $content->shipping_total,
            'address'         => $content->shipping['address_1'],
            'city_id'         => $content->shipping['city'],
            'postal_code'     => $content->shipping['postcode']
        );

        // Customer account
        $account = $this->getObject('com://site/rewardlabs.model.accounts')
                    ->app($app)
                    ->app_entity($content->customer_id)
                    ->fetch();

        if (count($account)) {
            $data['account'] = $account->id;
        }

        $order = $this->getObject($this->_model)->create($data);

        if ($order->save())
        {
            // Fetch newly created order to get the joined columns
            $order = $this->getObject($this->_model)->id($order->id)->fetch();

            foreach ($content->line_items as $line_item)
            {
                $item = $this->getObject('com://site/rewardlabs.model.products')
                    ->app($app)
                    ->app_entity($line_item['product_id'])
                    ->fetch();

                $orderItem = $this->getObject('com://site/rewardlabs.model.orderitems')->create(array(
                    'order_id'   => $order->id,
                    'ItemRef'    => $item->ItemRef,
                    'item_name'  => $item->Name,
                    'item_price' => $item->UnitPrice,
                    'quantity'   => $line_item['quantity'],
                    'drpv'       => $item->drpv,
                    'irpv'       => $item->irpv,
                    'rebates'    => $item->rebates,
                    'stockist'   => $item->stockist,
                    'charges'    => $item->charges,
                ));
                $orderItem->save();
            }

            // Calculate order totals based on order items
            $order->calculate()->save();
        }
        else throw new KControllerExceptionActionFailed($order->getStatusMessage());

        return $order;
    }

    protected function _actionSync(KControllerContextInterface $context)
    {
        $request = $context->request;
        $content = $request->data ? $request->data : json_decode($request->getContent());
        
        $order = $this->getObject($this->_model)->id($content->id)->fetch();

        return $order;
    }
}
