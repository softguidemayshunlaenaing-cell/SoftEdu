<?php
// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/backend/config/db.php';
$database = new Database();
$db = $database->getConnection();

// Fetch user data
$stmt = $db->prepare("SELECT name, profile_image FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get dynamic stats from database
$courseCount = 0;
$assignmentCount = 0;

// Count active courses
$courseStmt = $db->query("SELECT COUNT(*) FROM softedu_courses WHERE status = 'active'");
$courseCount = $courseStmt->fetchColumn();

// Count assignments NOT submitted by this student
$assignStmt = $db->prepare("
    SELECT COUNT(*) 
    FROM softedu_assignments a
    LEFT JOIN softedu_assignment_submissions s 
        ON a.id = s.assignment_id AND s.student_id = ?
    WHERE s.id IS NULL
");
$assignStmt->execute([$_SESSION['user_id']]);
$assignmentCount = $assignStmt->fetchColumn();

$page = $_GET['page'] ?? 'courses';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Workspace | SoftEdu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-green: #28a745;
            --bg-gray: #f8f9fa;
            --navbar-bg: lightgray;
        }

        body {
            background-color: var(--bg-gray);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            padding-top: 80px;
        }

        .navbar {
            background-color: var(--navbar-bg) !important;
            padding: 0.75rem 1rem;
        }

        .sidebar {
            background: white;
            min-height: calc(100vh - 80px);
            border-right: 1px solid #e2e8f0;
            padding: 20px;
        }

        .side-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #64748b;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .side-link:hover,
        .side-link.active {
            background: #ecfdf5;
            color: var(--primary-green);
            font-weight: 600;
        }

        .side-link i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--primary-green) 0%, #10b981 100%);
            color: white;
            padding: 60px 0;
            border-radius: 0 0 20px 20px;
            margin-bottom: 30px;
        }

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                <div class="sidebar position-fixed" style="width: inherit;">
                    <small class="text-uppercase text-muted fw-bold mb-3 d-block">Learning</small>
                    <a href="?page=courses" class="side-link <?= $page == 'courses' ? 'active' : '' ?>">
                        <i class="bi bi-book"></i> My Courses
                    </a>
                    <a href="?page=assignments" class="side-link <?= $page == 'assignments' ? 'active' : '' ?>">
                        <i class="bi bi-pencil-square"></i> Assignments
                    </a>
                    <a href="?page=certificate" class="side-link <?= $page == 'certificate' ? 'active' : '' ?>">
                        <i class="bi bi-award"></i> Certificate
                    </a>
                    <hr>
                    <a href="profile.php" class="side-link">
                        <i class="bi bi-person-circle"></i> My Profile
                    </a>
                    <a href="backend/auth/logout.php" class="side-link">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <main class="col-lg-10 offset-lg-2 p-4">
                <div class="container">
                    <?php if ($page == 'courses'): ?>
                        <h2 class="fw-bold mb-4">My Enrolled Courses</h2>
                        <?php
                        // Fetch courses with materials
                        $stmt = $db->prepare("
                                                        SELECT 
                                                            c.id,
                                                            c.title,
                                                            c.description,
                                                            COUNT(m.id) AS total_lectures,
                                                            COUNT(mp.id) AS completed_lectures
                                                        FROM softedu_courses c
                                                        LEFT JOIN softedu_course_materials m 
                                                            ON c.id = m.course_id
                                                        LEFT JOIN softedu_material_progress mp 
                                                            ON m.id = mp.material_id 
                                                            AND mp.student_id = ?
                                                        WHERE c.status = 'active'
                                                        GROUP BY c.id
                                                        ORDER BY c.created_at ASC
                                                    ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($courses)): ?>
                            <div class="alert alert-info">No courses available yet.</div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php
                                // Prepare the statement for fetching lectures for each course
                                $materialsStmt = $db->prepare("
    SELECT m.*, 
    CASE WHEN mp.id IS NOT NULL THEN 1 ELSE 0 END AS completed
    FROM softedu_course_materials m
    LEFT JOIN softedu_material_progress mp 
        ON m.id = mp.material_id 
        AND mp.student_id = ?
    WHERE m.course_id = ?
    ORDER BY m.id ASC
");

                                foreach ($courses as $course):
                                    // Calculate course progress
                                    $progress = $course['total_lectures'] > 0
                                        ? round(($course['completed_lectures'] / $course['total_lectures']) * 100)
                                        : 0;

                                    // Fetch all lectures for this course
                                    $materialsStmt->execute([$_SESSION['user_id'], $course['id']]);
                                    $materials = $materialsStmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold">
                                                    <?= htmlspecialchars($course['title']) ?>
                                                </h5>

                                                <!-- Progress bar -->
                                                <div class="mb-3">
                                                    <small class="text-muted">Progress:
                                                        <?= $progress ?>%
                                                    </small>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                                                    </div>
                                                </div>

                                                <!-- Lectures list -->
                                                <?php if ($materials): ?>
                                                    <ul class="list-group list-group-flush">
                                                        <?php foreach ($materials as $m): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <a href="<?= htmlspecialchars($m['material_url']) ?>" target="_blank">
                                                                    <?= htmlspecialchars($m['title']) ?>
                                                                </a>
                                                                <?php if ($m['completed']): ?>
                                                                    <span class="badge bg-success">✓</span>
                                                                <?php else: ?>
                                                                    <form method="POST" class="mark-done-form">
                                                                        <input type="hidden" name="material_id" value="<?= $m['id'] ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-success">Mark
                                                                            Done</button>
                                                                    </form>

                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <p class="text-muted small">No lectures yet.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>

                    <?php elseif ($page == 'assignments'): ?>
                        <h2 class="fw-bold mb-4">Assignments & Tasks</h2>
                        <?php
                        // Fetch assignments with submission status
                        $stmt = $db->prepare("
                            SELECT a.*, c.title as course_title,
                            CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END as submitted
                            FROM softedu_assignments a
                            JOIN softedu_courses c ON a.course_id = c.id
                            LEFT JOIN softedu_assignment_submissions s 
                                ON a.id = s.assignment_id AND s.student_id = ?
                            ORDER BY a.due_date DESC
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($assignments)): ?>
                            <div class="alert alert-info">No assignments available yet.</div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($assignments as $assign): ?>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($assign['title']) ?></h5>
                                                <p class="text-muted"><strong>Course:</strong>
                                                    <?= htmlspecialchars($assign['course_title']) ?></p>
                                                <p><strong>Due Date:</strong> <?= date('M j, Y', strtotime($assign['due_date'])) ?>
                                                </p>

                                                <?php if ($assign['submitted']): ?>
                                                    <div class="alert alert-success">
                                                        <strong>✅ Submitted!</strong>
                                                    </div>
                                                <?php else: ?>
                                                    <form method="POST" action="backend/user/submit_assignment.php"
                                                        enctype="multipart/form-data">
                                                        <input type="hidden" name="assignment_id" value="<?= $assign['id'] ?>">
                                                        <div class="mb-2">
                                                            <label class="form-label">Upload Solution</label>
                                                            <input type="file" class="form-control" name="solution_file"
                                                                accept=".pdf,.doc,.docx,.php,.zip" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-success">Submit Assignment</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($page == 'certificate'): ?>
                        <h2 class="fw-bold mb-4">Course Certification</h2>
                        <div class="card main-card p-5 text-center">
                            <h4>Ready for your certificate?</h4>
                            <p class="text-muted">You must complete all course modules before applying.</p>
                            <button class="btn btn-success rounded-pill px-5 mt-3" disabled>Apply Now</button>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Welcome Back Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="bg-gradient"
                    style="background: linear-gradient(135deg, #28a745 0%, #10b981 100%); padding: 2rem; text-align: center; color: white;">
                    <div class="avatar-placeholder mb-3"
                        style="width: 80px; height: 80px; margin: 0 auto; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <?= substr(htmlspecialchars($user['name']), 0, 1) ?>
                    </div>
                    <h3 class="mb-2" style="font-weight: 700; letter-spacing: 0.5px;">Welcome Back!</h3>
                    <p class="mb-0 opacity-90" style="font-size: 1.1rem;"><?= htmlspecialchars($user['name']) ?></p>
                </div>

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 bg-light rounded-circle mb-3">
                            <i class="bi bi-rocket-takeoff-fill text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Your Learning Journey Awaits!</h5>
                        <p class="text-muted mb-4">
                            You have <span class="fw-bold text-primary"><?= $courseCount ?></span> active courses<br>
                            and <span class="fw-bold text-danger"><?= $assignmentCount ?></span> pending assignments
                        </p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="?page=courses" class="btn btn-success btn-lg rounded-pill py-3 fw-bold">
                            <i class="bi bi-play-circle me-2"></i>Resume Learning
                        </a>
                        <a href="?page=assignments" class="btn btn-outline-success btn-lg rounded-pill py-3 fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>View Assignments
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">✨ Keep up the great work!</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (isset($_SESSION['show_welcome']) && $_SESSION['show_welcome']): ?>
            document.addEventListener('DOMContentLoaded', function () {
                const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                welcomeModal.show();

                // Clear session flag
                fetch('backend/auth/clear_welcome_flag.php')
                    .then(() => console.log('Welcome flag cleared'));
            });
        <?php endif; ?>
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Attach event listener to all mark done forms
            document.querySelectorAll('form.mark-done-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // prevent page reload

                    const formData = new FormData(this);
                    const btn = this.querySelector('button');
                    const materialItem = this.closest('li'); // li containing material

                    // Disable button while processing
                    btn.disabled = true;
                    btn.textContent = 'Marking...';

                    fetch('backend/user/mark_completed_ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Update progress bar
                                const card = materialItem.closest('.card-body');
                                const progressBar = card.querySelector('.progress-bar');
                                const progressText = card.querySelector('small');

                                if (progressBar && data.progress !== undefined) {
                                    progressBar.style.width = data.progress + '%';
                                    progressBar.innerText = data.progress + '%';
                                    progressText.textContent = 'Progress: ' + data.progress + '%';
                                }

                                // Change button to ✓
                                btn.outerHTML = '<span class="badge bg-success">✓</span>';
                            } else {
                                alert('Error: ' + (data.message || 'Something went wrong!'));
                                btn.disabled = false;
                                btn.textContent = 'Mark Done';
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error marking as completed.');
                            btn.disabled = false;
                            btn.textContent = 'Mark Done';
                        });
                });
            });
        });
    </script>


</body>

</html>