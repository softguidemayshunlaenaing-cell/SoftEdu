<?php
session_start();

require_once __DIR__ . '/backend/config/db.php';
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT name, email, role, profile_image FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard | SoftEdu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #059669;
            --primary-light: #f0fdf4;
            --primary-dark: #065f46;
            --bg-main: #f8fafc;
            --surface: #ffffff;
            --nav-dark: #2C2A32;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-main);
            color: #334155;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            background: var(--surface);
            overflow: hidden;
        }

        .header-gradient {
            background: linear-gradient(135deg, var(--primary-green) 0%, #10b981 100%);
            height: 120px;
            border-radius: 20px 20px 0 0;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 50%;
        }

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

        /* Green Buttons */
        .btn-green {
            background-color: var(--primary-green);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: transform 0.2s, background 0.2s;
        }

        .btn-green:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-green:active {
            transform: scale(0.98);
        }

        /* Small Green Buttons */
        .btn-green-sm {
            background-color: var(--primary-green);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .btn-green-sm:hover {
            background-color: var(--primary-dark);
        }

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
        .pagination-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
            min-height: 48px;
        }
        .table-fixed-rows {
            min-height: 600px;
        }
        .empty-row td {
            height: 48px;
        }

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
            <a class="navbar-brand fw-bold" href="staff_dashboard.php">SoftEdu Staff</a>
            <div class="d-flex align-items-center">
                <?php
                $imgSrc = $user['profile_image']
                    ? 'uploads/profiles/' . htmlspecialchars($user['profile_image'])
                    : 'https://ui-avatars.com/api/?name=' . substr(htmlspecialchars($user['name']), 0, 1) . '&background=0d6efd&color=fff';
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
            <div class="col-md-3 col-lg-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'profile' ? 'active' : '' ?>"
                            href="staff_dashboard.php?page=profile">
                            <i class="fas fa-user-circle me-2"></i> My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] === 'courses') ? 'active' : '' ?>"
                            href="staff_dashboard.php?page=courses">
                            <i class="fas fa-book me-2"></i> Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'materials' ? 'active' : '' ?>"
                            href="staff_dashboard.php?page=materials">
                            <i class="fas fa-file-video me-2"></i> Materials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'assignments' ? 'active' : '' ?>"
                            href="staff_dashboard.php?page=assignments">
                            <i class="fas fa-tasks me-2"></i> Assignments
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <?php if (!isset($_GET['page']) || $_GET['page'] === 'courses'): ?>
                    <!-- COURSES TAB -->
                    <h2 class="mb-4">Manage Courses</h2>
                    <button class="btn btn-green mb-3" data-bs-toggle="modal" data-bs-target="#courseModal">
                        <i class="fas fa-plus me-1"></i> Add New Course
                    </button>
                    <div class="table-responsive table-fixed-rows">
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
                                $stmt = $db->query("SELECT * FROM softedu_courses ORDER BY created_at DESC");
                                while ($course = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($course['image']): ?>
                                                <img src="uploads/courses/<?= htmlspecialchars($course['image']) ?>" width="60"
                                                    class="rounded">
                                            <?php else: ?>—<?php endif; ?>
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
                                            <button class="btn btn-green-sm materials-btn"
                                                data-id="<?= $course['id'] ?>">Materials</button>
                                            <button class="btn btn-green-sm edit-course-btn"
                                                data-id="<?= $course['id'] ?>">Edit</button>
                                            <button class="btn btn-outline-danger btn-sm delete-course-btn"
                                                data-id="<?= $course['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif ($_GET['page'] === 'materials'): ?>
                    <!-- MATERIALS TAB -->
                    <h2 class="mb-4">Manage Materials</h2>
                    <form method="GET" class="row g-3 mb-4">
                        <input type="hidden" name="page" value="materials">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Course</label>
                            <select name="course_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                <?php
                                // ✅ FIXED: Fetch ALL courses (not filtered)
                                $courseStmt = $db->query("SELECT id, title FROM softedu_courses ORDER BY title");
                                while ($c = $courseStmt->fetch(PDO::FETCH_ASSOC)):
                                    $selected = (isset($_GET['course_id']) && $_GET['course_id'] == $c['id']) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $c['id'] ?>" <?= $selected ?>><?= htmlspecialchars($c['title']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button type="button" class="btn btn-green me-2" data-bs-toggle="modal"
                                data-bs-target="#addMaterialModal">
                                <i class="fas fa-plus me-1"></i> Add New Material
                            </button>
                            <?php if (!empty($_GET['course_id'])): ?>
                                <a href="staff_dashboard.php?page=materials" class="btn btn-outline-secondary">Reset</a>
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
                                $sql = "SELECT m.*, c.title as course_title FROM softedu_course_materials m JOIN softedu_courses c ON m.course_id = c.id WHERE 1=1";
                                $params = [];
                                if (!empty($_GET['course_id'])) {
                                    $sql .= " AND m.course_id = ?";
                                    $params[] = (int) $_GET['course_id'];
                                }
                                $perPage = 10;
                                $pageNum = max(1, (int) ($_GET['materials_page'] ?? 1));
                                $offset = ($pageNum - 1) * $perPage;

                                $countSql = "SELECT COUNT(*) FROM softedu_course_materials m WHERE 1=1";
                                $countParams = $params;
                                if (!empty($_GET['course_id'])) {
                                    $countSql .= " AND m.course_id = ?";
                                }
                                $countStmt = $db->prepare($countSql);
                                $countStmt->execute($countParams);
                                $totalRows = (int) $countStmt->fetchColumn();
                                $totalPages = max(1, (int) ceil($totalRows / $perPage));

                                $sql .= " ORDER BY m.created_at DESC LIMIT $perPage OFFSET $offset";
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                $rowCount = 0;
                                while ($mat = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    $rowCount++;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($mat['course_title']) ?></td>
                                        <td><?= htmlspecialchars($mat['title']) ?></td>
                                        <td><?= ucfirst($mat['material_type']) ?></td>
                                        <td><?= ucfirst(str_replace('_', ' ', $mat['source'])) ?></td>
                                        <td>
                                            <a href="<?= htmlspecialchars($mat['material_url']) ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger delete-material-btn"
                                                data-id="<?= $mat['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php for ($i = $rowCount; $i < $perPage; $i++): ?>
                                    <tr class="empty-row">
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-wrap">
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Materials pagination">
                                <ul class="pagination">
                                    <?php
                                    $prevPage = max(1, $pageNum - 1);
                                    $nextPage = min($totalPages, $pageNum + 1);
                                    $baseParams = $_GET;
                                    $baseParams['page'] = 'materials';
                                    ?>
                                    <li class="page-item <?= $pageNum <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="staff_dashboard.php?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['materials_page' => $prevPage]))) ?>">
                                            Previous
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $pageNum ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="staff_dashboard.php?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['materials_page' => $i]))) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pageNum >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="staff_dashboard.php?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['materials_page' => $nextPage]))) ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>

                <?php elseif (($_GET['page'] ?? '') === 'profile'): ?>
                    <!-- PROFILE TAB -->
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <?php
                                $imgSrc = !empty($user['profile_image'])
                                    ? 'uploads/profiles/' . htmlspecialchars($user['profile_image'])
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=059669&color=fff&size=128';
                                ?>
                                <img src="<?= $imgSrc ?>" id="profilePreview" class="rounded-circle mb-3" width="120"
                                    height="120" style="object-fit: cover; border: 3px solid #e2e8f0;">
                                <h4 class="mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                                <span class="badge bg-success"><?= ucfirst($_SESSION['user_role']) ?></span>
                            </div>
                            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#accountTab">Account</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#securityTab">Security</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="accountTab">
                                    <form id="nameForm">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Full Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="<?= htmlspecialchars($user['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Email Address (Read Only)</label>
                                            <input type="email" class="form-control bg-light"
                                                value="<?= htmlspecialchars($_SESSION['user_email']) ?>" disabled>
                                        </div>
                                        <button type="submit" class="btn btn-green">Update Name</button>
                                        <div id="nameAlert" class="alert d-none mt-3"></div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="securityTab">
                                    <form id="passwordForm">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">New Password</label>
                                                <input type="password" class="form-control" name="new_password"
                                                    minlength="8" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Confirm New Password</label>
                                                <input type="password" class="form-control" name="confirm_password"
                                                    required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-green">Change Password</button>
                                        <div id="passwordAlert" class="alert d-none mt-3"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($_GET['page'] === 'assignments'): ?>
                    <!-- ASSIGNMENTS TAB -->
                    <h2 class="mb-4">Manage Assignments</h2>
                    <form method="GET" class="row g-3 mb-4">
                        <input type="hidden" name="page" value="assignments">
                        <div class="col-md-3">
                            <label class="form-label">Filter by Course</label>
                            <select name="course_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                <?php
                                $courseStmt = $db->query("SELECT id, title FROM softedu_courses ORDER BY title");
                                while ($c = $courseStmt->fetch(PDO::FETCH_ASSOC)):
                                    $selected = (isset($_GET['course_id']) && $_GET['course_id'] == $c['id']) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $c['id'] ?>" <?= $selected ?>><?= htmlspecialchars($c['title']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-9 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-green me-2" data-bs-toggle="modal"
                                data-bs-target="#addAssignmentModal">
                                <i class="fas fa-plus me-1"></i> Add New Assignment
                            </button>
                            <a href="backend/export/assignments_csv.php?<?= http_build_query($_GET) ?>"
                                class="btn btn-outline-success">
                                <i class="fas fa-file-csv me-1"></i> Export to CSV
                            </a>
                            <?php if (!empty($_GET['course_id'])): ?>
                                <a href="staff_dashboard.php?page=assignments" class="btn btn-outline-secondary">Reset
                                    Filters</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Title</th>
                                    <th>Due Date</th>
                                    <th>Submissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT a.*, c.title as course_title FROM softedu_assignments a JOIN softedu_courses c ON a.course_id = c.id WHERE 1=1";
                                $params = [];
                                if (!empty($_GET['course_id'])) {
                                    $sql .= " AND a.course_id = ?";
                                    $params[] = (int) $_GET['course_id'];
                                }
                                $sql .= " ORDER BY a.due_date DESC";
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                while ($assign = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    $subStmt = $db->prepare("SELECT COUNT(*) FROM softedu_assignment_submissions WHERE assignment_id = ?");
                                    $subStmt->execute([$assign['id']]);
                                    $submissionCount = $subStmt->fetchColumn();
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($assign['course_title']) ?></td>
                                        <td><?= htmlspecialchars($assign['title']) ?></td>
                                        <td><?= date('M j, Y', strtotime($assign['due_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $submissionCount > 0 ? 'success' : 'secondary' ?>">
                                                <?= $submissionCount ?> submission(s)
                                            </span>
                                        </td>
                                        <td>
                                            <a href="staff_dashboard.php?page=assignment_submissions&assignment_id=<?= $assign['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">View Submissions</a>
                                            <button class="btn btn-sm btn-outline-warning edit-assignment-btn"
                                                data-id="<?= $assign['id'] ?>"
                                                data-title="<?= htmlspecialchars($assign['title']) ?>"
                                                data-description="<?= htmlspecialchars($assign['description']) ?>"
                                                data-due-date="<?= date('Y-m-d\TH:i', strtotime($assign['due_date'])) ?>"
                                                data-course-id="<?= $assign['course_id'] ?>">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-assignment-btn"
                                                data-id="<?= $assign['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif ($_GET['page'] === 'assignment_submissions'): ?>
                    <!-- SUBMISSIONS DETAIL -->
                    <?php
                    $assignment_id = (int) ($_GET['assignment_id'] ?? 0);
                    if (!$assignment_id) {
                        echo '<div class="alert alert-danger">Invalid assignment ID.</div>';
                        exit;
                    }
                    $stmt = $db->prepare("
                        SELECT a.*, c.title as course_title
                        FROM softedu_assignments a
                        JOIN softedu_courses c ON a.course_id = c.id
                        WHERE a.id = ?
                    ");
                    $stmt->execute([$assignment_id]);
                    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$assignment) {
                        echo '<div class="alert alert-danger">Assignment not found.</div>';
                        exit;
                    }
                    ?>
                    <h2 class="mb-4">Submissions: <?= htmlspecialchars($assignment['title']) ?></h2>
                    <p><strong>Course:</strong> <?= htmlspecialchars($assignment['course_title']) ?></p>
                    <p><strong>Due Date:</strong> <?= date('M j, Y', strtotime($assignment['due_date'])) ?></p>
                    <a href="staff_dashboard.php?page=assignments" class="btn btn-secondary mb-3">← Back to Assignments</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>File</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->prepare("
                                    SELECT s.*, u.name as student_name, u.email
                                    FROM softedu_assignment_submissions s
                                    JOIN softedu_users u ON s.student_id = u.id
                                    WHERE s.assignment_id = ?
                                    ORDER BY s.submitted_at DESC
                                ");
                                $stmt->execute([$assignment_id]);
                                while ($sub = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sub['student_name']) ?><br><small><?= htmlspecialchars($sub['email']) ?></small>
                                        </td>
                                        <td>
                                            <a href="uploads/assignments/<?= htmlspecialchars($sub['file_path']) ?>"
                                                target="_blank" class="btn btn-sm btn-outline-primary">View File</a>
                                        </td>
                                        <td><?= date('M j, Y g:i A', strtotime($sub['submitted_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger delete-submission-btn"
                                                data-id="<?= $sub['id'] ?>">Delete</button>
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

    <!-- Modals -->
    <?php include 'modals/course_modal.php'; ?>
    <?php include 'modals/materials_modal.php'; ?>
    <?php include 'modals/add_material_modal.php'; ?>
    <?php include 'modals/add_assignment_modal.php'; ?>
    <?php include 'modals/edit_assignment_modal.php'; ?>

    <!-- Profile Image Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Choose Image</label>
                            <input type="file" class="form-control" name="profile_image" accept="image/*" required>
                        </div>
                        <div id="uploadAlert" class="alert d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-green">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
                const result = await res.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide();
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

        // ===== MATERIALS BUTTON HANDLER =====
        document.querySelectorAll('.materials-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const courseId = btn.dataset.id;
                const courseIdField = document.getElementById('materialCourseId');
                if (courseIdField) courseIdField.value = courseId;
                try {
                    const res = await fetch(`backend/course/get_materials.php?course_id=${courseId}`);
                    const materials = await res.json();
                    let html = '<h6>Existing Materials:</h6>';
                    if (materials.length > 0) {
                        html += '<ul class="list-group">';
                        materials.forEach(m => {
                            html += `<li class="list-group-item">${m.title} (${m.source})</li>`;
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

        // ===== EDIT COURSE BUTTON HANDLER =====
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

        // ===== DELETE HANDLERS =====
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
                } catch (err) {
                    alert('Failed to delete material.');
                }
            });
        });

        // ===== ADD MATERIAL (from Materials tab modal) =====
        document.getElementById('addMaterialForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('addMaterialAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/course/add_material.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    if (alertBox) {
                        alertBox.className = 'alert alert-success';
                        alertBox.textContent = result.message || 'Material added successfully.';
                        alertBox.classList.remove('d-none');
                    }
                    e.target.reset();
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'))?.hide();
                        window.location.href = 'staff_dashboard.php?page=materials';
                    }, 800);
                } else if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message || 'Failed to add material.';
                    alertBox.classList.remove('d-none');
                }
            } catch (err) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Network error.';
                    alertBox.classList.remove('d-none');
                }
            }
        });

        // ===== QUICK ADD MATERIAL (from Courses tab modal) =====
        document.getElementById('quickAddMaterialForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('materialAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            const courseId = formData.get('course_id');
            if (!courseId) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Please select a course first.';
                    alertBox.classList.remove('d-none');
                }
                return;
            }
            try {
                const res = await fetch('backend/course/add_material.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    if (alertBox) {
                        alertBox.className = 'alert alert-success';
                        alertBox.textContent = result.message || 'Material added successfully.';
                        alertBox.classList.remove('d-none');
                    }
                    e.target.reset();
                    document.getElementById('materialCourseId').value = courseId;
                    setTimeout(() => location.reload(), 800);
                } else if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message || 'Failed to add material.';
                    alertBox.classList.remove('d-none');
                }
            } catch (err) {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Network error.';
                    alertBox.classList.remove('d-none');
                }
            }
        });

        // ===== ASSIGNMENT MANAGEMENT =====
        document.getElementById('addAssignmentForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('assignmentAlert');
            alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/staff/add_assignment.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addAssignmentModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message;
                }
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('d-none');
            }
        });

        // ===== EDIT ASSIGNMENT =====
        document.querySelectorAll('.edit-assignment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editAssignmentId').value = btn.dataset.id;
                document.getElementById('editCourseId').value = btn.dataset.courseId;
                document.getElementById('editTitle').value = btn.dataset.title;
                document.getElementById('editDescription').value = btn.dataset.description;
                document.getElementById('editDueDate').value = btn.dataset.dueDate;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editAssignmentModal')).show();
            });
        });

        document.getElementById('editAssignmentForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('editAssignmentAlert');
            alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/staff/edit_assignment.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    alertBox.className = 'alert alert-success';
                    bootstrap.Modal.getInstance(document.getElementById('editAssignmentModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = result.message || 'Failed to update assignment.';
                }
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Network error. Please try again.';
                alertBox.classList.remove('d-none');
            }
        });

        document.querySelectorAll('.delete-assignment-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this assignment and all submissions?')) return;
                const id = btn.dataset.id;
                try {
                    const res = await fetch('backend/admin/delete_assignment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const result = await res.json();
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to delete assignment.');
                }
            });
        });

        document.querySelectorAll('.delete-submission-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this submission?')) return;
                const id = btn.dataset.id;
                try {
                    const res = await fetch('backend/admin/delete_submission.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const result = await res.json();
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to delete submission.');
                }
            });
        });

        // ===== PROFILE MANAGEMENT =====
        document.getElementById('profilePreview').addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
            modal.show();
        });

        document.getElementById('nameForm')?.addEventListener('submit', async (e) => {
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
                    alertBox.className = 'alert alert-success';
                } else {
                    alertBox.className = 'alert alert-danger';
                }
                alertBox.textContent = result.message;
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('d-none');
            }
        });

        document.getElementById('passwordForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('passwordAlert');
            alertBox.className = 'alert d-none';
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Passwords do not match.';
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
                    alertBox.className = 'alert alert-success';
                    e.target.reset();
                } else {
                    alertBox.className = 'alert alert-danger';
                }
                alertBox.textContent = result.message;
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('d-none');
            }
        });

        document.getElementById('uploadForm')?.addEventListener('submit', async (e) => {
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
                    alertBox.className = 'alert alert-success';
                    document.getElementById('profilePreview').src = 'uploads/profiles/' + result.filename + '?t=' + Date.now();
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                        e.target.reset();
                    }, 1500);
                } else {
                    alertBox.className = 'alert alert-danger';
                }
                alertBox.textContent = result.message;
                alertBox.classList.remove('d-none');
            } catch (err) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('d-none');
            }
        });
    </script>
</body>

</html>
