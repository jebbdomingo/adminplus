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

class ComAdminplusControllerBehaviorRebatable extends KControllerBehaviorAbstract
{
    /**
     * Referral bonus controller.
     *
     * @param KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Accounting Service
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $this->getObject($config->controller);
        $this->_accounting = $this->getObject($config->accounting);
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
            'accounting' => 'com://admin/nucleonplus.accounting.service.transfer',
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
        $items = $order->getOrderItems();

        foreach ($items as $item)
        {
            // Record rebates
            $data = array(
                'item'    => $item->id,
                'account' => $order->_account_number,
                'type'    => 'rebates',
                'points'  => $item->rebates,
            );
            $this->_controller->add($data);

            // Post rebates allocation to accounting system
            $this->_accounting->allocateRebates($item->ItemRef, $item->rebates);
        }
    }
}
