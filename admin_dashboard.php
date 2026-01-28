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

        /* PDF Preview */
        .pdf-preview {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-y: auto;
            max-height: 80vh;
        }

        .pdf-page img {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            border-radius: 3px;
            max-width: 100%;
            display: block;
            margin: auto;
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
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'assignments' ? 'active' : '' ?>"
                            href="?page=assignments">
                            <i class="fas fa-tasks me-2"></i>Assignments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'users' ? 'active' : '' ?>" href="?page=users">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'staff' ? 'active' : '' ?>" href="?page=staff">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Staff
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
<?php elseif ($_GET['page'] === 'assignments'): ?>
    <h2 class="mb-4">Manage Assignments</h2>
    
   <!-- Filter Form -->
<form method="GET" class="row g-3 mb-4">
    <input type="hidden" name="page" value="assignments">
    
    <!-- Course Filter -->
    <div class="col-md-3">
        <label class="form-label">Filter by Course</label>
        <select name="course_id" class="form-select" onchange="this.form.submit()">
            <option value="">All Courses</option>
            <?php
            $courseStmt = $db->query("SELECT id, title FROM softedu_courses ORDER BY title");
            while ($c = $courseStmt->fetch(PDO::FETCH_ASSOC)):
                $selected = (isset($_GET['course_id']) && $_GET['course_id'] == $c['id']) ? 'selected' : '';
            ?>
                <option value="<?= $c['id'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($c['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <!-- Student Filter -->
    <div class="col-md-3">
        <label class="form-label">Filter by Student</label>
        <select name="student_id" class="form-select" onchange="this.form.submit()">
            <option value="">All Students</option>
            <?php
            $studentStmt = $db->query("
                SELECT DISTINCT u.id, u.name 
                FROM softedu_users u
                JOIN softedu_assignment_submissions s ON u.id = s.student_id
                ORDER BY u.name
            ");
            while ($s = $studentStmt->fetch(PDO::FETCH_ASSOC)):
                $selected = (isset($_GET['student_id']) && $_GET['student_id'] == $s['id']) ? 'selected' : '';
            ?>
                <option value="<?= $s['id'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($s['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    
    <!-- Export & Reset -->
    <div class="col-md-6 d-flex align-items-end gap-2">
        <button type="button" class="btn btn-softedu me-2" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
            <i class="fas fa-plus me-1"></i> Add New Assignment
        </button>
        
        <!-- ✅ EXPORT BUTTON -->
        <a href="backend/export/assignments_csv.php?<?= http_build_query($_GET) ?>" 
           class="btn btn-outline-success">
            <i class="fas fa-file-csv me-1"></i> Export to CSV
        </a>
        
        <?php if (!empty($_GET['course_id']) || !empty($_GET['student_id'])): ?>
            <a href="?page=assignments" class="btn btn-outline-secondary">Reset Filters</a>
        <?php endif; ?>
    </div>
</form>
    <!-- Assignments Table -->
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
                // Build dynamic query
                $sql = "
                    SELECT a.*, c.title as course_title
                    FROM softedu_assignments a
                    JOIN softedu_courses c ON a.course_id = c.id
                    WHERE 1=1
                ";
                $params = [];
                
                // Apply course filter
                if (!empty($_GET['course_id'])) {
                    $sql .= " AND a.course_id = ?";
                    $params[] = (int)$_GET['course_id'];
                }
                
                // Apply student filter (only show assignments with submissions from this student)
                if (!empty($_GET['student_id'])) {
                    $sql .= " AND a.id IN (
                        SELECT assignment_id FROM softedu_assignment_submissions 
                        WHERE student_id = ?
                    )";
                    $params[] = (int)$_GET['student_id'];
                }
                
                $sql .= " ORDER BY a.due_date DESC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                while ($assign = $stmt->fetch(PDO::FETCH_ASSOC)):
                    // Count submissions (optionally filtered by student)
                    $subSql = "SELECT COUNT(*) FROM softedu_assignment_submissions WHERE assignment_id = ?";
                    $subParams = [$assign['id']];
                    
                    if (!empty($_GET['student_id'])) {
                        $subSql .= " AND student_id = ?";
                        $subParams[] = (int)$_GET['student_id'];
                    }
                    
                    $subStmt = $db->prepare($subSql);
                    $subStmt->execute($subParams);
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
    <a href="?page=assignment_submissions&assignment_id=<?= $assign['id'] ?>" 
       class="btn btn-sm btn-outline-primary">View Submissions</a>
    
    <!-- ✅ ADD EDIT BUTTON -->
    <button class="btn btn-sm btn-outline-warning edit-assignment-btn" 
            data-id="<?= $assign['id'] ?>"
            data-title="<?= htmlspecialchars($assign['title']) ?>"
            data-description="<?= htmlspecialchars($assign['description']) ?>"
            data-due-date="<?= date('Y-m-d\TH:i', strtotime($assign['due_date'])) ?>"
            data-course-id="<?= $assign['course_id'] ?>">
        Edit
    </button>
    
    <button class="btn btn-sm btn-danger delete-assignment-btn" data-id="<?= $assign['id'] ?>">Delete</button>
</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


                <?php elseif ($_GET['page'] === 'assignment_submissions'): ?>
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
                    <a href="?page=assignments" class="btn btn-secondary mb-3">← Back to Assignments</a>
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

                <?php elseif ($_GET['page'] === 'staff'): ?>
                    <!-- Staff Management -->
                    <h2 class="mb-4">Manage Staff</h2>
                    <button class="btn btn-softedu mb-3" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i class="fas fa-plus me-1"></i> Add New Staff
                    </button>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->prepare("
                                    SELECT id, name, email, role, profile_image, created_at
                                    FROM softedu_users
                                    WHERE role = 'staff'
                                    ORDER BY created_at DESC
                                ");
                                $stmt->execute();
                                while ($staff = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="<?= !empty($staff['profile_image'])
                                                ? 'uploads/profiles/' . htmlspecialchars($staff['profile_image'])
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($staff['name']) . '&background=0d6efd&color=fff&size=128'
                                                ?>" width="40" class="rounded" alt="Staff">
                                        </td>
                                        <td><?= htmlspecialchars($staff['name']) ?></td>
                                        <td><?= htmlspecialchars($staff['email']) ?></td>
                                        <td><span class="badge bg-info">Staff</span></td>
                                        <td><?= date('M j, Y', strtotime($staff['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger delete-staff-btn"
                                                data-id="<?= $staff['id'] ?>">
                                                Delete
                                            </button>
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
    <?php include 'modals/generated_email_modal.php'; ?>
    <?php include 'modals/user_docs_modal.php'; ?>
    <?php include 'modals/course_modal.php'; ?>
    <?php include 'modals/materials_modal.php'; ?>
    <?php include 'modals/add_staff_modal.php'; ?>
    <?php include 'modals/add_material_modal.php'; ?>
    <?php include 'modals/add_assignment_modal.php'; ?>
    <?php include 'modals/edit_assignment_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ✅ FIXED: Use CORRECT jsPDF legacy build -->
    <script src="https://unpkg.com/jspdf@2.5.1/dist/jspdf.legacy.min.js"></script>
    <script>
        // ===== ASSIGNMENT MANAGEMENT =====
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
// ===== EDIT ASSIGNMENT =====
document.querySelectorAll('.edit-assignment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Populate modal with assignment data
        document.getElementById('editAssignmentId').value = btn.dataset.id;
        document.getElementById('editCourseId').value = btn.dataset.courseId;
        document.getElementById('editTitle').value = btn.dataset.title;
        document.getElementById('editDescription').value = btn.dataset.description;
        document.getElementById('editDueDate').value = btn.dataset.dueDate;
        
        // Show modal
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editAssignmentModal')).show();
    });
});

// Handle form submission
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
        // ===== APPROVE / REJECT APPLICATIONS =====
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                const userName = btn.closest('tr').querySelector('td').textContent || 'User';
                const norm = userName.toLowerCase().replace(/[^a-z0-9]/g, '');
                const defaultEmail = `softedu.${norm}@gmail.com`;

                const modalEl = document.getElementById('generatedEmailModal');
                const modal = new bootstrap.Modal(modalEl);
                const emailInput = document.getElementById('generatedEmailInput');
                const emailAlert = document.getElementById('emailAlert');
                emailInput.value = defaultEmail;
                emailAlert.classList.add('d-none');
                modal.show();

                const confirmBtn = document.getElementById('confirmApproveBtn');
                confirmBtn.replaceWith(confirmBtn.cloneNode(true));
                document.getElementById('confirmApproveBtn').addEventListener('click', async () => {
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

        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                if (!confirm('Reject this application?')) return;
                try {
                    const res = await fetch('backend/admin/approve_application.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, action: 'reject' })
                    });
                    const result = await res.json();
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to reject application.');
                }
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
                img.src = url + '?t=' + Date.now();
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
                        alert(`${userName} has not uploaded any documents.`);
                        return;
                    }
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

                    document.getElementById('downloadPdfBtn').addEventListener('click', async () => {
                        // ✅ CORRECT jsPDF ACCESS
                        if (typeof window.jsPDF !== 'function') {
                            alert('PDF library failed to load. Please refresh the page.');
                            return;
                        }
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        const docs = await fetch(`backend/user/get_user_docs.php?user_id=${userId}`).then(r => r.json());
                        if (!docs || docs.length === 0) {
                            alert('No documents to download.');
                            return;
                        }
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        for (let i = 0; i < docs.length; i++) {
                            try {
                                const imgData = await loadImageAsBase64(docs[i].file_url);
                                const img = new Image();
                                img.src = imgData;
                                await new Promise(resolve => img.onload = resolve);
                                let width = pageWidth;
                                let height = (img.height * width) / img.width;
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

        // ===== STAFF MANAGEMENT =====
        document.getElementById('addStaffForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('addStaffAlert');
            alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/admin/add_staff.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    alertBox.className = 'alert alert-success';
                    bootstrap.Modal.getInstance(document.getElementById('addStaffModal')).hide();
                    setTimeout(() => location.reload(), 1000);
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

        document.querySelectorAll('.delete-staff-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this staff member?')) return;
                const id = btn.dataset.id;
                try {
                    const res = await fetch('backend/admin/delete_user.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, role: 'staff' })
                    });
                    const result = await res.json();
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to delete staff.');
                }
            });
        });
        // ===== ADD ASSIGNMENT HANDLER =====
document.getElementById('addAssignmentForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(e.target);
    const courseId = formData.get('course_id');
    const title = formData.get('title');
    const dueDate = formData.get('due_date');

    // Validate required fields
    if (!courseId || !title || !dueDate) {
        const alertBox = document.getElementById('assignmentAlert');
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'All fields are required.';
        alertBox.classList.remove('d-none');
        return;
    }

    // Send to backend
    try {
        const res = await fetch('backend/staff/add_assignment.php', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();

        if (result.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addAssignmentModal')).hide();
            
            // Show success & reload
            const alertBox = document.getElementById('assignmentAlert');
            alertBox.className = 'alert alert-success';
            alertBox.textContent = 'Assignment added successfully!';
            alertBox.classList.remove('d-none');
            
            // Reload assignments table after 1 second
            setTimeout(() => location.reload(), 1000);
        } else {
            // Show error
            const alertBox = document.getElementById('assignmentAlert');
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = result.message || 'Failed to add assignment.';
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        console.error('Assignment submission error:', err);
        const alertBox = document.getElementById('assignmentAlert');
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network error. Please try again.';
        alertBox.classList.remove('d-none');
    }
});
    </script>
</body>

</html>