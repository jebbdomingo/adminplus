<?
/**
 * Nucleon Plus Admin
 *
 * @package     Nucleon Plus Admin
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
                        <?= helper('grid.sort', array('column' => '_user.name', 'title' => 'Name')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet">
                        <?= helper('grid.sort', array('column' => 'status', 'title' => 'Status')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet">
                        <?= helper('grid.sort', array('column' => 'id', 'title' => 'Account Number')); ?>
                    </th>
                    <th width="15%" data-hide="phone,tablet">
                        <?= translate('Sponsor') ?>
                    </th>
                    <th width="10%" data-hide="phone,tablet">
                        <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Created')); ?>
                    </th>
                    <th width="10%" data-hide="phone,tablet">
                        <?= helper('grid.sort', array('column' => 'modified_on', 'title' => 'Modified')); ?>
                    </th>
                </tr>
            </thead>
            <tbody <?= parameters()->sort == 'ordering' ? 'data-behavior="orderable"' : '' ?>>
                <? if (count($accounts)): ?>
                    <? foreach ($accounts as $account): ?>
                        <tr>
                            <td class="k-table-data--form">
                                <?= helper('grid.checkbox', array('entity' => $account)) ?>
                            </td>
                            <td class="k-table-data--toggle"></td>
                            <td class="k-table-data--ellipsis">
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($account->_name))); ?>" href="<?= route('view=account&id='.$account->id); ?>">
                                        <?= escape($account->_name); ?>
                                </a>
                            </td>
                            <td><?= $account->status ?></td>
                            <td><?= $account->id ?></td>
                            <td><?= ($account->sponsor_id) ? $account->sponsor_id : '-' ?></td>
                            <td class="k-table-data--nowrap"><?= helper('date.humanize', array('date' => $account->created_on)) ?></td>
                            <td class="k-table-data--nowrap"><?= helper('date.humanize', array('date' => $account->modified_on)) ?></td>
                        </tr>
                    <? endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="k-table-data--center">
                            <?= translate('No member') ?>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($accounts)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
