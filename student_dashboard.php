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
$stmt = $db->prepare("SELECT name, email, role, profile_image FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: index.php?error=Invalid session.');
    exit;
}

// Fetch courses with materials
$stmt = $db->prepare("
    SELECT c.*, m.title as material_title, m.material_url, m.source
    FROM softedu_courses c
    LEFT JOIN softedu_course_materials m ON c.id = m.course_id
    WHERE c.status = 'active'
    ORDER BY c.created_at DESC
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group materials by course
$grouped = [];
foreach ($courses as $row) {
    $id = $row['id'];
    if (!isset($grouped[$id])) {
        $grouped[$id] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'duration' => $row['duration'],
            'fee' => $row['fee'],
            'image' => $row['image'],
            'materials' => []
        ];
    }
    if ($row['material_title']) {
        $grouped[$id]['materials'][] = [
            'title' => $row['material_title'],
            'url' => $row['material_url'],
            'source' => $row['source']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | SoftEdu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --softedu-green: #28a745;
            --softedu-green-light: #d4edda;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar {
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
        }

        .nav-link.active {
            background-color: var(--softedu-green-light);
            color: #155724;
            font-weight: 600;
        }

        .btn-softedu {
            background-color: var(--softedu-green);
            border-color: var(--softedu-green);
            color: white;
        }

        .card-img-top {
            object-fit: cover;
            height: 180px;
        }

        /* Modern hover effect for course cards */
        .card.course-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card.course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .btn-material {
            font-size: 0.8rem;
            padding: 0.25rem 0.6rem;
        }

        .badge-duration,
        .badge-fee {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="student_dashboard.php">SoftEdu Student</a>
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
                            href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] === 'courses') ? 'active' : '' ?>"
                            href="?page=courses"><i class="fas fa-book me-2"></i>My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'assignments' ? 'active' : '' ?>"
                            href="?page=assignments"><i class="fas fa-tasks me-2"></i>Assignments</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <?php if (!isset($_GET['page']) || $_GET['page'] === 'courses'): ?>
                    <h2 class="mb-4">My Courses</h2>
                    <?php if (empty($grouped)): ?>
                        <div class="alert alert-info">No courses available yet.</div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($grouped as $course): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card course-card h-100">
                                        <?php if ($course['image']): ?>
                                            <img src="uploads/courses/<?= htmlspecialchars($course['image']) ?>" class="card-img-top"
                                                alt="<?= htmlspecialchars($course['title']) ?>">
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                            <p class="text-muted"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...
                                            </p>

                                            <?php if ($course['materials']): ?>
                                                <h6 class="mt-auto mb-2">Materials:</h6>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($course['materials'] as $mat): ?>
                                                        <li class="d-flex justify-content-between align-items-center mb-2">
                                                            <span><?= htmlspecialchars($mat['title']) ?></span>
                                                            <?php if ($mat['source'] === 'youtube'): ?>
                                                                <a href="<?= htmlspecialchars($mat['url']) ?>" target="_blank"
                                                                    class="btn btn-sm btn-outline-danger btn-material">Watch</a>
                                                            <?php else: ?>
                                                                <a href="<?= htmlspecialchars($mat['url']) ?>" target="_blank"
                                                                    class="btn btn-sm btn-outline-primary btn-material">View</a>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>

                                            <div class="d-flex justify-content-between mt-3">
                                                <span
                                                    class="badge bg-success badge-duration"><?= htmlspecialchars($course['duration']) ?>
                                                    hrs</span>
                                                <span
                                                    class="fw-bold text-primary badge-fee">$<?= htmlspecialchars($course['fee']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif ($_GET['page'] === 'assignments'): ?>
                    <h2 class="mb-4">My Assignments</h2>

                    <?php
                    $stmt = $db->prepare("
                        SELECT a.*, c.title as course_title
                        FROM softedu_assignments a
                        JOIN softedu_courses c ON a.course_id = c.id
                        WHERE c.status = 'active'
                        ORDER BY a.due_date DESC
                    ");
                    $stmt->execute();
                    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (empty($assignments)): ?>
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
                                            <p><?= htmlspecialchars($assign['description']) ?></p>
                                            <p><strong>Due Date:</strong> <?= date('M j, Y', strtotime($assign['due_date'])) ?></p>

                                            <?php
                                            $subStmt = $db->prepare("
                                                SELECT * FROM softedu_assignment_submissions 
                                                WHERE assignment_id = ? AND student_id = ?
                                            ");
                                            $subStmt->execute([$assign['id'], $_SESSION['user_id']]);
                                            $submission = $subStmt->fetch(PDO::FETCH_ASSOC);
                                            ?>

                                            <?php if ($submission): ?>
                                                <div class="alert alert-success">
                                                    <strong>Submitted!</strong>
                                                    <a href="uploads/assignments/<?= htmlspecialchars($submission['file_path']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-light">View File</a>
                                                    <br><small>Submitted on:
                                                        <?= date('M j, Y g:i A', strtotime($submission['submitted_at'])) ?></small>
                                                </div>
                                            <?php else: ?>
                                                <form id="submitForm<?= $assign['id'] ?>" enctype="multipart/form-data">
                                                    <input type="hidden" name="assignment_id" value="<?= $assign['id'] ?>">
                                                    <div class="mb-2">
                                                        <label for="file<?= $assign['id'] ?>" class="form-label">Upload Here</label>
                                                        <input type="file" class="form-control" id="file<?= $assign['id'] ?>"
                                                            name="solution_file" accept=".pdf,.doc,.docx,.php,.zip" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-softedu btn-sm">Submit Assignment</button>
                                                    <div id="alert<?= $assign['id'] ?>" class="alert d-none mt-2"></div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('form[id^="submitForm"]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const assignmentId = form.querySelector('input[name="assignment_id"]').value;
                const formData = new FormData(form);
                const alertBox = document.getElementById('alert' + assignmentId);
                alertBox.className = 'alert d-none';

                try {
                    const res = await fetch('backend/user/submit_assignment.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await res.json();

                    if (result.success) {
                        alertBox.className = 'alert alert-success';
                        setTimeout(() => location.reload(), 1500);
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
        });
    </script>
</body>

</html>