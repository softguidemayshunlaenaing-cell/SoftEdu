<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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
    <link rel="stylesheet" href="assets/css/dashboard-shared.css">
</head>

<body>
    <!-- Navbar -->
    <?php
    $brandHref = 'staff_dashboard.php';
    $brandText = 'SoftEdu Staff';
    $avatarFallback = 'https://ui-avatars.com/api/?name=' . substr(htmlspecialchars($user['name']), 0, 1) . '&background=0d6efd&color=fff';
    ?>
    <?php include 'includes/dashboard_navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php
            $currentPage = $_GET['page'] ?? 'courses';
            $sidebarWrapperClass = 'col-md-3 col-lg-2 sidebar';
            $menu = [
                ['key' => 'profile', 'label' => 'My Profile', 'icon' => 'user-circle', 'href' => 'staff_dashboard.php?page=profile'],
                ['key' => 'courses', 'label' => 'Courses', 'icon' => 'book', 'href' => 'staff_dashboard.php?page=courses'],
                ['key' => 'materials', 'label' => 'Materials', 'icon' => 'file-video', 'href' => 'staff_dashboard.php?page=materials'],
                ['key' => 'assignments', 'label' => 'Assignments', 'icon' => 'tasks', 'href' => 'staff_dashboard.php?page=assignments'],
            ];
            ?>
            <?php include 'includes/dashboard_sidebar.php'; ?>

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
                    <?php
                    $profileBtnClass = 'btn btn-green';
                    ?>
                    <?php include 'includes/profile_panel.php'; ?>

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
                                            <a href="<?= htmlspecialchars($sub['file_path']) ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary">View File</a>
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
    <?php
    $quickAddSuccess = 'reload';
    $addMaterialRedirect = 'staff_dashboard.php?page=materials';
    ?>
    <?php include 'modals/course_modal.php'; ?>
    <?php include 'modals/materials_modal.php'; ?>
    <?php include 'modals/add_material_modal.php'; ?>
    <?php include 'modals/add_assignment_modal.php'; ?>
    <?php include 'modals/edit_assignment_modal.php'; ?>

    <?php
    $uploadBtnClass = 'btn btn-green';
    ?>
    <?php include 'includes/profile_upload_modal.php'; ?>

    <?php include 'includes/scripts.php'; ?>
    <script src="assets/js/dashboard-common.js"></script>
    <script>
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
    </script>
</body>

</html>