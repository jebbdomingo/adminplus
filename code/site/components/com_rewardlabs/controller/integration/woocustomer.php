<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComRewardlabsControllerIntegrationWoocustomer extends ComRewardlabsControllerIntegrationAbstract
{
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

        $this->_columns = KObjectConfig::unbox($config->columns);
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
            'model'             => 'com://site/rewardlabs.model.members',
            'columns'           => array(
                'sponsor_id',
                'name',
                'username',
                'email'
            ),
            // 'behaviors' => array(
            //     'customersyncable' => array(
            //         'actions' => array(
            //             'after.addcustomer'    => 'add',
            //             'after.editcustomer'   => 'update',
            //             'after.deletecustomer' => 'update',
            //         )
            //     ),
            // ),
        ));

        parent::_initialize($config);

        // Alter the permission of reward labs product controller
        $config->append(array(
            'behaviors' => array(
                'permissible' => 'com://site/rewardlabs.controller.permission.woocustomer'
            )
        ));
    }

    protected function _validate(KControllerContextInterface $context)
    {
        parent::_validate($context);

        $request   = $context->request;
        $data      = $request->data;
        $sponsorId = trim($data->sponsor_id);

        if (!empty($sponsorId))
        {
            $account = $this->getObject('com://site/rewardlabs.model.accounts')
                ->id($sponsorId)
                ->fetch();

            if (count($account) == 0) {
                throw new KControllerExceptionActionFailed('INVALID_SPONSOR_ID');
            }
        }

        return true;
    }

    // protected function _beforeAdd(KControllerContextInterface $context)
    // {
    //     $request = $context->request;
    //     $query   = $request->query;
    //     $content = json_decode($request->getContent());
    //     $data    = array();

    //     foreach ($this->_columns as $column) {
    //         $data[$column] = isset($content->$column) ? $content->$column : null;
    //     }

    //     $data['name']       = isset($content->first_name) ? "{$content->first_name} {$content->last_name}" : $content->username;
    //     $data['sponsor_id'] = isset($content->sponsor_id) ? $content->sponsor_id : null;

    //     $context->request->setData($data);
    // }

    // protected function _beforeEdit(KControllerContextInterface $context)
    // {
    //     $request = $context->request;
    //     $query   = $request->query;
    //     $content = json_decode($request->getContent());
    //     $data    = array();

    //     foreach ($this->_columns as $column) {
    //         $data[$column] = isset($content->$column) ? $content->$column : null;
    //     }

    //     $data['name']       = "{$content->first_name} {$content->last_name}";
    //     $data['sponsor_id'] = isset($content->sponsor_id) ? $content->sponsor_id : null;

    //     $context->request->setData($data);
    // }

    protected function _mapColumns(KControllerContextInterface $context)
    {
        $request = $context->request;
        $app     = $request->query->get('app', 'cmd');
        $action  = $request->query->get('action', 'cmd');
        $content = $request->data ? $request->data : json_decode($request->getContent());
        $data    = array();

        foreach ($this->_columns as $column) {
            $data[$column] = isset($content->$column) ? $content->$column : null;

        }
        // Fetch the identifier of the local copy of the entity
        if ('edit' == $action)
        {
            $data['name'] = "{$content->first_name} {$content->last_name}";

            $entity = $this->getObject('com://site/rewardlabs.model.accounts')->app($app)->app_entity($content->id)->fetch();
            $request->query->set($this->_identifier_column, $entity->user_id);
        }
        elseif ('add' == $action)
        {
            $data['name'] = !empty($content->first_name) ? "{$content->first_name} {$content->last_name}" : $content->username;
        }

        $data['app']        = $app;
        $data['app_entity'] = $content->id;
        $data['sponsor_id'] = isset($content->sponsor_id) ? $content->sponsor_id : null;

        $context->request->setData($data);
    }
}
