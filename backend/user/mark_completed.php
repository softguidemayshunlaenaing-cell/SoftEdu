<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    http_response_code(403);
    exit('Unauthorized');
}

require_once __DIR__ . '/../config/db.php';
$database = new Database();
$db = $database->getConnection();

$student_id = $_SESSION['user_id'];
$material_id = (int) ($_POST['material_id'] ?? 0);

if ($material_id <= 0) {
    $_SESSION['error'] = "Invalid material selected.";
    header('Location: ../../student_dashboard.php?page=courses');
    exit;
}

try {
    // Insert completion record
    $stmt = $db->prepare("
        INSERT INTO softedu_material_progress (student_id, material_id) 
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE completed_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$student_id, $material_id]);

    $_SESSION['success'] = "Lecture marked as completed!";
} catch (Exception $e) {
    $_SESSION['error'] = "Failed to update progress: " . $e->getMessage();
}

header('Location: ../../student_dashboard.php?page=courses');
exit;
