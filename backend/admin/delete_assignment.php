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
    echo json_encode(['success' => false, 'message' => 'Invalid assignment ID.']);
    exit;
}

try {
    // Delete associated files
    $stmt = $db->prepare("SELECT file_path FROM softedu_assignment_submissions WHERE assignment_id = ?");
    $stmt->execute([$id]);
    while ($row = $stmt->fetch()) {
        $filePath = __DIR__ . '/../../uploads/assignments/' . $row['file_path'];
        if (file_exists($filePath))
            unlink($filePath);
    }

    // Delete submissions
    $db->prepare("DELETE FROM softedu_assignment_submissions WHERE assignment_id = ?")->execute([$id]);

    // Delete assignment
    $db->prepare("DELETE FROM softedu_assignments WHERE id = ?")->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Assignment deleted successfully.']);
} catch (Exception $e) {
    error_log("Delete assignment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>