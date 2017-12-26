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
                <th width="1%">
                    <?= helper('grid.sort', array('column' => 'id', 'title' => 'Order #')); ?>
                </th>
                <th width="7%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'order_status', 'title' => 'Order Status')); ?>
                </th>
                <th width="2%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'SalesReceiptRef', 'title' => 'Sync Ref#')); ?>
                </th>
                <th width="10%">
                    <?= helper('grid.sort', array('column' => 'name', 'title' => 'Member')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'account', 'title' => 'Account #')); ?>
                </th>
                <th width="5%">
                    <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                </th>
                <th width="10%" class="k-table-data--right">
                    <?= helper('grid.sort', array('column' => 'total', 'title' => 'Amount')); ?>
                </th>
            </tr>
            </thead>
            <tbody <?= parameters()->sort == 'ordering' ? 'data-behavior="orderable"' : '' ?>>
                <? if (count($orders)): ?>
                    <? foreach ($orders as $order): ?>
                        <tr>
                            <td class="k-table-data--form">
                                <?= helper('grid.checkbox', array('entity' => $order)) ?>
                            </td>
                            <td>
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($order->id))); ?>" href="<?= route('view=order&id='.$order->id); ?>">
                                        <?= $order->id ?>
                                </a>
                            </td>
                            <td><?= helper('labels.orderStatus', array('value' => $order->order_status)) ?></td>
                            <td><?= $order->SalesReceiptRef ?></td>
                            <td class="k-table-data--ellipsis">
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($order->name))); ?>" href="<?= route('view=account&id='.$order->account_id); ?>">
                                        <?= escape($order->name); ?>
                                </a>
                            </td>
                            <td>
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($order->account_number))); ?>" href="<?= route('view=account&id='.$order->account); ?>">
                                        <?= escape($order->account); ?>
                                </a>
                            </td>
                            <td class="k-table-data--nowrap"><?= helper('date.humanize', array('date' => $order->created_on)) ?></td>
                            <td class="k-table-data--right">&#8369;<?= number_format($order->total, 2) ?></td>
                        </tr>
                    <? endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="k-table-data--center">
                            <?= translate('No order') ?>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($orders)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
