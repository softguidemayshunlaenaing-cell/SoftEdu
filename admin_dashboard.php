<?php
// Ensure $user is available (from dashboard.php)
if (!isset($user)) {
    die('Access denied.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | SoftEdu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ===== COLOR VARIABLES: MODERN EMERALD ===== */
        :root {
            --primary-green: #059669;
            --primary-light: #f0fdf4;
            --primary-dark: #065f46;
            --bg-main: #f8fafc;
            --surface: #ffffff;
            --nav-dark: #2C2A32;
            --border-color: #e2e8f0;
        }

        /* ===== BODY & FONTS ===== */
        body {
            background-color: var(--bg-main);
            color: #334155;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }

        /* ===== MODERN CARD STYLING ===== */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            background: var(--surface);
            overflow: hidden;
        }

        /* Profile Header Gradient */
        .header-gradient {
            background: linear-gradient(135deg, var(--primary-green) 0%, #10b981 100%);
            height: 120px;
            border-radius: 20px 20px 0 0;
        }

        .profile-avatar-wrapper {
            margin-top: -60px;
            position: relative;
            display: inline-block;
            padding-left: 20px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 50%;
        }

        /* ===== NAVBAR & SIDEBAR ===== */
        .navbar {
            background-color: var(--nav-dark);
            padding: 0.75rem 1.5rem;
        }

        .sidebar {
            background-color: var(--surface);
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
            border-right: 1px solid var(--border-color);
            padding: 20px;
        }

        .nav-link {
            color: #475569;
            padding: 12px 15px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 4px;
            text-decoration: none;
            display: block;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary-green);
            font-weight: 600;
        }

        /* ===== BUTTONS ===== */
        .btn-green {
            background-color: var(--primary-green);
            color: white;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: transform 0.2s, background 0.2s;
            cursor: pointer;
        }

        .btn-green:hover {
            background-color: var(--primary-dark);
            color: white;
        }

        .btn-green:active {
            transform: scale(0.98);
        }

        /* ===== FORM ELEMENTS ===== */
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }

        /* ===== BADGES & TABLES ===== */
        .role-badge {
            background: var(--primary-light);
            color: var(--primary-green);
            font-weight: 700;
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            display: inline-block;
        }

        .table-hover tbody tr:hover {
            background-color: var(--primary-light);
        }

        /* ===== MOBILE OPTIMIZATION ===== */
        @media (max-width: 576px) {
            .container {
                padding: 0 12px;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            .profile-img {
                width: 100px;
                height: 100px;
            }

            .sidebar {
                position: static;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }

            .btn-lg-mobile {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">SoftEdu Admin</a>
            <div class="d-flex align-items-center">
                <?php
                $profileImg = $user['profile_image'] ?? '';
                $imgSrc = $profileImg
                    ? 'uploads/profiles/' . htmlspecialchars($profileImg)
                    : 'https://via.placeholder.com/32?text=' . substr(htmlspecialchars($user['name']), 0, 1);
                ?>
                <img src="<?= $imgSrc ?>" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                <span class="text-white me-3"><?= htmlspecialchars($user['name']) ?></span>
                <a href="backend/auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar bg-light p-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>"
                            href="profile.php">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] === 'applications') ? 'active' : '' ?>"
                            href="?page=applications">
                            <i class="fas fa-file-alt me-2"></i>Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'courses' ? 'active' : '' ?>"
                            href="?page=courses">
                            <i class="fas fa-book me-2"></i>Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'materials' ? 'active' : '' ?>"
                            href="?page=materials">
                            <i class="fas fa-file-pdf me-2"></i>Materials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'users' ? 'active' : '' ?>" href="?page=users">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <?php if (!isset($_GET['page']) || $_GET['page'] === 'applications'): ?>
                    <!-- Applications Table -->
                    <h2 class="mb-4">Manage Applications</h2>
                    <form method="GET" class="row g-3 mb-4">
                        <input type="hidden" name="page" value="applications">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>
                                    Pending</option>
                                <option value="approved" <?= (($_GET['status'] ?? '') === 'approved') ? 'selected' : '' ?>>
                                    Approved</option>
                                <option value="rejected" <?= (($_GET['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>
                                    Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="start_date" class="form-control"
                                value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="end_date" class="form-control"
                                value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="?page=applications" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Applied On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM softedu_applications WHERE 1=1";
                                $params = [];
                                if (!empty($_GET['status'])) {
                                    $sql .= " AND status = ?";
                                    $params[] = $_GET['status'];
                                }
                                if (!empty($_GET['start_date'])) {
                                    $sql .= " AND DATE(created_at) >= ?";
                                    $params[] = $_GET['start_date'];
                                }
                                if (!empty($_GET['end_date'])) {
                                    $sql .= " AND DATE(created_at) <= ?";
                                    $params[] = $_GET['end_date'];
                                }
                                $sql .= " ORDER BY created_at ASC";
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                while ($app = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($app['name']) ?></td>
                                        <td><?= htmlspecialchars($app['email']) ?></td>
                                        <td><?= htmlspecialchars($app['phone']) ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?= $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($app['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($app['created_at'])) ?></td>
                                        <td>
                                            <?php if ($app['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-success approve-btn" data-id="<?= $app['id'] ?>"
                                                    data-name="<?= htmlspecialchars($app['name']) ?>">Approve</button>

                                                <button class="btn btn-sm btn-danger reject-btn"
                                                    data-id="<?= $app['id'] ?>">Reject</button>
                                            <?php else: ?>
                                                <em>Final</em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif ($_GET['page'] === 'courses'): ?>
                    <!-- Courses Management -->
                    <h2 class="mb-4">Manage Courses</h2>
                    <button class="btn btn-softedu mb-3" data-bs-toggle="modal" data-bs-target="#courseModal">
                        <i class="fas fa-plus me-1"></i> Add New Course
                    </button>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Duration</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->query("SELECT * FROM softedu_courses ORDER BY created_at ASC");
                                while ($course = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($course['image']): ?>
                                                <img src="uploads/courses/<?= htmlspecialchars($course['image']) ?>" width="60"
                                                    class="rounded">
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($course['title']) ?></td>
                                        <td><?= htmlspecialchars($course['duration']) ?></td>
                                        <td><?= $course['fee'] ? '$' . number_format($course['fee'], 2) : 'Free' ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?= $course['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($course['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info materials-btn"
                                                data-id="<?= $course['id'] ?>">Materials</button>
                                            <button class="btn btn-sm btn-outline-primary edit-course-btn"
                                                data-id="<?= $course['id'] ?>">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger delete-course-btn"
                                                data-id="<?= $course['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif ($_GET['page'] === 'materials'): ?>
                    <!-- Materials Management -->
                    <h2 class="mb-3">Manage Course Materials</h2>

                    <form method="GET" class="row g-3 mb-4">
                        <input type="hidden" name="page" value="materials">

                        <div class="col-md-4">
                            <label class="form-label">Filter by Course</label>
                            <select name="course_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                <?php
                                $courseStmt = $db->query("SELECT id, title FROM softedu_courses ORDER BY title");
                                while ($c = $courseStmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <option value="<?= $c['id'] ?>" <?= (($_GET['course_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['title']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-8 d-flex align-items-end">
                            <button type="button" class="btn btn-softedu me-2" data-bs-toggle="modal"
                                data-bs-target="#addMaterialModal">
                                <i class="fas fa-plus me-1"></i> Add New Material
                            </button>

                            <?php if (!empty($_GET['course_id'])): ?>
                                <a href="?page=materials" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>


                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>URL</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT m.*, c.title AS course_title FROM softedu_course_materials m JOIN softedu_courses c ON m.course_id = c.id WHERE 1=1";

                                    $params = [];

                                    if (!empty($_GET['course_id'])) {
                                        $sql .= " AND m.course_id = ?";
                                        $params[] = (int) $_GET['course_id'];
                                    }

                                    $sql .= " ORDER BY m.created_at ASC";

                                    $stmt = $db->prepare($sql);
                                    $stmt->execute($params);

                                while ($mat = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($mat['course_title']) ?></td>
                                        <td><?= htmlspecialchars($mat['title']) ?></td>
                                        <td><?= ucfirst($mat['material_type']) ?></td>
                                        <td><?= ucfirst(str_replace('_', ' ', $mat['source'])) ?></td>
                                        <td><a href="<?= htmlspecialchars($mat['material_url']) ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary">View</a></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger delete-material-btn"
                                                data-id="<?= $mat['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif ($_GET['page'] === 'users'): ?>
                    <!-- Users Management -->
                    <h2 class="mb-4">Manage Users</h2>
                    <form method="GET" class="row g-3 mb-4">
                        <input type="hidden" name="page" value="users">
                        <div class="col-md-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="">All Roles</option>
                                <option value="admin" <?= (($_GET['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin
                                </option>
                                <option value="staff" <?= (($_GET['role'] ?? '') === 'staff') ? 'selected' : '' ?>>Staff
                                </option>
                                <option value="student" <?= (($_GET['role'] ?? '') === 'student') ? 'selected' : '' ?>>Student
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="start_date" class="form-control"
                                value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="end_date" class="form-control"
                                value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                            <a href="?page=users" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Registered On</th>
                                    <th>Documents</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT id, name, email, role, profile_image, created_at FROM softedu_users WHERE 1=1";
                                $params = [];
                                if (!empty($_GET['role'])) {
                                    $sql .= " AND role = ?";
                                    $params[] = $_GET['role'];
                                }
                                if (!empty($_GET['start_date'])) {
                                    $sql .= " AND DATE(created_at) >= ?";
                                    $params[] = $_GET['start_date'];
                                }
                                if (!empty($_GET['end_date'])) {
                                    $sql .= " AND DATE(created_at) <= ?";
                                    $params[] = $_GET['end_date'];
                                }
                                $sql .= " ORDER BY created_at DESC";
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                while ($userRow = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="<?= !empty($userRow['profile_image'])
                                                    ? 'uploads/profiles/' . htmlspecialchars($userRow['profile_image'])
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($userRow['name']) . '&background=28a745&color=fff&size=128' ?>"
                                            width="40" class="rounded" alt="Profile">

                                        </td>
                                        <td><?= htmlspecialchars($userRow['name']) ?></td>
                                        <td><?= htmlspecialchars($userRow['email']) ?></td>
                                        <td><span class="badge bg-secondary"><?= ucfirst($userRow['role']) ?></span></td>
                                        <td><?= date('M j, Y', strtotime($userRow['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-docs-btn"
                                                data-user-id="<?= $userRow['id'] ?>">View Docs</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <!-- Generated Email Modal -->
    <div class="modal fade" id="generatedEmailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Generated Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Admin can edit the email before creating the student account:</p>
                    <input type="email" id="generatedEmailInput" class="form-control" />
                    <div id="emailAlert" class="alert alert-danger mt-2 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Docs Modal -->
    <div class="modal fade" id="userDocsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDocsList">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- COURSE MODAL -->
    <div class="modal fade" id="courseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="courseForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="courseId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="courseTitle" class="form-label">Course Title *</label>
                                    <input type="text" class="form-control" id="courseTitle" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="courseDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="courseDescription" name="description"
                                        rows="3"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="courseDuration" class="form-label">Duration (e.g., "3
                                                Years")</label>
                                            <input type="text" class="form-control" id="courseDuration" name="duration">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="courseFee" class="form-label">Fee (USD)</label>
                                            <input type="number" step="0.01" class="form-control" id="courseFee"
                                                name="fee">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="courseStatus" class="form-label">Status</label>
                                    <select class="form-select" id="courseStatus" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <label for="courseImage" class="form-label">Course Image</label>
                                    <input type="file" class="form-control" id="courseImage" name="image"
                                        accept="image/*">
                                    <div class="form-text">Max 2MB. JPG/PNG.</div>
                                    <img id="courseImagePreview" src="" class="mt-2"
                                        style="max-width:100%; display:none;">
                                </div>
                            </div>
                        </div>
                        <div id="courseAlert" class="alert d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-softedu">Save Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MATERIALS MODAL (per-course) -->
    <div class="modal fade" id="materialsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Course Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="materialsList"></div>
                    <hr>
                    <form id="quickAddMaterialForm">
                        <input type="hidden" name="course_id" id="materialCourseId">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="materialTitle" class="form-label">Title *</label>
                                <input type="text" name="title" id="materialTitle" class="form-control"
                                    placeholder="e.g., Week 1 Lecture" required>
                            </div>
                            <div class="col-md-3">
                                <label for="materialType" class="form-label">Type *</label>
                                <select name="material_type" id="materialType" class="form-select" required>
                                    <option value="video">Video</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="materialSource" class="form-label">Source *</label>
                                <select name="source" id="materialSource" class="form-select" required>
                                    <option value="youtube">YouTube</option>
                                    <option value="google_drive">Google Drive</option>
                                    <option value="external">External Link</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-softedu w-100">Add</button>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="materialUrl" class="form-label">URL *</label>
                            <input type="url" name="material_url" id="materialUrl" class="form-control"
                                placeholder="https://..." required>
                        </div>
                    </form>
                    <div id="materialAlert" class="alert d-none mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD MATERIAL MODAL (global) -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addMaterialForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="addMaterialCourse" class="form-label">Course *</label>
                                <select name="course_id" id="addMaterialCourse" class="form-select" required>
                                    <option value="">Select a course</option>
                                    <?php
                                    $courses = $db->query("SELECT id, title FROM softedu_courses WHERE status = 'active' ORDER BY title");
                                    while ($c = $courses->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['title']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="addMaterialTitle" class="form-label">Title *</label>
                                <input type="text" name="title" id="addMaterialTitle" class="form-control"
                                    placeholder="e.g., Week 1 Lecture" required>
                            </div>
                            <div class="col-md-4">
                                <label for="addMaterialType" class="form-label">Type *</label>
                                <select name="material_type" id="addMaterialType" class="form-select" required>
                                    <option value="video">Video</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="addMaterialSource" class="form-label">Source *</label>
                                <select name="source" id="addMaterialSource" class="form-select" required>
                                    <option value="youtube">YouTube</option>
                                    <option value="google_drive">Google Drive</option>
                                    <option value="external">External Link</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="addMaterialRole" class="form-label">Role Access</label>
                                <select name="role_access" id="addMaterialRole" class="form-select">
                                    <option value="all">All Roles</option>
                                    <option value="student">Students Only</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="addMaterialUrl" class="form-label">URL *</label>
                                <input type="url" name="material_url" id="addMaterialUrl" class="form-control"
                                    placeholder="https://..." required>
                                <div class="form-text">
                                    For Google Drive: use "Shareable link" → change to <code>preview</code> or
                                    <code>uc?export=download</code>
                                </div>
                            </div>
                        </div>
                        <div id="addMaterialAlert" class="alert d-none mt-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-softedu">Add Material</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ✅ FIXED: Use legacy jsPDF without trailing spaces -->
    <!-- jsPDF legacy build -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>


    <script>
        // ===== APPROVE / REJECT APPLICATIONS =====
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                const userName = btn.closest('tr').querySelector('td').textContent || 'User';

                // Generate default email
                const norm = userName.toLowerCase().replace(/[^a-z0-9]/g, '');
                const defaultEmail = `softedu.${norm}@gmail.com`;

                // Show modal
                const modalEl = document.getElementById('generatedEmailModal');
                const modal = new bootstrap.Modal(modalEl);
                const emailInput = document.getElementById('generatedEmailInput');
                const emailAlert = document.getElementById('emailAlert');

                emailInput.value = defaultEmail;
                emailAlert.classList.add('d-none'); // hide previous alerts

                modal.show();

                const confirmBtn = document.getElementById('confirmApproveBtn');

                // Remove any previous click handlers
                confirmBtn.replaceWith(confirmBtn.cloneNode(true));
                const newConfirmBtn = document.getElementById('confirmApproveBtn');

                newConfirmBtn.addEventListener('click', async () => {
                    const emailToUse = emailInput.value.trim();
                    if (!emailToUse) {
                        emailAlert.textContent = 'Email cannot be empty.';
                        emailAlert.classList.remove('d-none');
                        return;
                    }

                    try {
                        const res = await fetch('backend/admin/approve_application.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id, action: 'approve', generated_email: emailToUse })
                        });
                        const result = await res.json();
                        console.log(result);
                        if (result.success) {
                            modal.hide();
                            location.reload();
                        } else {
                            emailAlert.textContent = result.message;
                            emailAlert.classList.remove('d-none');
                        }
                    } catch (err) {
                        emailAlert.textContent = 'Network error. Try again.';
                        emailAlert.classList.remove('d-none');
                    }
                });
            });
        });


        // ===== COURSE MANAGEMENT =====
        document.querySelector('[data-bs-target="#courseModal"]')?.addEventListener('click', () => {
            document.getElementById('courseForm').reset();
            document.getElementById('courseImagePreview').style.display = 'none';
            document.getElementById('courseId').value = '';
            document.getElementById('courseModalLabel').textContent = 'Add New Course';
        });

        document.getElementById('courseForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('courseAlert');
            if (alertBox) alertBox.className = 'alert d-none';

            try {
                const url = formData.get('id')
                    ? 'backend/course/edit_course.php'
                    : 'backend/course/add_course.php';
                const res = await fetch(url, { method: 'POST', body: formData });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const result = await res.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide();
                    setTimeout(() => location.reload(), 800);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger';
                        alertBox.textContent = result.message;
                        alertBox.classList.remove('d-none');
                    } else {
                        alert(result.message);
                    }
                }
            } catch (err) {
                console.error('Save course error:', err);
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Network error. Check backend files.';
                    alertBox.classList.remove('d-none');
                } else {
                    alert('Network error. Check console.');
                }
            }
        });

        document.querySelectorAll('.edit-course-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                try {
                    const res = await fetch(`backend/course/get_course.php?id=${id}`);
                    const course = await res.json();
                    if (course) {
                        document.getElementById('courseId').value = course.id;
                        document.getElementById('courseTitle').value = course.title;
                        document.getElementById('courseDescription').value = course.description || '';
                        document.getElementById('courseDuration').value = course.duration || '';
                        document.getElementById('courseFee').value = course.fee || '';
                        document.getElementById('courseStatus').value = course.status;

                        const preview = document.getElementById('courseImagePreview');
                        if (course.image) {
                            preview.src = `uploads/courses/${course.image}`;
                            preview.style.display = 'block';
                        } else {
                            preview.style.display = 'none';
                        }

                        document.getElementById('courseModalLabel').textContent = 'Edit Course';
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('courseModal')).show();
                    }
                } catch (err) {
                    alert('Failed to load course data.');
                }
            });
        });

        document.querySelectorAll('.delete-course-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this course?')) return;
                const id = btn.dataset.id;
                try {
                    const res = await fetch('backend/course/delete_course.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const result = await res.json();
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to delete course.');
                }
            });
        });

        // ===== MATERIALS MANAGEMENT =====
        document.querySelectorAll('.materials-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const courseId = btn.dataset.id;
                document.getElementById('materialCourseId').value = courseId;
                try {
                    const res = await fetch(`backend/course/get_materials.php?course_id=${courseId}`);
                    const materials = await res.json();
                    let html = '<h6>Existing Materials:</h6>';
                    if (materials.length > 0) {
                        html += '<ul class="list-group">';
                        materials.forEach(m => {
                            html += `<li class="list-group-item d-flex justify-content-between">
                                ${m.title} (${m.source})
                              
                            </li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p class="text-muted">No materials yet.</p>';
                    }
                    document.getElementById('materialsList').innerHTML = html;
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('materialsModal')).show();
                } catch (err) {
                    alert('Failed to load materials.');
                }
            });
        });

        // Quick add from per-course modal
        document.getElementById('quickAddMaterialForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('materialAlert');
            if (alertBox) alertBox.className = 'alert d-none';

            // Validate
            const courseId = document.getElementById('materialCourseId').value;
            const title = document.getElementById('materialTitle').value;
            const url = document.getElementById('materialUrl').value;
            if (!courseId || !title || !url) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'All fields are required.';
                    alertBox.classList.remove('d-none');
                }
                return;
            }

            try {
                const res = await fetch('backend/course/add_material.php', { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent = 'Added!';
                    alertBox.classList.remove('d-none');
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('materialsModal')).hide();
                        document.querySelector(`.materials-btn[data-id="${courseId}"]`).click();
                    }, 1500);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger';
                        alertBox.textContent = result.message;
                        alertBox.classList.remove('d-none');
                    }
                }
            } catch (err) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Network error.';
                    alertBox.classList.remove('d-none');
                }
            }
        });

        // Add material from global modal
        document.getElementById('addMaterialForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('addMaterialAlert');
            if (alertBox) alertBox.className = 'alert d-none';

            const courseId = formData.get('course_id');
            const title = formData.get('title');
            const url = formData.get('material_url');
            if (!courseId || !title || !url) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'All fields are required.';
                    alertBox.classList.remove('d-none');
                }
                return;
            }

            try {
                const res = await fetch('backend/course/add_material.php', { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) {
                    alertBox.className = 'alert alert-success';
                    bootstrap.Modal.getInstance(document.getElementById('addMaterialModal')).hide();
                    setTimeout(() => location.reload(), 800);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger';
                        alertBox.textContent = result.message;
                        alertBox.classList.remove('d-none');
                    }
                }
            } catch (err) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Network error.';
                    alertBox.classList.remove('d-none');
                }
            }
        });

        // Delete material
        document.querySelectorAll('.delete-material-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this material?')) return;
                const id = btn.dataset.id;
                try {
                    await fetch('backend/course/delete_material.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    btn.closest('tr')?.remove();
                    // Refresh per-course modal if open
                    const modal = bootstrap.Modal.getInstance(document.getElementById('materialsModal'));
                    if (modal && document.querySelector('#materialsModal.show')) {
                        const courseId = document.getElementById('materialCourseId').value;
                        document.querySelector(`.materials-btn[data-id="${courseId}"]`).click();
                    }
                } catch (err) {
                    alert('Failed to delete material.');
                }
            });
        });

        // ===== VIEW DOCS + PDF DOWNLOAD =====
        function loadImageAsBase64(url) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    canvas.getContext('2d').drawImage(img, 0, 0);
                    resolve(canvas.toDataURL('image/jpeg', 0.9));
                };
                img.onerror = () => reject(new Error('Failed to load image'));
                img.src = url + '?t=' + Date.now(); // Bypass cache
            });
        }

        document.querySelectorAll('.view-docs-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const userId = btn.dataset.userId;
                const userName = btn.dataset.userName || 'User';
                const modalBody = document.getElementById('userDocsList');

                try {
                    const res = await fetch(`backend/user/get_user_docs.php?user_id=${userId}`);
                    const docs = await res.json();

                    if (!docs || docs.length === 0) {
                        // ✅ Show alert and do NOT open modal
                        alert(`${userName} has not uploaded any documents.`);
                        return;
                    }

                    // If there are docs, show the modal
                    let html = '<div class="pdf-preview">';
                    docs.forEach(d => {
                        html += `
                <div class="pdf-page mb-3">
                    <img src="${d.file_url}" class="img-fluid border" style="max-height:500px; display:block; margin:auto;">
                </div>`;
                    });
                    html += `</div>
            <button id="downloadPdfBtn" class="btn btn-success mt-3">Download as PDF</button>`;
                    modalBody.innerHTML = html;

                    const modal = new bootstrap.Modal(document.getElementById('userDocsModal'));
                    modal.show();

                    // Download PDF
                    // Inside the "Download PDF" click handler
                    document.getElementById('downloadPdfBtn').addEventListener('click', async () => {
                        // ✅ SAFETY CHECK: Is jsPDF available?
                        if (!window.jspdf || typeof window.jspdf.jsPDF !== 'function') {
                            alert('PDF library failed to load. Please refresh the page.');
                            return;
                        }

                        const { jsPDF } = window.jspdf; // ✅ Correct
                        const pdf = new jsPDF('p', 'mm', 'a4');


                        const docs = await fetch(`backend/user/get_user_docs.php?user_id=${userId}`)
                            .then(r => r.json());

                        if (!docs || docs.length === 0) {
                            alert('No documents to download.');
                            return;
                        }

                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();

                        for (let i = 0; i < docs.length; i++) {
                            try {
                                const imgData = await loadImageAsBase64(docs[i].file_url);

                                // Create temp image to get dimensions
                                const img = new Image();
                                img.src = imgData;
                                await new Promise(resolve => img.onload = resolve);

                                let width = pageWidth;
                                let height = (img.height * width) / img.width;

                                // Scale to fit page
                                if (height > pageHeight) {
                                    const ratio = pageHeight / height;
                                    height *= ratio;
                                    width *= ratio;
                                }

                                const yOffset = (pageHeight - height) / 2;
                                pdf.addImage(imgData, 'JPEG', 0, yOffset, width, height);

                                if (i < docs.length - 1) pdf.addPage();
                            } catch (err) {
                                console.error('PDF generation error:', err);
                                alert(`Failed to add document ${i + 1}. Check console.`);
                                return;
                            }
                        }

                        pdf.save(`user_${userId}_documents.pdf`);
                    });
                } catch (err) {
                    console.error('Fetch error:', err);
                    modalBody.innerHTML = '<p class="text-danger">Failed to load documents.</p>';
                }
            });
        });
    </script>
</body>

</html>