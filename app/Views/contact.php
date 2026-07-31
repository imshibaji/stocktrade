<section class="py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-white mb-4">Contact <span class="text-accent">Us</span></h1>
        <p class="text-gray-400 max-w-2xl mx-auto">Have questions or feedback? We'd love to hear from you. Reach out and we'll get back as soon as possible.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 max-w-5xl mx-auto">
        <div class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <div class="text-3xl text-accent mb-4"><i class="fas fa-envelope"></i></div>
            <h3 class="text-white font-semibold mb-2">Email Us</h3>
            <p class="text-gray-400 text-sm">support@stocktradetips.com</p>
        </div>
        <div class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <div class="text-3xl text-accent mb-4"><i class="fas fa-phone"></i></div>
            <h3 class="text-white font-semibold mb-2">Call Us</h3>
            <p class="text-gray-400 text-sm">+91 1800-123-4567</p>
        </div>
        <div class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <div class="text-3xl text-accent mb-4"><i class="fas fa-map-marker-alt"></i></div>
            <h3 class="text-white font-semibold mb-2">Visit Us</h3>
            <p class="text-gray-400 text-sm">Mumbai, Maharashtra, India</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto bg-surface rounded-xl p-8 border border-gray-700">
        <h2 class="text-2xl font-bold text-white mb-6">Send a Message</h2>

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/contact/send" method="post">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Name</label>
                <input type="text" name="name" value="<?= old('name') ?>" required
                    class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none"
                    placeholder="Your full name">
            </div>
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 text-sm">Email</label>
                <input type="email" name="email" value="<?= old('email') ?>" required
                    class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none"
                    placeholder="your@email.com">
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 text-sm">Message</label>
                <textarea name="message" rows="5" required
                    class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none"
                    placeholder="Tell us how we can help you..."><?= old('message') ?></textarea>
            </div>
            <button type="submit" class="w-full bg-accent hover:bg-accent-2 text-on-accent font-bold py-3 rounded-lg transition">
                Send Message
            </button>
        </form>
    </div>
</section>
