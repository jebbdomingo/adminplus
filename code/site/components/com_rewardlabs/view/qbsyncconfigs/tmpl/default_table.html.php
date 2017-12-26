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
                    <th width="1%">
                        <?= helper('grid.sort', array('column' => 'id', 'title' => 'ID')); ?>
                    </th>
                    <th width="1%" class="k-table-data--toggle" data-toggle="true"></th>
                    <th>
                        <?= helper('grid.sort', array('column' => 'item', 'title' => 'Item')); ?>
                    </th>
                </tr>
            </thead>
            <tbody <?= parameters()->sort == 'ordering' ? 'data-behavior="orderable"' : '' ?>>
                <? if (count($qbsyncconfigs)): ?>
                    <? foreach ($qbsyncconfigs as $config): ?>
                        <tr>
                            <td><?= $config->id ?></td>
                            <td class="k-table-data--toggle"></td>
                            <td>
                                <a data-k-tooltip='{"container":".k-ui-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View {title}', array('title' => escape($config->item))); ?>" href="<?= route('view=qbsyncconfig&id='.$config->id); ?>">
                                        <?= escape($config->item); ?>
                                </a>
                            </td>
                        </tr>
                    <? endforeach; ?>
                <? else: ?>
                    <tr>
                        <td colspan="3" class="k-table-data--center">
                            <?= translate('No config(s)') ?>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($qbsyncconfigs)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
