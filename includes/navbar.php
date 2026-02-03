<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function getProfileImageUrl()
{
    $profileImg = $_SESSION['user_profile_image'] ?? '';
    if (!empty($profileImg)) {
        return 'uploads/profiles/' . htmlspecialchars($profileImg);
    }

    $initial = strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1));
    return "https://via.placeholder.com/32?text={$initial}";
}

// Normalize role once
$userRole = strtolower(trim($_SESSION['user_role'] ?? ''));
?>
<nav class="navbar navbar-expand-lg fixed-top shadow-sm py-3 mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">
            <span style="color: var(--primary-green);">SOFT</span><span class="text-dark">EDU</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link px-3 fw-medium text-dark" href="index.php#home">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-medium text-dark"
                        href="index.php#learning-path">Learning Path</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-medium text-dark" href="blogs.php">Blogs</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-medium text-dark" href="index.php#contact">Contact</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <!-- Dashboard Button -->
                    <a href="dashboard.php" class="btn btn-link fw-bold text-dark px-4">
                        Dashboard
                    </a>


                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" href="#"
                            data-bs-toggle="dropdown">
                            <img src="<?= getProfileImageUrl() ?>" width="32" height="32" class="rounded-circle me-2"
                                style="object-fit:cover;">
                            <span>
                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="dashboard.php?page=profile">
                                    My Profile
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="backend/auth/logout.php">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>

                <?php else: ?>
                    <a href="#" class="btn btn-link fw-bold text-dark px-4" data-bs-toggle="modal"
                        data-bs-target="#loginModal">
                        Sign In
                    </a>
                    <a href="#" class="btn btn-emerald px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#applyModal">
                        Apply Now
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>