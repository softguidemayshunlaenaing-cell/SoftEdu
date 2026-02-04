<?php
$menu = $menu ?? [];
$currentPage = $currentPage ?? '';
$sidebarWrapperClass = $sidebarWrapperClass ?? 'col-md-3 col-lg-2 sidebar';
?>
<div class="<?= htmlspecialchars($sidebarWrapperClass) ?>">
    <ul class="nav flex-column">
        <?php foreach ($menu as $item): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === ($item['key'] ?? '') ? 'active' : '' ?>"
                    href="<?= htmlspecialchars($item['href'] ?? '#') ?>">
                    <i class="fas fa-<?= htmlspecialchars($item['icon'] ?? 'circle') ?> me-2"></i>
                    <?= htmlspecialchars($item['label'] ?? '') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
