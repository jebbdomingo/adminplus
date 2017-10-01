<?php
/**
 * Reward Labs
 *
 * @package     Reward Labs
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerBehaviorPayoutable extends KControllerBehaviorAbstract
{
    /**
     * List of actions
     *
     * @var array
     */
    protected $_actions;

    /**
     * List of columns
     *
     * @var array
     */
    protected $_columns;

    /**
     *
     * @var ComRewardlabsAccountingServiceTransferInterface
     */
    protected $_accounting_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_actions = KObjectConfig::unbox($config->actions);
        $this->_columns = KObjectConfig::unbox($config->columns);

        // Accounting Service
        $identifier = $this->getIdentifier($config->accounting_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComRewardlabsAccountingTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComRewardlabsAccountingTransferInterface"
            );
        }
        else $this->_accounting_service = $service;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'accounting_service' => 'com://site/rewardlabs.accounting.transfer',
            'priority'           => self::PRIORITY_LOWEST, // Ensure masspayable runs first
            'actions'            => array('after.processing'),
            'columns'            => array(
                'id'                 => 'id',
                'rebates'            => 'rebates',
                'direct_referrals'   => 'username',
                'indirect_referrals' => 'amount',
            )
        ));

        // Append the default action if none is set.
        if (!count($config->actions)) {
            $config->append(array('actions' => array('after.edit')));
        }

        parent::_initialize($config);
    }

    /**
     * Command handler.
     *
     * @param KCommandInterface      $command The command.
     * @param KCommandChainInterface $chain   The chain executing the command.
     * 
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    final public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $controller = $this->getObject($this->_controller);
        $action     = $command->getName();

        if ($controller instanceof KControllerModellable && in_array($action, $this->_actions))
        {
            $env = getenv('APP_ENV');

            // @todo move dragonpay config to its own table
            $config = $this->getObject('com://site/rewardlabs.model.configs')->item('dragonpay')->fetch();

            $dragonpay = $config->getJsonValue();
            $entities  = $this->getEntity($command);

            foreach ($entities as $entity)
            {
                $data = $this->getData($entity);

                $rebates            = (float) $data['rebates'];
                $direct_referrals   = (float) $data['direct_referrals'];
                $indirect_referrals = (float) $data['indirect_referrals'];

                try
                {
                    if ($rebates > 0) {
                        $this->_accounting_service->rebatesCheck($data['id'], $rebates);
                    }

                    if ($direct_referrals > 0) {
                        $this->_accounting_service->directReferralCheck($data['id'], $direct_referrals);
                    }

                    if ($indirect_referrals > 0) {
                        $this->_accounting_service->indirectReferralCheck($data['id'], $indirect_referrals);
                    }
                }
                catch(Exception $e)
                {
                    $error = $e->getMessage();

                    $data = '<pre>' . print_r($data, true) . '</pre>';

                    $this->getContext()->response->addMessage($error, 'exception');
                    $this->getContext()->response->addMessage($data, 'exception');
                }
            }
        }
    }

    /**
     * Get the entity.
     *
     * @param KCommandInterface $command The command.
     *
     * @return KModelEntityInterface
     */
    public function getEntity(KCommandInterface $command)
    {
        $parts = explode('.', $command->getName());

        // Properly fetch data for the event.
        if ($parts[0] == 'before') {
            $entity = $command->getSubject()->getModel()->fetch();
        } else {
            $entity = $command->result;
        }

        return $entity;
    }

    /**
     * Get the data.
     *
     * @param KModelEntityInterface $entity
     * 
     * @return array data.
     */
    public function getData(KModelEntityInterface $entity)
    {
        $data = array();

        foreach ($this->_columns as $name => $column)
        {
            if ($entity->{$column}) {
                $data[$name] = $entity->{$column};
            }
        }

        return $data;
    }

    /**
     * Get the behavior name.
     *
     * Hardcode the name to 'loggable'.
     *
     * @return string
     */
    final public function getName()
    {
        return 'masspayable';
    }

    /**
     * Get an object handle.
     *
     * Force the object to be enqueued in the command chain.
     *
     * @see execute()
     *
     * @return string A string that is unique, or NULL.
     */
    final public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}
