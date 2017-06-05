<? 
/**
 * Nucleon Plus Admin
 *
 * @package     Nucleon Plus Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die; ?>

<?
$customer = $order->getCustomer();
?>

<div class="k-well">
    <div class="k-sidebar-item__header">
        <?= translate('Total') ?>
    </div>

    <div class="k-sidebar-item__content">
        <div class="k-heading" style="color: #27ae60">
            &#8369; <?= number_format($order->total, 2) ?>
        </div>
        <dl>
            <dt><?= translate('Status') ?>:</dt>
            <dd><?= helper('labels.orderStatus', array('value' => $order->order_status)) ?></dd>
        </dl>
    </div>

</div>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Customer details') ?>
    </div>

    <div class="k-form-block__content">

        <div class="k-table-container">

            <div class="k-table">

                <table>
                    <tbody>
                        <tr>
                            <td width="30%">Name</td>
                            <td width="70%"><?= $customer->_name ?></td>
                        </tr>
                        <tr>
                            <td width="30%">Account #</td>
                            <td width="70%"><?= $customer->account_number ?></td>
                        </tr>
                        <tr>
                            <td width="30%">Member since</td>
                            <td width="70%">
                                <span class="k-icon-calendar" aria-hidden="true"></span>
                                <?= helper('date.humanize', array('date' => $customer->created_on)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%">Sponsor</td>
                            <td width="70%"><?= $customer->sponsor_id ? $customer->sponsor_id : '---' ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>

    </div>
</fieldset>
