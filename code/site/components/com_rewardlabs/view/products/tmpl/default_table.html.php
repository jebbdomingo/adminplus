<?
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

defined('KOOWA') or die; ?>

<div class="k-table-container">
    <div class="k-table">

        <table class="k-js-responsive-table">
            <thead>
            <tr>
                <th width="1%" class="k-table-data--form">
                    <?= helper('grid.checkall')?>
                </th>
                <th width="1%" class="k-table-data--toggle" data-toggle="true"></th>
                <th>
                    <?= helper('grid.sort', array('column' => 'Name', 'title' => 'Name')); ?>
                </th>
                <th width="10%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'status', 'title' => 'Status')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'shipping_type', 'title' => 'Shipping type')); ?>
                </th>
                <th width="2%" data-hide="phone,tablet" class="k-table-data--right">
                    <?= helper('grid.sort', array('column' => 'QtyOnHand', 'title' => 'Qty.')); ?>
                </th>
                <th width="2%" data-hide="phone,tablet" class="k-table-data--right">
                    <?= helper('grid.sort', array('column' => 'PurchaseCost', 'title' => 'Cost')); ?>
                </th>
                <th width="2%" data-hide="phone,tablet" class="k-table-data--right">
                    <?= helper('grid.sort', array('column' => 'UnitPrice', 'title' => 'Price')); ?>
                </th>
            </tr>
            </thead>
            <tbody <?= parameters()->sort == 'ordering' ? 'data-behavior="orderable"' : '' ?>>
                <? if (count($products)): ?>
                    <? foreach ($products as $product): ?>
                        <tr>
                            <td class="k-table-data--form">
                                <?= helper('grid.checkbox', array('entity' => $product)) ?>
                            </td>
                            <td class="k-table-data--toggle"></td>
                            <td class="k-table-data--ellipsis">
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($product->Name))); ?>" href="<?= route('view=product&id='.$product->id); ?>">
                                        <?= escape($product->Name); ?>
                                </a>
                            </td>
                            <td><?= helper('labels.productStatus', array('value' => $product->status)) ?></td>
                            <td><?= $product->shipping_type ?></td>
                            <td class="k-table-data--right"><?= $product->QtyOnHand ?></td>
                            <td class="k-table-data--right">&#8369;<?= number_format($product->PurchaseCost, 2) ?></td>
                            <td class="k-table-data--right">&#8369;<?= number_format($product->UnitPrice, 2) ?></td>
                        </tr>
                    <? endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="k-table-data--center">
                            <?= translate('No product') ?>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($products)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
