<?php if (isset($pager) && $pager->getTotal() > 0 && $pager->getPageCount() > 1): ?>
<div class="mt-6 flex flex-wrap items-center justify-between gap-3">
    <div class="text-sm text-gray-400">
        <?php
            $page     = $pager->getCurrentPage();
            $perPage  = $pager->getPerPage();
            $total    = $pager->getTotal();
            $first    = ($page - 1) * $perPage + 1;
            $last     = min($page * $perPage, $total);
        ?>
        Showing <?= $first ?>–<?= $last ?> of <?= $total ?> <?= esc($label ?? 'items') ?>
    </div>
    <nav class="flex items-center gap-1" aria-label="Pagination">
        <?= $pager->links('default', 'default_full') ?>
    </nav>
</div>
<?php endif; ?>
