<?
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die; ?>

<?= helper('ui.load', array(
    'domain' => 'admin'
)); ?>

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
            <ktml:toolbar type="titlebar" title="Admin Plus" mobile>

            <!-- Component -->
            <div class="k-component-wrapper">

                <div class="k-table-container">

                    <div class="k-table">

                        <table class="k-js-responsive-table">
                            <tbody>
                                <tr>
                                    <td>Rebates</td>
                                    <td class="k-table-data--right"><?= number_format($total_rebates, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Direct referral bonus</td>
                                    <td class="k-table-data--right"><?= number_format($total_direct_referrals, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Patronage bonus</td>
                                    <td class="k-table-data--right"><?= number_format($total_patronages, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Unilevel bonus</td>
                                    <td class="k-table-data--right"><?= number_format($total_referral_bonus, 2) ?></td>
                                </tr>
                                <tr>
                                    <td><span class="k-table__item--state k-table__item--state-published">Total</span></td>
                                    <td class="k-table-data--right"><span class="k-table__item--state k-table__item--state-published"><?= number_format($total_bonus, 2) ?></span></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>