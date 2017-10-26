<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerIntegrationWooproduct extends ComRewardlabsControllerIntegrationAbstract
{
    /**
     * Model
     *
     * @var string
     */
    protected $_model;

    /**
     * Identifier
     *
     * @var string
     */
    protected $_identifier_column;

    /**
     * Columns
     *
     * @var array
     */
    protected $_columns;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_identifier_column = $config->identifier_column;
        $this->_model             = $config->model;
        $this->_columns           = KObjectConfig::unbox($config->columns);
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
            'identifier_column' => 'id',
            'model'             => 'com://site/rewardlabs.model.products',
            'columns'           => array(
                'Name',
                'Description',
                'Type',
                'UnitPrice',
                'QtyOnHand',
                'weight',
            ),
        ));

        parent::_initialize($config);

        // Alter the permission of reward labs product controller
        $config->append(array(
            'behaviors' => array(
                'permissible' => 'com://site/rewardlabs.controller.permission.wooproduct'
            )
        ));
    }

    protected function _mapColumns(KControllerContextInterface $context)
    {
        $request = $context->request;
        $app     = $request->query->get('app', 'cmd');
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());
        $data    = array();

        // Fetch the identifier of the local copy of the entity
        if ('edit' == $action)
        {
            $entity = $this->getObject($this->_model)->app($app)->app_entity($content->id)->fetch();
            $request->query->set($this->_identifier_column, $entity->id);
        }

        foreach ($this->_columns as $column) {
            $data[$column] = isset($content->$column) ? $content->$column : null;
        }

        $data['app']         = $app;
        $data['app_entity']  = $content->id;
        $data['Name']        = $content->name;
        $data['Description'] = $content->description;
        $data['Type']        = $content->virtual ? 'Service' : 'Inventory';
        $data['UnitPrice']   = $content->price;
        $data['QtyOnHand']   = $content->stock_quantity;
        $data['Active']      = 1;

        if (isset($content->meta_data))
        {
            $params = array('charges','rebates','profit','drpv','irpv','stockist');

            foreach ($content->meta_data as $datum)
            {
                if (in_array($datum['key'], $params)) {
                    $data[$datum['key']] = $datum['value'];
                }
            }
        }

        $context->request->setData($data);
    }
}
