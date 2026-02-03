<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/backend/config/db.php';
$database = new Database();
$db = $database->getConnection();

// Fetch user data
$stmt = $db->prepare("SELECT name, profile_image, force_password_change, force_document_upload FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if onboarding is required
$needPassword = (int) $user['force_password_change'] === 1;
$needDocuments = (int) $user['force_document_upload'] === 1;

if ($needPassword || $needDocuments) {
    // Redirect to onboarding if not completed
    header('Location: student_onboarding.php');
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
    <title>SoftEdu | Excellence in Education</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* =========================
   Sidebar
========================= */
        .sidebar {
            background: white;
            min-height: calc(100vh - 80px);
            border-right: 1px solid #dee2e6;
            padding: 20px;
            position: sticky;
            top: 80px;
            /* Remove width: 100%; */
        }
        @media (max-width: 991.98px) {
            .sidebar {
                position: static;
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid #dee2e6;
            }
        }



        .side-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #495057;
            /* darker gray for text */
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

        /* =========================
   Cards
========================= */
        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .course-card {
            --course-bg: none;
            position: relative;
            overflow: hidden;
            border: 1px solid #e7edf3;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .course-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(135deg, rgba(5, 150, 105, 0.08), rgba(14, 116, 144, 0.06)),
                var(--course-bg);
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.5;
        }

        .course-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(255, 255, 255, 0.55), rgba(255, 255, 255, 0.05));
            opacity: 0.35;
        }

        .course-card .card-body {
            position: relative;
            z-index: 1;
            padding: 1.5rem;
        }

        .course-card:hover {
            transform: translateY(-2px);
            border-color: #cde7de;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .course-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.3;
        }

        .course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .course-chip {
            font-size: 0.72rem;
            font-weight: 600;
            color: #0f766e;
            background: #e6fffb;
            border: 1px solid #bff3ea;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
        }

        .course-desc {
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 0.9rem;
        }

        .progress-container small {
            color: #475569;
            font-weight: 600;
        }

        .lecture-list .list-group-item {
            background: rgba(255, 255, 255, 0.75);
            border-color: #eef2f7;
        }

        .lecture-list a {
            text-decoration: none;
            color: #0f172a;
            font-weight: 600;
        }

        .lecture-list a:hover {
            color: #0f766e;
            text-decoration: none;
        }

        .lecture-list a:active,
        .lecture-list a:focus,
        .lecture-list a:focus-visible {
            color: #0f766e;
            text-decoration: none;
            outline: none;
        }

        .assignment-card {
            border: 1px solid #e7edf3;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .assignment-card:hover {
            transform: translateY(-2px);
            border-color: #cde7de;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .assignment-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .assignment-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.3;
        }

        .assignment-chip {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .assignment-chip.submitted {
            color: #0f766e;
            background: #e6fffb;
            border-color: #bff3ea;
        }

        .assignment-chip.pending {
            color: #92400e;
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .assignment-chip.late {
            color: #b91c1c;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .assignment-desc {
            color: #475569;
            font-size: 0.88rem;
        }

        .lecture-placeholder {
            min-height: 42px;
        }

        /* =========================
   Dashboard Hero Section
========================= */
        .dashboard-hero {
            background-color: var(--bg-gray);
            /* same as navbar */
            border-bottom: 1px solid #dee2e6;
            /* subtle separation */
            padding: 60px 0;
            margin-bottom: 30px;
            text-align: center;
        }

        .dashboard-hero h1 {
            color: #333;
            /* matches navbar text */
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
        }

        .dashboard-hero p {
            color: #555;
            font-size: 1.1rem;
        }

        /* =========================
   Progress Bars
========================= */
        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .today-input {
            max-width: 480px;
            margin: 0 auto;
            opacity: 1;
            /* always visible */
        }

        .today-input input {
            border-radius: 50px;
            border: none;
            padding: 14px 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.2s ease;
            /* subtle, no movement */
        }

        .today-input input:focus {
            outline: none;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        /* Hide placeholder on focus */
        #todayPlan:focus::placeholder {
            color: transparent;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <?php
    $today = date('Y-m-d');

    $stmt = $db->prepare("
    SELECT goal_text 
    FROM softedu_daily_goals 
    WHERE student_id = ? AND goal_date = ?
");
    $stmt->execute([$_SESSION['user_id'], $today]);
    $todayGoal = $stmt->fetchColumn();
    ?>
    <section class="hero-gradient text-white py-5 hero-animate">
        <div class="container text-center">

            <h1 class="fw-bold hero-title" style="font-family: 'Playfair Display', serif;">
                Hi, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
            </h1>

            <p class="opacity-90 mt-3 fs-5 hero-subtitle">
                You have <strong><?= $assignmentCount ?></strong> pending assignments
                and <strong><?= $courseCount ?></strong> active courses today.
            </p>

            <!-- Today's Goal -->
            <div class="today-input mt-4">
                <input type="text" class="form-control form-control-lg text-center"
                    placeholder="🎯 What’s your main goal today?" id="todayPlan"
                    value="<?= htmlspecialchars($todayGoal ?? '') ?>">
            </div>

            <small class="text-white-50 mt-2 d-block">
                Saved automatically for <?= date('M j') ?>
            </small>

        </div>
    </section>



    <div class="container-fluid">

        <div class="row">

            <div class="col-12 col-lg-2 mb-3 mb-lg-0">
                <div class="sidebar">
                    <small class="text-uppercase text-muted fw-bold mb-3 d-block">Learning</small>
                    <?php
                    $links = [
                        'courses' => ['icon' => 'book', 'label' => 'My Courses'],
                        'assignments' => ['icon' => 'pencil-square', 'label' => 'Assignments'],
                        'certificate' => ['icon' => 'award', 'label' => 'Certificate'],
                        'profile' => ['icon' => 'person-circle', 'label' => 'My Profile'],
                    ];
                    foreach ($links as $key => $link): ?>
                        <a href="?page=<?= $key ?>" class="side-link <?= $page === $key ? 'active' : '' ?>">
                            <i class="bi bi-<?= $link['icon'] ?>"></i> <?= $link['label'] ?>
                        </a>
                    <?php endforeach; ?>
                    <hr>
                    <a href="backend/auth/logout.php" class="side-link">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>


            <main class="col-lg-10 p-4">
                <div class="container">
                    <?php if ($page == 'courses'): ?>

                        <?php
                        // Get all active courses
                        $courseStmt = $db->query("SELECT * FROM softedu_courses WHERE status = 'active' ORDER BY created_at ASC");
                        $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($courses)): ?>
                            <div class="alert alert-info">No courses available yet.</div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($courses as $course):
                                    // Get materials for this course
                                    $matStmt = $db->prepare("
                    SELECT m.*, 
                    CASE WHEN mp.id IS NOT NULL THEN 1 ELSE 0 END AS completed
                    FROM softedu_course_materials m
                    LEFT JOIN softedu_material_progress mp 
                        ON m.id = mp.material_id AND mp.student_id = ?
                    WHERE m.course_id = ?
                    ORDER BY m.created_at ASC
                ");
                                    $matStmt->execute([$_SESSION['user_id'], $course['id']]);
                                    $materials = $matStmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Calculate progress
                                    $total = count($materials);
                                    $completed = array_sum(array_column($materials, 'completed'));
                                    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

                                    // Pagination setup
                                    $itemsPerPage = 5;
                                    $totalMaterials = count($materials);
                                    $totalPages = ceil($totalMaterials / $itemsPerPage);
                                    $currentPage = isset($_GET['page_' . $course['id']]) ? (int) $_GET['page_' . $course['id']] : 1;
                                    $currentPage = max(1, min($currentPage, $totalPages));
                                    $offset = ($currentPage - 1) * $itemsPerPage;
                                    $paginatedMaterials = array_slice($materials, $offset, $itemsPerPage);
                                    ?>
                                    <div class="col-md-6 col-lg-4">
                                        <?php
                                        $bgStyle = '';
                                        if (!empty($course['image'])) {
                                            $bgStyle = " style=\"--course-bg: url('uploads/courses/" . htmlspecialchars($course['image']) . "');\"";
                                        }
                                        ?>
                                        <div class="card h-100 shadow-sm course-card" <?= $bgStyle ?>>
                                            <div class="card-body d-flex flex-column">

                                                <div class="course-card-header">
                                                    <h5 class="course-title"><?= htmlspecialchars($course['title']) ?></h5>
                                                    <div class="course-meta">
                                                        <?php if (!empty($course['duration'])): ?>
                                                            <span
                                                                class="course-chip"><?= htmlspecialchars($course['duration']) ?></span>
                                                        <?php endif; ?>
                                                        <span class="course-chip">
                                                            <?= $course['fee'] ? '$' . number_format($course['fee'], 2) : 'Free' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php if (!empty($course['description'])): ?>
                                                    <p class="course-desc">
                                                        <?= htmlspecialchars(mb_strimwidth($course['description'], 0, 110, '...')) ?>
                                                    </p>
                                                <?php endif; ?>

                                                <!-- Progress bar -->
                                                <div class="progress-container">
                                                    <small class="text-muted">Progress: <?= $progress ?>%</small>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                                                    </div>
                                                </div>

                                                <!-- Lectures list -->
                                                <?php if ($materials): ?>
                                                    <ul class="list-group list-group-flush lecture-list">
                                                        <?php
                                                        $maxSlots = 5;
                                                        $count = count($paginatedMaterials);

                                                        foreach ($paginatedMaterials as $m): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <a href="<?= htmlspecialchars(trim($m['material_url'])) ?>" target="_blank">
                                                                    <?= htmlspecialchars($m['title']) ?>
                                                                </a>
                                                                <button type="button"
                                                                    class="mark-toggle-btn btn btn-sm <?= $m['completed'] ? 'btn-success' : 'btn-outline-success' ?>"
                                                                    data-material-id="<?= $m['id'] ?>"
                                                                    data-completed="<?= (int) $m['completed'] ?>">
                                                                    <?= $m['completed'] ? '✓' : 'Mark Done' ?>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>

                                                        <!-- Fill empty slots -->
                                                        <?php for ($i = $count; $i < $maxSlots; $i++): ?>
                                                            <li class="list-group-item lecture-placeholder"></li>
                                                        <?php endfor; ?>
                                                    </ul>

                                                <?php else: ?>
                                                    <p class="text-muted small mt-auto">No lectures yet.</p>
                                                <?php endif; ?>

                                                <!-- Pagination -->
                                                <?php if ($totalPages > 1): ?>
                                                    <nav aria-label="Lecture pagination" class="mt-auto">
                                                        <ul class="pagination pagination-sm justify-content-center mb-0">
                                                            <?php if ($currentPage > 1): ?>
                                                                <li class="page-item">
                                                                    <a class="page-link"
                                                                        href="?page=courses&page_<?= $course['id'] ?>=<?= $currentPage - 1 ?>">«</a>
                                                                </li>
                                                            <?php endif; ?>

                                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                                    <a class="page-link"
                                                                        href="?page=courses&page_<?= $course['id'] ?>=<?= $i ?>"><?= $i ?></a>
                                                                </li>
                                                            <?php endfor; ?>

                                                            <?php if ($currentPage < $totalPages): ?>
                                                                <li class="page-item">
                                                                    <a class="page-link"
                                                                        href="?page=courses&page_<?= $course['id'] ?>=<?= $currentPage + 1 ?>">»</a>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </nav>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($page == 'assignments'): ?>
                        <div class="row g-4">
                            <?php
                            // Fetch assignments with submission status
                            $stmt = $db->prepare("
    SELECT 
        a.id,
        a.title,
        a.description,
        a.due_date,
        a.late_days,
        a.late_penalty,
        a.file_path AS assignment_file,   -- 👈 teacher file
        c.title AS course_title,
        s.id AS submission_id,
        s.file_path AS submission_file,   -- 👈 student file
        s.is_late,
        s.penalty,
        CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END AS submitted
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
                                <?php foreach ($assignments as $assign):
                                    $isSubmitted = (int) $assign['submitted'] === 1;
                                    $now = time();
                                    $deadline = strtotime($assign['due_date']);
                                    $lateDays = (int) $assign['late_days'];
                                    $latePenalty = (int) $assign['late_penalty'];
                                    $lateDeadline = strtotime("+{$lateDays} days", $deadline);

                                    $beforeDeadline = $now <= $deadline;
                                    $withinLateWindow = $now > $deadline && $now <= $lateDeadline;
                                    ?>
                                    <div class="col-md-6">
                                        <div class="card assignment-card">
                                            <div class="card-body">
                                                <div class="assignment-header">
                                                    <h5 class="assignment-title"><?= htmlspecialchars($assign['title']) ?></h5>
                                                    <?php
                                                    $chipClass = $isSubmitted ? 'submitted' : 'pending';
                                                    $chipText = $isSubmitted ? 'Submitted' : 'Pending';
                                                    if (!$beforeDeadline && $withinLateWindow && !$isSubmitted) {
                                                        $chipClass = 'late';
                                                        $chipText = 'Late';
                                                    }
                                                    ?>
                                                    <span class="assignment-chip <?= $chipClass ?>"><?= $chipText ?></span>
                                                </div>
                                                <p class="text-muted"><strong>Course:</strong>
                                                    <?= htmlspecialchars($assign['course_title']) ?></p>
                                                <p><strong>Due Date:</strong> <?= date('M j, Y', strtotime($assign['due_date'])) ?>
                                                </p>
                                                <?php if (!empty($assign['description'])): ?>
                                                    <p class="assignment-desc"><?= nl2br(htmlspecialchars($assign['description'])) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if (!empty($assign['assignment_file'])): ?>
                                                    <a href="<?= htmlspecialchars($assign['assignment_file']) ?>" target="_blank"
                                                        class="btn btn-outline-primary btn-sm mb-2">
                                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                                        Download Assignment
                                                    </a>
                                                <?php endif; ?>


                                                <div class="assignment-feedback mb-2"></div>

                                                <form method="POST" action="backend/user/submit_assignment.php"
                                                    enctype="multipart/form-data">
                                                    <input type="hidden" name="assignment_id" value="<?= $assign['id'] ?>">
                                                    <div class="mb-2">
                                                        <label class="form-label">
                                                            <?= $isSubmitted
                                                                ? ($beforeDeadline ? 'Resubmit Solution' : 'Submit Solution (Late, Penalty applies)')
                                                                : ($beforeDeadline ? 'Upload Solution' : 'Submit Solution (Late, Penalty applies)') ?>
                                                        </label>
                                                        <input type="file" class="form-control" name="solution_file"
                                                            accept=".pdf,.doc,.docx,.php,.zip" required>
                                                    </div>

                                                    <?php if ($isSubmitted && $assign['is_late']): ?>
                                                        <small class="text-danger mb-2 d-block">
                                                            Already Late Submission, Penalty: <?= $assign['penalty'] ?>%
                                                        </small>
                                                    <?php elseif ($withinLateWindow):
                                                        $daysLate = ceil(($now - $deadline) / 86400);
                                                        $penalty = $daysLate * $latePenalty;
                                                        ?>
                                                        <small class="text-danger mb-2 d-block">
                                                            Late by <?= $daysLate ?> day(s), penalty: <?= $penalty ?>%
                                                        </small>
                                                    <?php endif; ?>

                                                    <?php if ($beforeDeadline || $withinLateWindow): ?>
                                                        <button type="submit"
                                                            class="btn <?= $isSubmitted ? 'btn-warning' : ($withinLateWindow ? 'btn-danger' : 'btn-success') ?>">
                                                            <?= $isSubmitted ? 'Resubmit' : ($withinLateWindow ? 'Submit Late' : 'Submit') ?>
                                                        </button>

                                                    <?php else: ?>
                                                        <button class="btn btn-secondary" disabled>Submission Closed</button>
                                                    <?php endif; ?>

                                                    <?php if ($isSubmitted && !empty($assign['submission_file'])): ?>
                                                        <a href="<?= htmlspecialchars($assign['submission_file']) ?>" target="_blank"
                                                            class="btn btn-outline-success btn-sm ms-2">
                                                            <i class="bi bi-eye"></i> View Submission
                                                        </a>
                                                    <?php endif; ?>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>



                    <?php elseif ($page == 'certificate'): ?>
                        <h2>Certifiacte</h2>
                        <div class="card main-card p-5 text-center">
                            <h4>Ready for your certificate?</h4>
                            <p class="text-muted">You must complete all course modules before applying.</p>
                            <button class="btn btn-success rounded-pill px-5 mt-3" disabled>Apply Now</button>
                        </div>

                    <?php elseif ($page == 'profile'): ?>

                        <div class="card">
                            <div class="card-body">
                                <!-- Profile Header -->
                                <div class="text-center mb-4">
                                    <?php
                                    $imgSrc = !empty($user['profile_image'])
                                        ? 'uploads/profiles/' . htmlspecialchars($user['profile_image'])
                                        : 'https://ui-avatars.com/api/?name=    ' . urlencode($user['name']) . '&background=059669&color=fff&size=128';
                                    ?>
                                    <img src="<?= $imgSrc ?>" id="profilePreview" class="rounded-circle mb-3" width="120"
                                        height="120" style="object-fit: cover; border: 3px solid #e2e8f0;">

                                    <h4 class="mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                                    <span class="badge bg-success"><?= ucfirst($_SESSION['user_role']) ?></span>
                                </div>

                                <!-- Profile Tabs -->
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
                                    <!-- Account Tab -->
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
                                            <button type="submit" class="btn btn-success">Update Name</button>
                                            <div id="nameAlert" class="alert d-none mt-3"></div>
                                        </form>
                                    </div>

                                    <!-- Security Tab -->
                                    <div class="tab-pane fade" id="securityTab">
                                        <form id="passwordForm">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Current Password</label>
                                                <input type="password" class="form-control" name="current_password"
                                                    required>
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
                                            <button type="submit" class="btn btn-success">Change Password</button>
                                            <div id="passwordAlert" class="alert d-none mt-3"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js    "></script>
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
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
<script>
    // Profile Image Upload
    document.getElementById('profilePreview').addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
    });

    // Update Name
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
                // Update session name
                const sessionName = document.querySelector('[data-session-name]');
                if (sessionName) sessionName.textContent = formData.get('name');
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

    // Change Password
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

    // Upload Profile Image
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
                // Update profile image
                document.getElementById('profilePreview').src = 'uploads/profiles/' + result.filename + '?t=' + Date.now();

                // Close modal after success
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to attach submit handler to a form
        function attachAssignmentForm(form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const formData = new FormData(this);

                // Disable button while submitting
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';

                // Alert box inside form
                let alertBox = this.querySelector('.alert');
                if (!alertBox) {
                    alertBox = document.createElement('div');
                    alertBox.className = 'alert mt-3';
                    this.appendChild(alertBox);
                }

                alertBox.className = 'alert d-none';
                alertBox.textContent = '';

                try {
                    const res = await fetch(this.action, { method: 'POST', body: formData });
                    const data = await res.json();

                    if (data.success) {
                        // Show success alert
                        alertBox.className = 'alert alert-success mt-3';
                        alertBox.textContent = data.message || 'Assignment submitted successfully!';

                        // Update badge dynamically instead of replacing form
                        const cardBody = this.closest('.card-body');
                        const existingBadge = cardBody.querySelector('.submission-badge');
                        let submittedText = '✅ Submitted';
                        if (data.late) submittedText += ` (Late, Penalty: ${data.penalty}%)`;

                        if (existingBadge) {
                            existingBadge.textContent = submittedText;
                        } else {
                            const badge = document.createElement('div');
                            badge.className = 'badge bg-success submission-badge my-2';
                            badge.textContent = submittedText;
                            this.parentNode.insertBefore(badge, this);
                        }

                        // Keep the form for resubmit (do not remove it)
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Resubmit';
                    } else {
                        alertBox.className = 'alert alert-danger mt-3';
                        alertBox.textContent = data.message || 'Submission failed.';
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit';
                    }
                } catch (err) {
                    console.error(err);
                    alertBox.className = 'alert alert-danger mt-3';
                    alertBox.textContent = 'Network error. Please try again.';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                }
            });
        }

        // Attach to all forms on page load
        document.querySelectorAll('form[action="backend/user/submit_assignment.php"]').forEach(attachAssignmentForm);
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle Mark Done toggle
        document.querySelectorAll('.mark-toggle-btn').forEach(button => {
            button.addEventListener('click', async function () {
                const materialId = this.dataset.materialId;
                const isCompleted = this.dataset.completed === '1';

                // Toggle UI immediately
                if (isCompleted) {
                    this.textContent = 'Mark Done';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-success');
                    this.dataset.completed = '0';
                } else {
                    this.textContent = '✓';
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                    this.dataset.completed = '1';
                }

                try {
                    const response = await fetch('backend/user/toggle_material_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            material_id: materialId,
                            student_id: <?= $_SESSION['user_id'] ?>
                        })
                    });

                    const result = await response.json();
                    if (!result.success) {
                        // Revert UI on failure
                        if (isCompleted) {
                            this.textContent = '✓';
                            this.classList.remove('btn-outline-success');
                            this.classList.add('btn-success');
                            this.dataset.completed = '1';
                        } else {
                            this.textContent = 'Mark Done';
                            this.classList.remove('btn-success');
                            this.classList.add('btn-outline-success');
                            this.dataset.completed = '0';
                        }
                        console.error('Toggle failed:', result.message);
                    }

                    // Update progress bar
                    if (result.progress !== undefined) {
                        const progressBar = this.closest('.card-body').querySelector('.progress-bar');
                        const progressText = this.closest('.card-body').querySelector('.progress-container small');
                        if (progressBar && progressText) {
                            progressBar.style.width = result.progress + '%';
                            progressText.textContent = 'Progress: ' + result.progress + '%';
                        }
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    // Revert UI
                    if (isCompleted) {
                        this.textContent = '✓';
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                        this.dataset.completed = '1';
                    } else {
                        this.textContent = 'Mark Done';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-success');
                        this.dataset.completed = '0';
                    }
                }
            });
        });
    });
</script>
<script>
    document.getElementById('todayPlan').addEventListener('input', function () {
        fetch('backend/user/save_daily_goal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ goal: this.value })
        });
    });
</script>



</html>
