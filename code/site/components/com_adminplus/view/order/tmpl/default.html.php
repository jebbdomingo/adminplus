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

<?= helper('ui.load', array(
    'styles' => array('file' => 'admin'),
    'domain' => 'admin'
)); ?>

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

                    <!-- Container -->
                    <div class="k-container">

                        <div class="k-container__main">
                            <? if ($order->shipping_method == ComNucleonplusModelEntityOrder::SHIPPING_METHOD_XEND): ?>
                                <?= import('tracking_reference.html') ?>
                            <? endif ?>

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