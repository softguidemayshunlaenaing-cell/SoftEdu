<?php
session_start();
// Local debug helpers
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$material_id = (int) ($data['material_id'] ?? 0);
$student_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

if (!$material_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request: missing material_id']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verify material exists
    $verify = $db->prepare("SELECT id FROM softedu_course_materials WHERE id = ?");
    $verify->execute([$material_id]);
    if (!$verify->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Material not found']);
        exit;
    }

    // Check current status
    $check = $db->prepare("
        SELECT id FROM softedu_material_progress 
        WHERE material_id = ? AND student_id = ?
    ");
    $check->execute([$material_id, $student_id]);
    $exists = $check->fetch();

    if ($exists) {
        // Remove completion
        $delete = $db->prepare("DELETE FROM softedu_material_progress WHERE id = ?");
        $delete->execute([$exists['id']]);
        $newStatus = false;
    } else {
        // Add completion
        $insert = $db->prepare("
            INSERT INTO softedu_material_progress (material_id, student_id, completed_at) 
            VALUES (?, ?, NOW())
        ");
        $insert->execute([$material_id, $student_id]);
        $newStatus = true;
    }

    // Calculate new progress
    $courseStmt = $db->prepare("SELECT course_id FROM softedu_course_materials WHERE id = ?");
    $courseStmt->execute([$material_id]);
    $courseId = $courseStmt->fetchColumn();

    $progressStmt = $db->prepare("
        SELECT 
            COUNT(m.id) as total,
            SUM(CASE WHEN mp.id IS NOT NULL THEN 1 ELSE 0 END) as completed
        FROM softedu_course_materials m
        LEFT JOIN softedu_material_progress mp 
            ON m.id = mp.material_id AND mp.student_id = ?
        WHERE m.course_id = ?
    ");
    $progressStmt->execute([$_SESSION['user_id'], $courseId]);
    $progressData = $progressStmt->fetch();

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
    error_log("Toggle error: " . $e->getMessage());
    // Return error message for local debugging
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}
?>