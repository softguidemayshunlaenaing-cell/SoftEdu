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
    <link rel="stylesheet" href="assets/css/dashboard-shared.css">
    <style>
    .profile-avatar-wrapper {
        margin-top: -60px;
        position: relative;
        display: inline-block;
        padding-left: 20px;
    }

    .btn-green:hover {
        background-color: #047857;
        transform: translateY(-1px);
    }

    .btn-green-sm:hover {
        background-color: #047857;
    }

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
</style>
</head>

<body>
    <!-- Navbar -->
    <?php
    $brandHref = 'dashboard.php';
    $brandText = 'SoftEdu Admin';
    $avatarFallback = 'https://via.placeholder.com/32?text=' . substr(htmlspecialchars($user['name']), 0, 1);
    ?>
    <?php include 'includes/dashboard_navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
            $currentPage = $_GET['page'] ?? 'applications';
            $sidebarWrapperClass = 'col-md-3 col-lg-2 sidebar bg-light p-3';
            $menu = [
                ['key' => 'profile', 'label' => 'My Profile', 'icon' => 'user-circle', 'href' => '?page=profile'],
                ['key' => 'applications', 'label' => 'Applications', 'icon' => 'file-alt', 'href' => '?page=applications'],
                ['key' => 'courses', 'label' => 'Courses', 'icon' => 'book', 'href' => '?page=courses'],
                ['key' => 'materials', 'label' => 'Materials', 'icon' => 'file-pdf', 'href' => '?page=materials'],
                ['key' => 'assignments', 'label' => 'Assignments', 'icon' => 'tasks', 'href' => '?page=assignments'],
                ['key' => 'users', 'label' => 'Users', 'icon' => 'users', 'href' => '?page=users'],
                ['key' => 'staff', 'label' => 'Staff', 'icon' => 'chalkboard-teacher', 'href' => '?page=staff'],
            ];
            ?>
            <?php include 'includes/dashboard_sidebar.php'; ?>

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

                    <div class="table-responsive table-fixed-rows">
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
                                $perPage = 10;
                                $pageNum = max(1, (int) ($_GET['app_page'] ?? 1));
                                $offset = ($pageNum - 1) * $perPage;

                                $countSql = "SELECT COUNT(*) FROM softedu_applications WHERE 1=1";
                                $countParams = $params;
                                if (!empty($_GET['status'])) {
                                    $countSql .= " AND status = ?";
                                }
                                if (!empty($_GET['start_date'])) {
                                    $countSql .= " AND DATE(created_at) >= ?";
                                }
                                if (!empty($_GET['end_date'])) {
                                    $countSql .= " AND DATE(created_at) <= ?";
                                }
                                $countStmt = $db->prepare($countSql);
                                $countStmt->execute($countParams);
                                $totalRows = (int) $countStmt->fetchColumn();
                                $totalPages = max(1, (int) ceil($totalRows / $perPage));

                                $sql .= " ORDER BY created_at ASC LIMIT $perPage OFFSET $offset";
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                $rowCount = 0;
                                while ($app = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    $rowCount++;
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
                                <?php for ($i = $rowCount; $i < $perPage; $i++): ?>
                                    <tr class="empty-row">
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-wrap">
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Applications pagination">
                                <ul class="pagination">
                                    <?php
                                    $prevPage = max(1, $pageNum - 1);
                                    $nextPage = min($totalPages, $pageNum + 1);
                                    $baseParams = $_GET;
                                    $baseParams['page'] = 'applications';
                                    ?>
                                    <li class="page-item <?= $pageNum <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['app_page' => $prevPage]))) ?>">
                                            Previous
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $pageNum ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['app_page' => $i]))) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pageNum >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['app_page' => $nextPage]))) ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                        <?php elseif (($_GET['page'] ?? '') === 'profile'): ?>
                            <?php
                            $profileBtnClass = 'btn btn-success';
                            ?>
                            <?php include 'includes/profile_panel.php'; ?>

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
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
            <i class="fas fa-plus me-1"></i> Add New Assignment
        </button>
        
        <!-- ✁EEXPORT BUTTON -->
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
    
    <!-- ✁EADD EDIT BUTTON -->
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
                    <div class="table-responsive table-fixed-rows">
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
                                            <a href="<?= htmlspecialchars($sub['file_path']) ?>"
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
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#courseModal">
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
                                $stmt = $db->query("SELECT * FROM softedu_courses ORDER BY created_at ASC");
                                while ($course = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($course['image']): ?>
                                                <img src="uploads/courses/<?= htmlspecialchars($course['image']) ?>" width="60"
                                                    class="rounded">
                                            <?php else: ?>
                                                <span class="text-muted small">No Image</span>
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
                                        <?php echo htmlspecialchars($c['title']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
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

                                $sql .= " ORDER BY m.created_at ASC LIMIT $perPage OFFSET $offset";
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
                                        <td><a href="<?= htmlspecialchars($mat['material_url']) ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary">View</a></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger delete-material-btn"
                                                data-id="<?= $mat['id'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php for ($i = $rowCount; $i < $perPage; $i++): ?>
                                    <tr class="empty-row">
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
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
                                            href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['materials_page' => $prevPage]))) ?>">
                                            Previous
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $pageNum ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['materials_page' => $i]))) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pageNum >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['materials_page' => $nextPage]))) ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
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
                                $perPage = 10;
                                $pageNum = max(1, (int) ($_GET['users_page'] ?? 1));
                                $offset = ($pageNum - 1) * $perPage;

                                $countSql = "SELECT COUNT(*) FROM softedu_users WHERE 1=1";
                                $countParams = $params;
                                if (!empty($_GET['role'])) {
                                    $countSql .= " AND role = ?";
                                }
                                if (!empty($_GET['start_date'])) {
                                    $countSql .= " AND DATE(created_at) >= ?";
                                }
                                if (!empty($_GET['end_date'])) {
                                    $countSql .= " AND DATE(created_at) <= ?";
                                }
                                $countStmt = $db->prepare($countSql);
                                $countStmt->execute($countParams);
                                $totalRows = (int) $countStmt->fetchColumn();
                                $totalPages = max(1, (int) ceil($totalRows / $perPage));

                                $sql .= " ORDER BY created_at ASC LIMIT $perPage OFFSET $offset";
                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                $rowCount = 0;
                                while ($userRow = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    $rowCount++;
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
                                            <?php if (($userRow['role'] ?? '') === 'student'): ?>
                                                <button class="btn btn-sm btn-outline-primary view-docs-btn"
                                                    data-user-id="<?= $userRow['id'] ?>">View Docs</button>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php for ($i = $rowCount; $i < $perPage; $i++): ?>
                                    <tr class="empty-row">
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                        <td class="empty-cell"></td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-wrap">
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Users pagination">
                                <ul class="pagination">
                                    <?php
                                    $prevPage = max(1, $pageNum - 1);
                                    $nextPage = min($totalPages, $pageNum + 1);
                                    $baseParams = $_GET;
                                    $baseParams['page'] = 'users';
                                    ?>
                                    <li class="page-item <?= $pageNum <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['users_page' => $prevPage]))) ?>">
                                            Previous
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $pageNum ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['users_page' => $i]))) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $pageNum >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= htmlspecialchars(http_build_query(array_merge($baseParams, ['users_page' => $nextPage]))) ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>

                <?php elseif ($_GET['page'] === 'staff'): ?>
                    <!-- Staff Management -->
                    <h2 class="mb-4">Manage Staff</h2>
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addStaffModal">
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
    <?php
    $quickAddSuccess = 'refreshModal';
    ?>
    <?php include 'modals/generated_email_modal.php'; ?>
    <?php include 'modals/user_docs_modal.php'; ?>
    <?php include 'modals/course_modal.php'; ?>
    <?php include 'modals/materials_modal.php'; ?>
    <?php include 'modals/add_staff_modal.php'; ?>
    <?php include 'modals/add_material_modal.php'; ?>
    <?php include 'modals/add_assignment_modal.php'; ?>
    <?php include 'modals/edit_assignment_modal.php'; ?>
    <?php
    $uploadBtnClass = 'btn btn-success';
    ?>
    <?php include 'includes/profile_upload_modal.php'; ?>

    <?php include 'includes/scripts.php'; ?>
    <script src="assets/js/dashboard-common.js"></script>
    <!-- ✁EFIXED: Use CORRECT jsPDF legacy build -->
    <script src="https://unpkg.com/jspdf@2.5.1/dist/jspdf.legacy.min.js"></script>
    <script>
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
                    const confirmBtnEl = document.getElementById('confirmApproveBtn');
                    const originalText = confirmBtnEl.textContent;
                    confirmBtnEl.disabled = true;
                    confirmBtnEl.setAttribute('aria-busy', 'true');
                    confirmBtnEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Approving...';
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
                    } finally {
                        confirmBtnEl.disabled = false;
                        confirmBtnEl.removeAttribute('aria-busy');
                        confirmBtnEl.textContent = originalText;
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
                        // Helper to detect or load jsPDF and return constructor
                        async function getJsPDFCtor() {
                            const checkGlobals = () => {
                                if (typeof window.jsPDF === 'function') return window.jsPDF;
                                if (window.jspdf && typeof window.jspdf.jsPDF === 'function') return window.jspdf.jsPDF;
                                return null;
                            };

                            let ctor = checkGlobals();
                            if (ctor) return ctor;

                            const urls = [
                                'https://unpkg.com/jspdf@2.5.1/dist/jspdf.legacy.min.js',
                                'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.legacy.min.js',
                                'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'
                            ];

                            for (const url of urls) {
                                try {
                                    // Skip adding if already attempted
                                    if (!document.querySelector(`script[src="${url}"]`)) {
                                        await new Promise((resolve, reject) => {
                                            const s = document.createElement('script');
                                            s.src = url;
                                            s.async = true;
                                            s.onload = resolve;
                                            s.onerror = () => reject(new Error('Failed to load ' + url));
                                            document.head.appendChild(s);
                                        });
                                    }
                                    // Wait briefly for UMD to initialize
                                    await new Promise(r => setTimeout(r, 120));
                                    ctor = checkGlobals();
                                    if (ctor) return ctor;
                                } catch (err) {
                                    console.warn('jsPDF load failed for', url, err);
                                    // try next URL
                                }
                            }

                            throw new Error('jsPDF not available');
                        }

                        let jsPDFCtor;
                        try {
                            jsPDFCtor = await getJsPDFCtor();
                        } catch (err) {
                            console.error('PDF init error:', err);
                            alert('PDF library failed to load. See console for details.');
                            return;
                        }

                        const pdf = new jsPDFCtor('p', 'mm', 'a4');
                        const docsList = await fetch(`backend/user/get_user_docs.php?user_id=${userId}`).then(r => r.json());
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

    const form = e.target;
    const formData = new FormData(form);
    const alertBox = document.getElementById('assignmentAlert');
    alertBox.className = 'alert d-none';

    const courseId = formData.get('course_id');
    const title = formData.get('title')?.trim();
    const dueDate = formData.get('due_date');

    // Validate required fields (file is optional)
    if (!courseId || courseId === '0' || !title || !dueDate) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Please fill in all required fields.';
        alertBox.classList.remove('d-none');
        return;
    }

    try {
        const res = await fetch('backend/staff/add_assignment.php', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('addAssignmentModal')).hide();
            alertBox.className = 'alert alert-success';
            alertBox.textContent = 'Assignment added successfully!';
            alertBox.classList.remove('d-none');
            setTimeout(() => location.reload(), 1000);
        } else {
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = result.message || 'Failed to add assignment.';
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        console.error('Assignment submission error:', err);
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network error. Please try again.';
        alertBox.classList.remove('d-none');
    }
});

    </script>
</body>

</html>


