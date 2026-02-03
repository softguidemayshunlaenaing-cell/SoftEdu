<?php
session_start();
header('Content-Type: application/json');

// Validate session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$studentId = (int) $_SESSION['user_id'];
$materialId = (int) ($_POST['material_id'] ?? 0);

// Validate input
if ($materialId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid material ID']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verify material exists and belongs to an active course
    $verifyStmt = $db->prepare("
        SELECT m.id 
        FROM softedu_course_materials m
        JOIN softedu_courses c ON m.course_id = c.id
        WHERE m.id = ? AND c.status = 'active'
    ");
    $verifyStmt->execute([$materialId]);

    if (!$verifyStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Material not found or course inactive']);
        exit;
    }

    // Insert progress record (IGNORE prevents duplicates)
    $insertStmt = $db->prepare("
        INSERT IGNORE INTO softedu_material_progress (student_id, material_id, completed_at)
        VALUES (?, ?, NOW())
    ");
    $insertStmt->execute([$studentId, $materialId]);

    // Get course_id for this material
    $courseStmt = $db->prepare("SELECT course_id FROM softedu_course_materials WHERE id = ?");
    $courseStmt->execute([$materialId]);
    $courseId = $courseStmt->fetchColumn();

    if (!$courseId) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }

    // Calculate new progress percentage
    $progressStmt = $db->prepare("
        SELECT 
            COUNT(m.id) as total,
            SUM(CASE WHEN mp.id IS NOT NULL THEN 1 ELSE 0 END) as completed
        FROM softedu_course_materials m
        LEFT JOIN softedu_material_progress mp 
            ON m.id = mp.material_id AND mp.student_id = ?
        WHERE m.course_id = ?
    ");
    $progressStmt->execute([$studentId, $courseId]);
    $progressData = $progressStmt->fetch(PDO::FETCH_ASSOC);

    $total = (int) $progressData['total'];
    $completed = (int) $progressData['completed'];
    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

    echo json_encode([
        'success' => true,
        'progress' => $progress,
        'completed' => $completed,
        'total' => $total
    ]);

} catch (Exception $e) {
    error_log("Mark completed error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
?>