<div class="k-sidebar-left k-js-sidebar-left">

    <div class="k-sidebar-item">
        <ul class="k-navigation">
            <li class="<?= parameters()->view === 'account' && parameters()->layout === 'default' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=&id=' . $account->id) ?>">
                    Account
                </a>
            </li>
            <li class="<?= parameters()->layout === 'rewards' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=rewards') ?>">
                    Rewards
                </a>
            </li>
            <li class="<?= parameters()->layout === 'referrals' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=referrals') ?>">
                    Referrals
                </a>
            </li>
            <li class="<?= parameters()->view === 'payouts' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=payouts') ?>">
                    Payouts
                </a>
            </li>
        </ul>
    </div>

</div>
