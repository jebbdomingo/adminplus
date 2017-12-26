<?php

/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */


/**
 * Order Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComRewardlabsControllerOrder extends ComKoowaControllerModel
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
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionSync(KControllerContextInterface $context)
    {
        $controller = $this->getObject('com://site/rewardlabs.controller.wooorder');
        $controller->getRequest()->getQuery()->set('action', 'sync');

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        if (count($entities))
        {
            foreach($entities as $entity)
            {
                $controller->getRequest()->getQuery()->set('app', $entity->app);

                $data = array(
                    'id'             => $entity->id,
                    'customer_id'    => $entity->account,
                    'status'         => $entity->order_status,
                    'payment_method' => $entity->payment_method,
                    'shipping_lines' => array(array('method_title' => $entity->shipping_method)),
                    'shipping_total' => $entity->shipping_cost,
                    'shipping'       => array(
                        'address_1' => $entity->address,
                        'city'      => $entity->city,
                        'postcode'  => $entity->postal_code
                    )
                );

                // var_dump(get_class($controller));die;
                $controller->getRequest()->setData($data);
                $controller->sync();

                $context->response->addMessage("Order #{$entity->id} has been synced.");
            }
        }

        return $entities;
    }
}
