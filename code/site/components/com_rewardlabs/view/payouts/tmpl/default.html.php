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

<?= helper('behavior.tooltip') ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Payouts" mobile>

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component -->
            <div class="k-component-wrapper">

                <!-- Form -->
                <form class="k-component k-js-component k-js-grid-controller" action="" method="get">

                    <!-- Scopebar -->
                    <?= import('default_scopebar.html'); ?>

                    <!-- Reward summary -->
                    <div class="k-alert k-alert--info k-no-margin">
                        <span class="k-icon-info" aria-hidden="true"></span> <strong>Total</strong>
                        <div class="k-table">
                            <table class="k-js-responsive-table">
                                <tbody>
                                    <tr>
                                        <td>Rebates</td>
                                        <td><strong><?= $rebates ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Direct Referrals</td>
                                        <td><strong><?= $direct_referrals ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Inirect Referrals</td>
                                        <td><strong><?= $indirect_referrals ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table -->
                    <?= import('default_table.html'); ?>

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div>

    </div>
</div>