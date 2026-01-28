<?php
session_start();
header('Content-Type: application/json');

if ($_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);
$role = $input['role'] ?? '';

if (!$id || !in_array($role, ['staff', 'student'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

try {
    // Delete profile image
    $stmt = $db->prepare("SELECT profile_image FROM softedu_users WHERE id = ? AND role = ?");
    $stmt->execute([$id, $role]);
    $image = $stmt->fetchColumn();
    if ($image && file_exists(__DIR__ . "/../../uploads/profiles/$image")) {
        unlink(__DIR__ . "/../../uploads/profiles/$image");
    }

    // Delete user
    $stmt = $db->prepare("DELETE FROM softedu_users WHERE id = ? AND role = ?");
    $stmt->execute([$id, $role]);

    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
} catch (Exception $e) {
    error_log("Delete user error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>