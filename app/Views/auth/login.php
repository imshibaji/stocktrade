<section class="max-w-md mx-auto py-12">
    <div class="bg-surface rounded-xl p-8 border border-gray-700">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Welcome Back</h1>
        <p class="text-gray-400 text-center mb-8">Login to your StockTrade Tips account</p>

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/login" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect" value="<?= esc($redirect ?? '') ?>">
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Email Address</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="email" name="email" value="<?= old('email') ?>" required
                        class="w-full bg-page border border-gray-600 rounded-lg pl-10 pr-4 py-3 text-white focus:border-accent focus:outline-none"
                        placeholder="your@email.com">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 text-sm">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="password" name="password" required
                        class="w-full bg-page border border-gray-600 rounded-lg pl-10 pr-4 py-3 text-white focus:border-accent focus:outline-none"
                        placeholder="Your password">
                </div>
            </div>
            <button type="submit" class="w-full bg-accent hover:bg-accent-2 text-on-accent font-bold py-3 rounded-lg transition mb-4">
                Login
            </button>
            <p class="text-center text-gray-400 text-sm">
                Don't have an account?
                <a href="/register" class="text-accent hover:text-accent-2 transition">Register here</a>
            </p>
        </form>
    </div>
</section>
