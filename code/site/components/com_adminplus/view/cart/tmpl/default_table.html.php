<?
/**
* Nucleon Plus - Admin
*
* @package     Nucleon Plus - Admin
* @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
* @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
*/

defined('KOOWA') or die; ?>

<div class="k-table-container">
    <div class="k-table">
        <table class="k-js-fixed-table-header k-js-responsive-table">
            <thead>
                <tr>
                    <th width="1%" class="k-table-data--form">&nbsp;</th>
                    <th class="k-sort-desc">Item</th>
                    <th width="15%" class="k-table-data--right">Price</th>
                    <th width="15%" class="k-table-data--right">Quantity</th>
                    <th width="5%" data-hide="phone,tablet" class="k-table-data--right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($cart->getItems() as $item): ?>
                    <tr>
                        <td class="k-table-data--form">
                            <?= helper('grid.checkbox', array('entity' => $item)) ?>
                        </td>
                        <td class="k-table-data--ellipsis">
                            <a href="<?= route("view=product&id={$item->_item_id}") ?>"><?= $item->_item_name ?></a>
                        </td>
                        <td class="k-table-data--right">&#8369; <?= number_format($item->_item_price, 2) ?></td>
                        <td class="k-table-data--ellipsis">
                          <input type="text" class="form-control input-sm" size="10" name="quantity[<?= $item->id ?>]" value="<?= $item->quantity ?>">
                        </td>
                        <td class="k-table-data--nowrap k-table-data--right">
                            &#8369; <?= number_format($item->_item_price * $item->quantity, 2) ?>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>
        </table>
    </div>

    <button type="button" class="k-button k-button--primary k-button--block k-js-cart-form--update">Update</button>
</div>
