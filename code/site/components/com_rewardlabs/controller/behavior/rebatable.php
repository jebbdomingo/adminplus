<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsControllerBehaviorRebatable extends KControllerBehaviorAbstract
{
    /**
     * Rewards model
     *
     * @param KModelInterface
     */
    protected $_model;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_model = $this->getObject($config->model);
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
            'priority' => static::PRIORITY_LOW, // low priority so that rewardable runs first
            'model'    => 'com://site/rewardlabs.model.rewards',
        ));

        parent::_initialize($config);
    }

    /**
     * Hook to after add event
     *
     * @param KControllerContext $context
     *
     * @return void
     */
    protected function _afterAdd(KControllerContext $context)
    {
        $this->encode($context->result);
    }

    /**
     * Encode rebates
     *
     * @return void
     */
    public function encode($order)
    {
        // $accounting = $this->getObject('com://site/rewardlabs.accounting.journal');

        $items = $order->getOrderItems();

        foreach ($items as $item)
        {
            // Record rebates
            $data = array(
                'item'    => $item->id,
                'account' => $order->_account_number,
                'type'    => 'rebates',
                'points'  => $item->rebates * $item->quantity,
            );
            $reward = $this->_model->create($data);
            $reward->save();

            // Post rebates allocation to accounting system
            // $accounting->recordRebatesExpense($item->id, $item->rebates * $item->quantity);
        }
    }
}
