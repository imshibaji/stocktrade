    </main>

    <?php if (!is_logged_in()): ?>
    <footer class="bg-surface border-t border-gray-700 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-accent font-bold text-lg mb-3"><?= esc(site_name()) ?></h3>
                    <p class="text-gray-400 text-sm">Your trusted platform for stock analysis, predictions, and investment tracking.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/" class="hover:text-accent transition">Home</a></li>
                        <li><a href="/about" class="hover:text-accent transition">About Us</a></li>
                        <li><a href="/contact" class="hover:text-accent transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Features</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Stock Analysis</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Future Predictions</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Portfolio Tracking</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Tax Calculations</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Disclaimer</h4>
                    <p class="text-gray-500 text-xs">Stock market investments are subject to market risks. Past performance is not indicative of future results. Predictions are AI-generated and should not be considered as financial advice.</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-500 text-sm">
                &copy; <?= date('Y') ?> <?= esc(site_name()) ?>. All rights reserved. Built with CodeIgniter 4.
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>
    <footer class="bg-surface border-t border-gray-700 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-500 text-xs">
            &copy; <?= date('Y') ?> <?= esc(site_name()) ?>. Market data powered by Yahoo Finance. Not financial advice.
        </div>
    </footer>
    <?php endif; ?>

</body>
</html>
