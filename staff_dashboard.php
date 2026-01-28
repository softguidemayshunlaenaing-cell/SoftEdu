<?php
// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'staff') {
    header('Location: index.php');
    exit;
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
    <style>
        .sidebar {
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
        }

        .nav-link.active {
            background-color: #d4edda;
            color: #155724;
        }

        .btn-softedu {
            background-color: #28a745;
            border-color: #28a745;
        }

        .card-img-top {
            object-fit: cover;
            height: 180px;
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
                        <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] === 'courses') ? 'active' : '' ?>"
                            href="?page=courses">
                            <i class="fas fa-book me-2"></i>Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'materials' ? 'active' : '' ?>"
                            href="?page=materials">
                            <i class="fas fa-file-video me-2"></i>Materials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'assignments' ? 'active' : '' ?>"
                            href="?page=assignments">
                            <i class="fas fa-tasks me-2"></i>Assignments
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <?php if (!isset($_GET['page']) || $_GET['page'] === 'courses'): ?>
                    <!-- COURSES TAB -->
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
                    <!-- MATERIALS TAB -->
                    <h2 class="mb-4">Manage Materials</h2>

                    <!-- Filter Form -->
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
                                    <option value="<?= $c['id'] ?>" <?= $selected ?>>
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
                                <a href="?page=materials" class="btn btn-outline-secondary">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Materials Table -->
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
                                // Build dynamic query
                                $sql = "
                    SELECT m.*, c.title as course_title
                    FROM softedu_course_materials m
                    JOIN softedu_courses c ON m.course_id = c.id
                    WHERE 1=1
                ";
                                $params = [];
                                if (!empty($_GET['course_id'])) {
                                    $sql .= " AND m.course_id = ?";
                                    $params[] = (int) $_GET['course_id'];
                                }
                                $sql .= " ORDER BY m.created_at DESC";

                                $stmt = $db->prepare($sql);
                                $stmt->execute($params);
                                while ($mat = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($mat['course_title']) ?></td>
                                        <td><?= htmlspecialchars($mat['title']) ?></td>
                                        <td><?= ucfirst($mat['material_type']) ?></td>
                                        <td><?= ucfirst(str_replace('_', ' ', $mat['source'])) ?></td>
                                        <td>
                                            <a href="<?= htmlspecialchars($mat['material_url']) ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger delete-material-btn"
                                                data-id="<?= $mat['id'] ?>">
                                                Delete
                                            </button>
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
                            <button type="button" class="btn btn-softedu me-2" data-bs-toggle="modal"
                                data-bs-target="#addAssignmentModal">
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
                                    $params[] = (int) $_GET['course_id'];
                                }

                                // Apply student filter (only show assignments with submissions from this student)
                                if (!empty($_GET['student_id'])) {
                                    $sql .= " AND a.id IN (
                        SELECT assignment_id FROM softedu_assignment_submissions 
                        WHERE student_id = ?
                    )";
                                    $params[] = (int) $_GET['student_id'];
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
                                        $subParams[] = (int) $_GET['student_id'];
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
                    btn.closest('tr').remove();
                } catch (err) {
                    alert('Failed to delete material.');
                }
            });
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
    </script>
</body>

</html>