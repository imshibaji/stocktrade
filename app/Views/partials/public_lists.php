<?php if (!empty($publicLists)): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($publicLists as $list): ?>
    <a href="/screener/<?= (int) $list['id'] ?>" class="bg-surface rounded-xl p-5 border border-gray-700 hover:border-accent transition cursor-pointer block group">
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="text-white font-bold text-lg group-hover:text-accent transition"><?= esc($list['name']) ?></h3>
                <p class="text-gray-400 text-sm"><?= (int) $list['stock_count'] ?> stocks</p>
            </div>
            <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-300"><i class="fas fa-globe mr-1"></i>Public</span>
        </div>
        <p class="text-xs text-gray-500">Shared by <?= esc($list['owner_name'] ?? 'Member') ?> &middot; <?= esc(date('M j, Y', strtotime($list['created_at']))) ?></p>
        <p class="mt-3 text-xs text-accent"><i class="fas fa-arrow-right mr-1"></i>View list</p>
    </a>
    <?php endforeach; ?>
</div>
<?php else: ?>
<p class="text-center text-gray-500">No community lists yet.</p>
<?php endif; ?>
