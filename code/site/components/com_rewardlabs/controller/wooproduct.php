<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerWooproduct extends ComRewardlabsControllerIntegrationabstract
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
            'model'             => 'com://site/rewardlabs.model.products',
            'columns'           => array(
                'id'             => 'app_entity',
                'name'           => 'Name',
                'description'    => 'Description',
                'price'          => 'UnitPrice',
                'stock_quantity' => 'QtyOnHand',
                'weight'         => 'weight',
            ),
        ));

        parent::_initialize($config);
    }

    protected function _validate(KControllerContextInterface $context)
    {
        $request = $context->request;
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());

        // Ensure woocommerce product is already published when creating a reward labs product
        if ('publish' != $content->status) {
            throw new Exception('Sync aborted - product is not yet published');
        }

        return true;
    }

    protected function _mapColumns(KControllerContextInterface $context)
    {
        $request = $context->request;
        $app     = $request->query->get('app', 'cmd');
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());
        $data    = array();

        // Direct column mapping
        foreach ($this->_columns as $field => $column) {
            $data[$column] = isset($content->$field) ? $content->$field : null;
        }

        // Dynamic column mapping
        if ('edit' == $action)
        {
            // Fetch the identifier of the local copy of the entity
            $entity = $this->getObject($this->_model)->app($app)->app_entity($content->id)->fetch();

            if (count($entity))
            {
                $request->query->set($this->_identifier_column, $entity->id);
                
                // Quantity update is handled in the inventory system for tracking cost
                unset($data['QtyOnHand']);
            }
        }

        $data['app']    = $app;
        $data['Type']   = $content->virtual ? 'Service' : 'Inventory';
        $data['status'] = 'publish' == $content->status ?  'active' : 'inactive';

        // Meta data column mapping
        if (isset($content->meta_data))
        {
            $params = array(
                'charges'       => 'charges',
                'rebates'       => 'rebates',
                'profit'        => 'profit',
                'drpv'          => 'drpv',
                'irpv'          => 'irpv',
                'stockist'      => 'stockist',
                'purchase_cost' => 'PurchaseCost',
            );

            foreach ($content->meta_data as $datum)
            {
                // Map columns
                $column = $params[$datum['key']];
                
                if (!array_key_exists($datum['key'], $params)) {
                    continue;
                }

                if ('edit' == $action && 'PurchaseCost' == $column && count($entity)) {
                    continue;
                }

                $data[$column] = $datum['value'];
            }
        }

        $context->request->setData($data);
    }
}
