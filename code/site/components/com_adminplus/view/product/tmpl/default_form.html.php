<fieldset>
    <div class="k-form-group">
        <label for="Name"><?= translate('Product name') ?></label>
        <input class="k-form-control" type="text" id="Name" name="Name" value="<?= $product->Name ?>" />
    </div>
    <div class="k-form-group">
        <label for="status"><?= translate('Status') ?></label>
        <?= helper('listbox.productStatus', array(
            'name'     => 'status',
            'selected' => $product->status,
        )) ?>
    </div>
    <div class="k-form-group">
        <label for="weight"><?= translate('Weight') ?></label>
        <input class="k-form-control" type="text" id="weight" name="weight" value="<?= $product->weight ?>" />
    </div>
    <div class="k-form-group">
        <label for="QtyOnHand"><?= translate('Quantity') ?></label>
        <input class="k-form-control" type="text" id="QtyOnHand" name="QtyOnHand" value="<?= $product->QtyOnHand ?>" <?= $product->isNew() ? null : 'disabled="disabled"' ?> />
    </div>
    <div class="k-form-group">
        <label for="Description"><?= translate('Intro text') ?></label>
        <input class="k-form-control" type="text" id="Description" name="Description" value="<?= $product->Description ?>" />
    </div>
    <div class="k-form-group">
        <label for="fulltext"><?= translate('Full text') ?></label>
        <?= helper('editor.display', array(
            'name' => 'fulltext',
            'text' => $product->fulltext,
        )) ?>
    </div>
</fieldset>
