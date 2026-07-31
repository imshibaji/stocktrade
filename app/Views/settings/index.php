<section class="max-w-2xl mx-auto py-4">
    <h1 class="text-3xl font-bold text-white mb-8">Account Settings</h1>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8 mb-6">
        <h2 class="text-white font-bold text-lg mb-6 flex items-center">
            <i class="fas fa-user-edit text-gold mr-2"></i>Edit Profile
        </h2>

        <?php if (session()->getFlashdata('errors') && !session()->getFlashdata('error')): ?>
        <div class="bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/settings/update-profile" method="post">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Full Name</label>
                <input type="text" name="name" value="<?= old('name', $user['name']) ?>" required
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 text-sm">Email Address</label>
                <input type="email" name="email" value="<?= old('email', $user['email']) ?>" required
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
            </div>
            <button type="submit" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg text-sm transition">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </form>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8 mb-6">
        <h2 class="text-white font-bold text-lg mb-6 flex items-center">
            <i class="fas fa-palette text-gold mr-2"></i>Appearance
        </h2>
        <label class="block text-gray-300 mb-3 text-sm">Theme</label>
        <div class="theme-segmented mb-2" data-theme-segment-wrap>
            <button type="button" data-theme-segment="day" onclick="setTheme('day')">
                <i class="fa-regular fa-sun icon-off"></i><i class="fa-solid fa-sun icon-on"></i> Day
            </button>
            <button type="button" data-theme-segment="system" onclick="setTheme('system')">
                <i class="fa-regular fa-circle icon-off"></i><i class="fa-solid fa-circle-half-stroke icon-on"></i> Auto
            </button>
            <button type="button" data-theme-segment="night" onclick="setTheme('night')">
                <i class="fa-regular fa-moon icon-off"></i><i class="fa-solid fa-moon icon-on"></i> Night
            </button>
        </div>
        <p class="text-gray-500 text-xs">Changes apply instantly and are saved to this browser.</p>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8 mb-6">
        <h2 class="text-white font-bold text-lg mb-6 flex items-center">
            <i class="fas fa-calculator text-gold mr-2"></i>Tax Settings
        </h2>

        <form action="/settings/update-tax" method="post">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">STCG Rate (%)</label>
                    <input type="number" name="stcg_rate" value="<?= old('stcg_rate', $user['stcg_rate'] ?? 15) ?>" required min="0" max="100" step="0.01"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">Short Term Capital Gains Tax (held &lt; 1 year)</p>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">LTCG Rate (%)</label>
                    <input type="number" name="ltcg_rate" value="<?= old('ltcg_rate', $user['ltcg_rate'] ?? 10) ?>" required min="0" max="100" step="0.01"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">Long Term Capital Gains Tax (held &gt; 1 year)</p>
                </div>
            </div>
            <button type="submit" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg text-sm transition">
                <i class="fas fa-save mr-2"></i>Save Tax Settings
            </button>
        </form>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8 mb-6">
        <h2 class="text-white font-bold text-lg mb-6 flex items-center">
            <i class="fas fa-receipt text-gold mr-2"></i>Brokerage &amp; Transaction Fees
        </h2>

        <form action="/settings/update-fees" method="post">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Brokerage (%)</label>
                    <input type="number" name="brokerage_pct" value="<?= old('brokerage_pct', $user['brokerage_pct'] ?? 0) ?>" min="0" max="100" step="0.0001"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">Per trade</p>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">STT (%)</label>
                    <input type="number" name="stt_pct" value="<?= old('stt_pct', $user['stt_pct'] ?? 0) ?>" min="0" max="100" step="0.0001"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">Securities Transaction Tax</p>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Exchange Charges (%)</label>
                    <input type="number" name="exchange_pct" value="<?= old('exchange_pct', $user['exchange_pct'] ?? 0) ?>" min="0" max="100" step="0.0001"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">NSE/BSE transaction charges</p>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">GST on Brokerage (%)</label>
                    <input type="number" name="gst_pct" value="<?= old('gst_pct', $user['gst_pct'] ?? 18) ?>" min="0" max="100" step="0.01"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">GST on (brokerage + exchange charges)</p>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Stamp Duty (%)</label>
                    <input type="number" name="stamp_duty_pct" value="<?= old('stamp_duty_pct', $user['stamp_duty_pct'] ?? 0) ?>" min="0" max="100" step="0.0001"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">SEBI Fees (%)</label>
                    <input type="number" name="sebi_fees" value="<?= old('sebi_fees', $user['sebi_fees'] ?? 0) ?>" min="0" max="100" step="0.000001"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <p class="text-gray-500 text-xs mt-1">SEBI regulatory fees per trade</p>
                </div>
            </div>
            <button type="submit" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg text-sm transition mt-4">
                <i class="fas fa-save mr-2"></i>Save Fee Settings
            </button>
        </form>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8 mb-6">
        <h2 class="text-white font-bold text-lg mb-6 flex items-center">
            <i class="fas fa-globe text-gold mr-2"></i>Base Currency
        </h2>

        <form action="/settings/update-base-currency" method="post">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Default Display Currency</label>
                <select name="base_currency" class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                    <option value="INR" <?= ($user['base_currency'] ?? 'INR') === 'INR' ? 'selected' : '' ?>>INR - Indian Rupee</option>
                    <option value="USD" <?= ($user['base_currency'] ?? 'INR') === 'USD' ? 'selected' : '' ?>>USD - US Dollar</option>
                    <option value="EUR" <?= ($user['base_currency'] ?? 'INR') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                    <option value="GBP" <?= ($user['base_currency'] ?? 'INR') === 'GBP' ? 'selected' : '' ?>>GBP - British Pound</option>
                    <option value="JPY" <?= ($user['base_currency'] ?? 'INR') === 'JPY' ? 'selected' : '' ?>>JPY - Japanese Yen</option>
                    <option value="AUD" <?= ($user['base_currency'] ?? 'INR') === 'AUD' ? 'selected' : '' ?>>AUD - Australian Dollar</option>
                    <option value="CAD" <?= ($user['base_currency'] ?? 'INR') === 'CAD' ? 'selected' : '' ?>>CAD - Canadian Dollar</option>
                    <option value="CHF" <?= ($user['base_currency'] ?? 'INR') === 'CHF' ? 'selected' : '' ?>>CHF - Swiss Franc</option>
                    <option value="CNY" <?= ($user['base_currency'] ?? 'INR') === 'CNY' ? 'selected' : '' ?>>CNY - Chinese Yuan</option>
                    <option value="SGD" <?= ($user['base_currency'] ?? 'INR') === 'SGD' ? 'selected' : '' ?>>SGD - Singapore Dollar</option>
                </select>
                <p class="text-gray-500 text-xs mt-1">All portfolio values and P/L will be displayed in this currency. Exchange rates fetched live from Yahoo Finance.</p>
            </div>
            <button type="submit" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg text-sm transition">
                <i class="fas fa-save mr-2"></i>Save Base Currency
            </button>
        </form>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8">
        <h2 class="text-white font-bold text-lg mb-6 flex items-center">
            <i class="fas fa-lock text-gold mr-2"></i>Change Password
        </h2>

        <form action="/settings/update-password" method="post">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Current Password</label>
                <input type="password" name="current_password" required
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
            </div>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">New Password</label>
                <input type="password" name="new_password" required minlength="6"
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none"
                    placeholder="Minimum 6 characters">
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 text-sm">Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="6"
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
            </div>
            <button type="submit" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg text-sm transition">
                <i class="fas fa-key mr-2"></i>Update Password
            </button>
        </form>
    </div>
</section>
