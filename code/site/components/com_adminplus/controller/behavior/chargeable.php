<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComAdminplusControllerBehaviorChargeable extends KControllerBehaviorAbstract
{
    /**
     * Referral bonus controller.
     *
     * @param KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $this->getObject($config->controller);
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
            'priority'   => static::PRIORITY_LOW, // low priority so that rewardable runs first
            'controller' => 'com://admin/nucleonplus.controller.rewards',
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
        $accounting = $this->getObject('com://admin/nucleonplus.accounting.service.journal');

        // Post charges allocation to accounting system
        foreach ($order->getOrderItems() as $item) {
            $accounting->recordChargesExpense($item->id, $item->charges);
        }
    }
}
