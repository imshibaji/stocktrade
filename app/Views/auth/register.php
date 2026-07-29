<section class="max-w-md mx-auto py-12">
    <div class="bg-navy2 rounded-xl p-8 border border-gray-700">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Create Account</h1>
        <p class="text-gray-400 text-center mb-8">Join StockTrade Tips and start investing smarter</p>

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/register" method="post">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Full Name</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="text" name="name" value="<?= old('name') ?>" required
                        class="w-full bg-navy border border-gray-600 rounded-lg pl-10 pr-4 py-3 text-white focus:border-gold focus:outline-none"
                        placeholder="Your full name">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Email Address</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="email" name="email" value="<?= old('email') ?>" required
                        class="w-full bg-navy border border-gray-600 rounded-lg pl-10 pr-4 py-3 text-white focus:border-gold focus:outline-none"
                        placeholder="your@email.com">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 text-sm">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="password" name="password" required
                        class="w-full bg-navy border border-gray-600 rounded-lg pl-10 pr-4 py-3 text-white focus:border-gold focus:outline-none"
                        placeholder="Minimum 6 characters">
                </div>
            </div>
            <button type="submit" class="w-full bg-gold hover:bg-gold2 text-navy font-bold py-3 rounded-lg transition mb-4">
                Create Account
            </button>
            <p class="text-center text-gray-400 text-sm">
                Already have an account?
                <a href="/login" class="text-gold hover:text-gold2 transition">Login here</a>
            </p>
        </form>
    </div>
</section>
