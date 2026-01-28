<?php
session_start();
header('Content-Type: application/json');

if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid submission ID.']);
    exit;
}

try {
    // Get file path before deletion
    $stmt = $db->prepare("SELECT file_path FROM softedu_assignment_submissions WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if ($file && file_exists(__DIR__ . "/../../uploads/assignments/$file")) {
        unlink(__DIR__ . "/../../uploads/assignments/$file");
    }

    $db->prepare("DELETE FROM softedu_assignment_submissions WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Submission deleted.']);
} catch (Exception $e) {
    error_log("Delete submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>