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

class ComRewardlabsControllerBehaviorChargeable extends KControllerBehaviorAbstract
{
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
     * Hook to after sync event
     *
     * @param KControllerContext $context
     *
     * @return void
     */
    protected function _afterSync(KControllerContext $context)
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
        $accounting = $this->getObject('com://site/rewardlabs.accounting.journal');

        // Post charges allocation to accounting system
        foreach ($order->getOrderItems() as $item) {
            $accounting->recordChargesExpense($item->id, $item->charges * $item->quantity);
        }
    }
}
