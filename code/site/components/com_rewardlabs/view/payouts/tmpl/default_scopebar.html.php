<?
/**
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */
defined('KOOWA') or die; ?>

<div class="k-js-filters k-dynamic-content-holder">
    <div data-filter data-title="<?= translate('Member'); ?>"
         data-count="<?= (!is_null(parameters()->account_id)) ? 1 : 0 ?>"
    >
        <?= helper('listbox.users', array(
            'name'     => 'account_id',
            'select2'  => true,
            'selected' => parameters()->account_id,
            'deselect' => true,
            'prompt'   => '- Select -',
        )) ?>
    </div>
    <div data-filter data-title="<?= translate('Status'); ?>"
         data-count="<?= (!is_null(parameters()->status)) ? 1 : 0 ?>"
    >
        <?= helper('listbox.payoutStatus', array(
            'name'     => 'status',
            'select2'  => true,
            'selected' => parameters()->status,
            'deselect' => true,
            'prompt'   => '- Select -',
        )) ?>
    </div>
</div>

<!-- Scopebar -->
<div class="k-scopebar k-js-scopebar">

    <!-- Scopebar filters -->
    <div class="k-scopebar__item k-scopebar__item--filters">

        <!-- Filters wrapper -->
        <div class="k-scopebar__filters-content">

            <!-- Filters -->
            <div class="k-scopebar__filters k-js-filter-container">

                <!-- Filter -->
                <div style="display: none;" class="k-scopebar__item--filter k-scopebar-dropdown k-js-filter-prototype k-js-dropdown">
                    <button type="button" class="k-scopebar-dropdown__button k-js-dropdown-button">
                        <span class="k-scopebar__item--filter__title k-js-dropdown-title"></span>
                        <span class="k-scopebar__item--filter__icon k-icon-chevron-bottom" aria-hidden="true"></span>
                        <span class="k-scopebar__item-label k-js-dropdown-label"></span>
                    </button>
                    <div class="k-scopebar-dropdown__body k-js-dropdown-body">
                        <div class="k-scopebar-dropdown__body__buttons">
                            <button type="button" class="k-button k-button--default k-js-clear-filter"><?= translate('Clear') ?></button>
                            <button type="button" class="k-button k-button--primary k-js-apply-filter"><?= translate('Apply filter') ?></button>
                        </div>
                    </div>
                </div>

            </div><!-- .k-scopebar__filters -->

        </div><!-- .k-scopebar__filters-content -->

    </div><!-- .k-scopebar__item--filters -->

    <!-- Search -->
    <div class="k-scopebar__item k-scopebar__item--search">
        <?= helper('grid.search', array('submit_on_clear' => true)) ?>
    </div><!-- .k-scopebar__item--search -->

</div><!-- .k-scopebar -->
