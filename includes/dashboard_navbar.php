<?php
$brandHref = $brandHref ?? 'dashboard.php';
$brandText = $brandText ?? 'SoftEdu';
$avatarFallback = $avatarFallback ?? 'https://ui-avatars.com/api/?name=' . substr(htmlspecialchars($user['name'] ?? 'U'), 0, 1) . '&background=0d6efd&color=fff';
$profileImage = $user['profile_image'] ?? '';
$imgSrc = $profileImage
    ? 'uploads/profiles/' . htmlspecialchars($profileImage)
    : $avatarFallback;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= htmlspecialchars($brandHref) ?>"><?= htmlspecialchars($brandText) ?></a>
        <div class="d-flex align-items-center">
            <img src="<?= $imgSrc ?>" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
            <span class="text-white me-3"><?= htmlspecialchars($user['name'] ?? '') ?></span>
            <a href="backend/auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>
