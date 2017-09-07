<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
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

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="<?= $account->_name ?>" mobile>

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component -->
            <div class="k-component-wrapper">

                <div class="k-table-container">

                    <div class="k-table">

                        <table>
                            <tbody>
                                <tr>
                                    <td width="20%">Name</td>
                                    <td><?= $account->_name ?></td>
                                </tr>
                                <tr>
                                    <td>Account Number</td>
                                    <td><?= $account->id ?></td>
                                </tr>
                                <tr>
                                    <td>Sponsor ID</td>
                                    <td><?= $account->sponsor_id ?></td>
                                </tr>
                                <tr>
                                    <td>Member Since</td>
                                    <td><?= helper('date.humanize', array('date' => $account->created_on)) ?></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>