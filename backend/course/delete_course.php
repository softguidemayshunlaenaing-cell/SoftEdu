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

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID.']);
    exit;
}

try {
    // Get image filename
    $stmt = $db->prepare("SELECT image FROM softedu_courses WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    // Delete image file
    if ($image) {
        $filePath = __DIR__ . '/../../uploads/courses/' . $image;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Delete course
    $stmt = $db->prepare("DELETE FROM softedu_courses WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Course deleted successfully.']);

} catch (Exception $e) {
    error_log("Delete course error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to delete course.']);
}
?>