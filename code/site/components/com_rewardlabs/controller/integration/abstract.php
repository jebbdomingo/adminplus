<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

abstract class ComRewardlabsControllerIntegrationAbstract extends KControllerAbstract
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
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_debug');
        $this->addCommandCallback('before.add', '_validate');
        $this->addCommandCallback('before.edit', '_debug');
        $this->addCommandCallback('before.edit', '_validate');
        $this->addCommandCallback('before.delete', '_debug');
        $this->addCommandCallback('before.delete', '_validate');

        $this->_identifier_column = $config->identifier_column;
        $this->_model             = $config->model;
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
            'identifier_column' => null,
            'model'             => null,
        ));

        parent::_initialize($config);

        // Alter the permission of reward labs product controller
        $config->append(array(
            'behaviors' => array(
                'permissible' => null
            )
        ));
    }

    protected function _validate(KControllerContextInterface $context)
    {
        return true;
    }

    protected function _debug(KControllerContextInterface $context)
    {
        $data = array(
            'request_data' => $context->request->toString()
        );

        $log = $this->getObject('com://site/rewardlabs.model.httplogs')->create($data);
        $log->save();
    }

    protected function _mapColumns(KControllerContextInterface $context)
    {
        return array();
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $this->_mapColumns($context);

        $data = $context->request->data;

        $entity = $this->getObject($this->_model)->create($data->toArray());
        $entity->save();

        return $entity;
    }

    protected function _actionEdit(KControllerContextInterface $context)
    {
        $this->_mapColumns($context);

        $id   = $context->request->query->get($this->_identifier_column, 'cmd', false);
        $data = $context->request->data;

        $entity = $this->getObject($this->_model)->id($id)->fetch();

        if (count($entity))
        {
            $entity->setProperties($data->toArray());
            $entity->save();
        }

        return $entity;
    }
}
