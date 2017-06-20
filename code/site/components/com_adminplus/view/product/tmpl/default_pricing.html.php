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

<div class="k-well">
    <div class="k-sidebar-item__header">
        <?= translate('Unit price') ?>
    </div>

    <div class="k-sidebar-item__content">
        <div class="k-heading" style="color: #27ae60">
            &#8369; <span id="unit-price-label"><?= number_format($product->UnitPrice, 2) ?></span>
        </div>
    </div>

</div>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Pricing') ?>
    </div>

    <div class="k-form-block__content k-js-form--pricing">
        <div class="k-form-group">
            <label for="PurchaseCost"><?= translate('Cost') ?></label>
            <input class="k-form-control" type="text" id="PurchaseCost" name="PurchaseCost" value="<?= $product->PurchaseCost ?>" />
        </div>
        <div class="k-form-group">
            <label for="profit"><?= translate('Profit') ?></label>
            <input class="k-form-control" type="text" id="profit" name="profit" value="<?= $product->profit ?>" />
        </div>
        <div class="k-form-group">
            <label for="charges"><?= translate('System fee') ?></label>
            <input class="k-form-control" type="text" id="charges" name="charges" value="<?= $product->charges ?>" />
        </div>
        <div class="k-form-group">
            <label for="drpv"><?= translate('Direct referral') ?></label>
            <input class="k-form-control" type="text" id="drpv" name="drpv" value="<?= $product->drpv ?>" />
        </div>
        <div class="k-form-group">
            <label for="irpv"><?= translate('Indirect referral') ?></label>
            <input class="k-form-control" type="text" id="irpv" name="irpv" value="<?= $product->irpv ?>" />
        </div>
        <div class="k-form-group">
            <label for="rebates"><?= translate('Rebates') ?></label>
            <input class="k-form-control" type="text" id="rebates" name="rebates" value="<?= $product->rebates ?>" />
        </div>
        <div class="k-form-group">
            <label for="stockist"><?= translate('Stockist') ?></label>
            <input class="k-form-control" type="text" id="stockist" name="stockist" value="<?= $product->stockist ?>" />
        </div>
    </div>

</fieldset>

<script>
    kQuery(document).ready(function($) {

        $('.k-js-form--pricing').on('change, keyup', function(){
            var total = 0;

            $('.k-js-form--pricing :input').each(function(){
                if (this.value) {
                    total += parseFloat(this.value);
                }
            });

            $('#unit-price-label').text(total);
        });

    });
</script>
