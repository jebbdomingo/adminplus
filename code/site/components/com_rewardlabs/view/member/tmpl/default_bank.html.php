<div class="k-form-group">
    <label for="PrintOnCheckName"><?= translate('Name in cheque') ?></label>
    <input class="k-form-control" type="text" id="PrintOnCheckName" name="PrintOnCheckName" value="<?= $member->_account_check_name ?>" />
</div>
<div class="k-form-group">
    <label for="bank_name"><?= translate('Bank') ?></label>
    <?= helper('listbox.banks', array(
        'name'     => 'bank_name',
        'selected' => $member->_account_bank_name,
    )) ?>
</div>
<div class="k-form-group">
    <label for="bank_account_type"><?= translate('Account type') ?></label>
    <?= helper('listbox.bankAccountTypes', array(
        'name'     => 'bank_account_type',
        'selected' => $member->_account_bank_account_type,
    )) ?>
</div>
<div class="k-form-group">
    <label for="bank_account_number"><?= translate('Account number') ?></label>
    <input class="k-form-control" type="text" id="bank_account_number" name="bank_account_number" value="<?= $member->_account_bank_account_number ?>" />
</div>
<div class="k-form-group">
    <label for="bank_account_name"><?= translate('Account name') ?></label>
    <input class="k-form-control" type="text" id="bank_account_name" name="bank_account_name" value="<?= $member->_account_bank_account_name ?>" />
</div>
<div class="k-form-group">
    <label for="bank_account_branch"><?= translate('Branch of account') ?></label>
    <input class="k-form-control" type="text" id="bank_account_branch" name="bank_account_branch" value="<?= $member->_account_bank_account_branch ?>" />
</div>