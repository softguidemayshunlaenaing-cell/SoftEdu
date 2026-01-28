<?php
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    exit;
}

try {
    $stmt = $db->prepare("DELETE FROM softedu_course_materials WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Material deleted successfully.']);

} catch (Exception $e) {
    error_log("Delete material error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to delete material.']);
}
?>