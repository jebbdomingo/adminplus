<?
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
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

            <!-- Title -->
            <ktml:toolbar type="titlebar" title="<?= $account->_name ?>">

            <!-- Component -->
            <div class="k-component-wrapper">
                <div class="k-table-container">
                    <div class="k-table">
                        <table class="k-js-responsive-table">
                            <thead>
                                <tr>
                                    <th width="20%">Name</th>
                                    <th>Account No.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach ($account->getDirectReferrals() as $referral): ?>
                                    <tr>
                                        <td><?= object('user.provider')->load($referral->user_id)->getName() ?></td>
                                        <td><?= $referral->id ?></td>
                                    </tr>
                                <? endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>