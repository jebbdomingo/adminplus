<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComRewardlabsModelCarts extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('interface', 'string')
            ->insert('customer', 'string')
            ->insert('cart_id', 'int')
        ;
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->interface) {
            $query->where('tbl.interface = :interface')->bind(['interface' => $state->interface]);
        }

        if ($state->customer) {
            $query->where('tbl.customer = :customer')->bind(['customer' => $state->customer]);
        }

        if ($state->cart_id) {
            $query->where('tbl.cart_id = :cart_id')->bind(['cart_id' => $state->cart_id]);
        }
    }

    /**
     * Get the total amount of this cart
     *
     * @return decimal
     */
    public function getAmount()
    {
        $state = $this->getState();


        $table = $this->getObject('com://site/rewardlabs.database.table.carts');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_carts AS tbl')
            ->columns('tbl.rewardlabs_cart_id, SUM(_item.UnitPrice * _cart_items.quantity) AS total')
            ->join(array('_cart_items' => 'rewardlabs_cartitems'), '_cart_items.cart_id = tbl.rewardlabs_cart_id', 'INNER')
            ->join(array('_item' => 'qbsync_items'), '_cart_items.row = _item.ItemRef', 'INNER')
            ->group('tbl.customer')
        ;

        $this->_buildQueryWhere($query);

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get the total weight of this order
     *
     * @return integer
     */
    public function getWeight($itemType = null)
    {
        $state = $this->getState();

        $table = $this->getObject('com://site/rewardlabs.database.table.items');
        $query = $this->getObject('database.query.select')
            ->table('rewardlabs_cart_items AS tbl')
            ->columns('tbl.cart_item_id, SUM(_item.weight * tbl.quantity) AS total')
            ->join(array('_item' => 'qbsync_items'), 'tbl.row = _item.ItemRef', 'INNER')
            ->group('tbl.cart_id')
        ;

        if ($itemType) {
            $query->where('_item.shipping_type = :type')->bind(['type' => $itemType]);
        }

        $this->_buildQueryWhere($query);

        $entities = $table->select($query);

        return (int) $entities->total;
    }
}
