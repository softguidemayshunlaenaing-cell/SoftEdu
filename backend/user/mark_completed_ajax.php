<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$studentId = $_SESSION['user_id'];
$materialId = $_POST['material_id'] ?? 0;

if (!$materialId) {
    echo json_encode(['success' => false, 'message' => 'Invalid material']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Insert progress record if not exists
$stmt = $db->prepare("
    INSERT IGNORE INTO softedu_material_progress (student_id, material_id)
    VALUES (?, ?)
");
$stmt->execute([$studentId, $materialId]);

// Get course_id for this material
$stmt = $db->prepare("SELECT course_id FROM softedu_course_materials WHERE id = ?");
$stmt->execute([$materialId]);
$courseId = $stmt->fetchColumn();

// Calculate new progress for this course
$stmt = $db->prepare("
    SELECT COUNT(*) as total,
           SUM(CASE WHEN mp.id IS NOT NULL THEN 1 ELSE 0 END) as completed
    FROM softedu_course_materials m
    LEFT JOIN softedu_material_progress mp
        ON m.id = mp.material_id AND mp.student_id = ?
    WHERE m.course_id = ?
");
$stmt->execute([$studentId, $courseId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$progress = $row['total'] ? round(($row['completed'] / $row['total']) * 100) : 0;

echo json_encode(['success' => true, 'progress' => $progress]);
