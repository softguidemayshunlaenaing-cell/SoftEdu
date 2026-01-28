<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/backend/config/db.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die('Database connection failed.');
}

$stmt = $db->prepare("SELECT name, email, role, profile_image FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | SoftEdu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-green: #059669;
            /* Modern Emerald */
            --bg-light: #f0fdf4;
            --surface: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        /* Mobile Optimization */
        @media (max-width: 576px) {
            .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            .profile-img {
                width: 100px;
                height: 100px;
            }

            h3 {
                font-size: 1.25rem;
            }

            .btn-lg-mobile {
                width: 100%;
            }

            /* Stack buttons on mobile */
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        }

        .header-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            height: 120px;
            border-radius: 20px 20px 0 0;
        }

        .profile-avatar-wrapper {
            margin-top: -60px;
            position: relative;
            display: inline-block;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background-color: #fff;
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }

        .btn-green {
            background-color: var(--primary-green);
            color: white;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: transform 0.2s, background 0.2s;
        }

        .btn-green:active {
            transform: scale(0.98);
        }

        .input-group-text {
            background: transparent;
            border-right: none;
        }

        .role-badge {
            background: var(--bg-light);
            color: var(--primary-green);
            font-weight: 700;
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container py-4 py-md-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <!-- <div class="mb-3 d-flex align-items-center">
                    <a href="dashboard.php" class="text-decoration-none text-muted small fw-bold">
                        <i class="fa-solid fa-chevron-left me-1"></i> BACK TO DASHBOARD
                    </a>
                </div> -->

                <div class="card">
                    <div class="header-gradient"></div>

                    <div class="card-body pt-0 text-center">
                        <div class="profile-avatar-wrapper mb-4">
                            <?php
                            $imgSrc = !empty($user['profile_image'])
                                ? 'uploads/profiles/' . htmlspecialchars($user['profile_image'])
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=059669&color=fff&size=128';
                            ?>
                            <img src="<?= $imgSrc ?>" id="profilePreview" class="rounded-circle profile-img"
                                alt="Profile">
                            <button
                                class="btn btn-sm btn-light position-absolute bottom-0 end-0 shadow-sm rounded-circle"
                                style="width: 36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fa-solid fa-camera text-primary-green"></i>
                            </button>
                        </div>

                        <h3 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                        <div class="mb-4">
                            <span class="role-badge"><?= htmlspecialchars($user['role']) ?></span>
                        </div>

                        <div class="text-start">
                            <ul class="nav nav-pills mb-4 justify-content-center justify-content-md-start"
                                id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill"
                                        data-bs-target="#tab-info">Account</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill px-4" data-bs-toggle="pill"
                                        data-bs-target="#tab-password">Security</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-info">
                                    <form id="nameForm">
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="<?= htmlspecialchars($user['name']) ?>" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Email Address (Read Only)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                                        class="fa-solid fa-envelope"></i></span>
                                                <input type="email" class="form-control bg-light border-start-0"
                                                    value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-green btn-lg-mobile px-5">Update
                                            Name</button>
                                        <div id="nameAlert" class="alert d-none mt-3"></div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="tab-password">
                                    <form id="passwordForm">
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control" name="current_password"
                                                required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">New Password</label>
                                                <input type="password" class="form-control" name="new_password"
                                                    minlength="8" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" name="confirm_password"
                                                    required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-green btn-lg-mobile px-5 mt-2">Change
                                            Password</button>
                                        <div id="passwordAlert" class="alert d-none mt-3"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Update Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="profile_image" accept="image/*" required>
                        </div>
                        <div id="uploadAlert" class="alert d-none small"></div>
                        <button type="submit" class="btn btn-green w-100">Upload Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- UPDATE NAME ---
        document.getElementById('nameForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('nameAlert');
            alertBox.className = 'alert d-none';

            try {
                const res = await fetch('backend/user/profile_update.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.className = 'alert alert-success mt-3';
                    // Update any name displays on the page
                    document.querySelectorAll('[data-session-name]').forEach(el => el.textContent = formData.get('name'));
                } else {
                    alertBox.className = 'alert alert-danger mt-3';
                }
                alertBox.textContent = result.message;
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.textContent = 'Network error. Could not connect to backend.';
                alertBox.classList.remove('d-none');
            }
        });

        // --- CHANGE PASSWORD ---
        document.getElementById('passwordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('passwordAlert');
            alertBox.className = 'alert d-none';

            // Client-side validation
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.textContent = 'New passwords do not match.';
                alertBox.classList.remove('d-none');
                return;
            }

            try {
                const res = await fetch('backend/user/password_change.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.className = 'alert alert-success mt-3';
                    e.target.reset(); // Clear form on success
                } else {
                    alertBox.className = 'alert alert-danger mt-3';
                }
                alertBox.textContent = result.message;
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('d-none');
            }
        });

        // --- UPLOAD PHOTO ---
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('uploadAlert');
            alertBox.className = 'alert d-none';

            try {
                const res = await fetch('backend/user/profile_image_upload.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.className = 'alert alert-success mt-2';
                    // Refresh the preview image
                    const timestamp = new Date().getTime();
                    document.getElementById('profilePreview').src = 'uploads/profiles/' + result.filename + '?t=' + timestamp;

                    // Close modal after 1 second
                    setTimeout(() => {
                        const modalEl = document.getElementById('uploadModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        alertBox.classList.add('d-none');
                        e.target.reset();
                    }, 1000);
                } else {
                    alertBox.className = 'alert alert-danger mt-2';
                }
                alertBox.textContent = result.message;
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger mt-2';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('d-none');
            }
        });
    </script>
    <footer class="mt-5 pb-4">
        <div class="container text-center">
            <hr class="mb-4 opacity-10">
            <div class="row align-items-center">
                <div class="col-md-4 text-md-start mb-3 mb-md-0">
                    <span class="fw-bold text-success" style="letter-spacing: 1px;">SOFT<span
                            class="text-dark">EDU</span></span>
                    <p class="text-muted small mb-0">&copy; 2024 All Rights Reserved</p>
                </div>

                <div class="col-md-4 mb-3 mb-md-0">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item mx-2">
                            <a href="#" class="text-decoration-none text-muted small hover-green">Privacy Policy</a>
                        </li>
                        <li class="list-inline-item mx-2">
                            <a href="#" class="text-decoration-none text-muted small hover-green">Terms of Service</a>
                        </li>
                        <li class="list-inline-item mx-2">
                            <a href="#" class="text-decoration-none text-muted small hover-green">Help Center</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 text-md-end">
                    <a href="#" class="text-muted mx-2 fs-5 hover-green"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-muted mx-2 fs-5 hover-green"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="text-muted mx-2 fs-5 hover-green"><i class="fa-brands fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <style>
            .hover-green {
                transition: color 0.2s ease;
            }

            .hover-green:hover {
                color: #059669 !important;
                /* Matches your primary green */
            }

            footer hr {
                border-top: 1px solid #e2e8f0;
            }
        </style>
    </footer>
</body>

</html>