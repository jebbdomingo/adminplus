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

class ComRewardlabsControllerBehaviorItemsyncable extends KControllerBehaviorAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $actions = KObjectConfig::unbox($config->actions);

        foreach ($actions as $event => $action) {
            $this->addCommandCallback($event, "_{$action}");
        }
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
            'actions' => array(
                'after.add'    => 'add',
                'after.edit'   => 'update',
                'after.delete' => 'update',
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Sync data to QBO
     *
     * @param KControllerContextInterface $context
     * @throws Exception
     *
     * @return void
     */
    protected function _add(KControllerContextInterface $context)
    {
        $config = $this->getObject('com://site/rewardlabs.accounting.data');
        $entity = $context->result;

        // Data
        $item = array(
            'Type'              => 'Inventory',
            'Name'              => $entity->Name,
            'Description'       => $entity->Description,
            'UnitPrice'         => $entity->UnitPrice,
            'PurchaseCost'      => $entity->PurchaseCost,
            'TrackQtyOnHand'    => true,
            'QtyOnHand'         => $entity->QtyOnHand,
            'IncomeAccountRef'  => $config->ACCOUNT_SALES_INCOME,
            'ExpenseAccountRef' => $config->ACCOUNT_COGS,
            'AssetAccountRef'   => $config->ACCOUNT_INVENTORY_ASSET,
            'InvStartDate'      => new \DateTime('NOW'),
        );

        try {
            $id = $this->getObject('com://admin/qbsync.service.item')->create($item);
            $entity->ItemRef = $id;
            $entity->save();
        } catch(Exception $e) {
            throw new KControllerExceptionActionFailed($e->getMessage());
        }
    }

    /**
     * Sync data to QBO
     *
     * @param KControllerContextInterface $context
     * @throws Exception
     *
     * @return void
     */
    protected function _update(KControllerContextInterface $context)
    {
        $entity  = $context->result;
        $service = $this->getObject('com://admin/qbsync.service.item');

        if ($service->get($entity->ItemRef) !== false)
        {
            // Data
            $data = array(
                'Name'        => $entity->Name,
                'Description' => $entity->Description,
                'UnitPrice'   => $entity->UnitPrice,
                'ItemRef'     => $entity->ItemRef,
            );

            try {
                $service->update($data);
            } catch(Exception $e) {
                throw new KControllerExceptionActionFailed($e->getMessage());
            }
        }
        else $this->_add($context);
    }
}
