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

        <table class="k-js-responsive-table">
            <thead>
            <tr>
                <th width="1%" class="k-table-data--form">
                    <?= helper('grid.checkall')?>
                </th>
                <th width="1%">
                    <?= helper('grid.sort', array('column' => 'id', 'title' => 'Payout #')); ?>
                </th>
                <th width="2%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'status', 'title' => 'Status')); ?>
                </th>
                <th width="2%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'payout_method', 'title' => 'Encashment method')); ?>
                </th>
                <th width="10%">
                    <?= helper('grid.sort', array('column' => 'name', 'title' => 'Member')); ?>
                </th>
                <th width="5%" data-hide="phone,tablet">
                    <?= helper('grid.sort', array('column' => 'account_number', 'title' => 'Account #')); ?>
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
                <? if (count($payouts)): ?>
                    <? foreach ($payouts as $payout): ?>
                        <tr>
                            <td class="k-table-data--form">
                                <?= helper('grid.checkbox', array('entity' => $payout)) ?>
                            </td>
                            <td><?= $payout->id ?></td>
                            <td><?= $payout->status ?></td>
                            <td><?= $payout->payout_method ?></td>
                            <td class="k-table-data--ellipsis">
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($payout->name))); ?>" href="<?= route('view=account&id='.$payout->account_id); ?>">
                                        <?= escape($payout->name); ?>
                                </a>
                            </td>
                            <td>
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($payout->account_number))); ?>" href="<?= route('view=account&id='.$payout->account_id); ?>">
                                        <?= escape($payout->account_number); ?>
                                </a>
                            </td>
                            <td class="k-table-data--nowrap"><?= helper('date.humanize', array('date' => $payout->created_on)) ?></td>
                            <td class="k-table-data--right">&#8369;<?= number_format($payout->amount, 2) ?></td>
                        </tr>
                    <? endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="k-table-data--center">
                            <?= translate('No payout') ?>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($payouts)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
