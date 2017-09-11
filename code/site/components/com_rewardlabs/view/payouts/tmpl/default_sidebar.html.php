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

<div class="k-sidebar-left k-js-sidebar-left">

    <div class="k-sidebar-item">
        <ul class="k-navigation">
            <li class="<?= parameters()->view === 'accounts' && parameters()->layout === 'default' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=accounts') ?>"><?= translate('Accounts') ?></a>
            </li>
            <li class="<?= parameters()->view === 'products' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=products') ?>"><?= translate('Products') ?></a>
            </li>
            <li class="<?= parameters()->view === 'orders' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=orders') ?>"><?= translate('Orders') ?></a>
            </li>
            <li class="<?= parameters()->view === 'payouts' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=payouts') ?>"><?= translate('Payouts') ?></a>
            </li>
            <? if (object('com://site/rewardlabs.controller.config')->canManage()): ?>
                <li class="<?= parameters()->view === 'configs' ? 'k-is-active' : null ?>">
                    <a href="<?= route('view=configs') ?>"><?= translate('Configs') ?></a>
                </li>
            <? endif ?>
        </ul>
    </div>

    <div class="k-sidebar-item k-js-sidebar-toggle-item">
        <div class="k-sidebar-item__header">
            <?= translate('Quick Navigation') ?>
        </div>
        <ul class="k-list">
            <li class="<?= empty(parameters()->created_by) && (parameters()->sort === 'created_on' && parameters()->direction === 'desc') ? 'k-is-active' : ''; ?>">
                <?
                $url = 'view=payouts&';
                $url .= parameters()->sort === 'created_on' && parameters()->direction === 'desc' ? 'sort=&direction=&created_by=' : 'sort=created_on&direction=desc&created_by='
                ?>
                <a href="<?= route($url) ?>">
                    <span class="k-icon-clock" aria-hidden="true"></span>
                    <?= translate('Recently created') ?>
                </a>
            </li>
        </ul>
    </div>

</div>
