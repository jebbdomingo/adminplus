<?
$disabled = $order->order_status <> ComRewardlabsModelEntityOrder::STATUS_PROCESSING ? 'disabled="disabled"' : null;
?>
<fieldset class="k-form-block">
    <div class="k-form-block__content">
        <div class="k-form-group">
            <label for="ItemRef"><?= translate('Tracking reference #') ?></label>
            <input required <?= $disabled ?> class="k-form-control" type="text" id="tracking_reference" name="tracking_reference" value="<?= $order->tracking_reference ?>" />
        </div>
    </div>
</fieldset>