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

<?= helper('ui.load', array(
    'domain' => 'admin'
)); ?>

<?= helper('com://admin/cart.behavior.addable'); ?>
<?= helper('com://admin/cart.behavior.updatable'); ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Nucleon Plus" mobile>

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller" action="" method="post">

                    <input type="hidden" name="account_id" value="<?= $account->id ?>" />
                    <input type="hidden" name="cart_id" value="<?= $cart->id ?>" />

                    <!-- Container -->
                    <div class="k-container">

                        <div class="k-container__main">
                            <fieldset class="k-form-block">
                                <div class="k-form-block__content">
                                    <div class="k-form-group">
                                        <label for="ItemRef"><?= translate('Item') ?></label>
                                        <?= helper('listbox.productList', array(
                                            'name'    => 'ItemRef',
                                            'attribs' => array(
                                                'style' => 'width: 100%',
                                                'size'  => false,
                                            )
                                        )) ?>
                                    </div>

                                    <div class="k-input-group">
                                        <label for="form_quantity" class="k-input-group__addon">Quantity</label>
                                        <input class="k-form-control" type="text" id="form_quantity" name="form_quantity" value="1" />
                                        <div class="k-input-group__button">
                                            <button type="button" class="k-button k-button--primary k-js-cart-form--add">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="k-form-block">
                                <div class="k-form-block__header">
                                    <?= translate('Items') ?>
                                </div>
                                <div class="k-form-block__content">
                                    <!-- Table -->
                                    <?= import('default_table.html'); ?>
                                </div>
                            </fieldset>
                        </div>

                        <div class="k-container__sub">
                            <!-- Sidebar -->
                            <?= import('default_sidebar.html'); ?>
                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>