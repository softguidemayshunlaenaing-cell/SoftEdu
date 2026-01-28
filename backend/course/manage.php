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

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($action === 'add') {
    // ... (use your existing add_course logic)
} elseif ($action === 'edit') {
    // ... (edit logic)
} elseif ($action === 'delete') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int) ($input['id'] ?? 0);
    if ($id) {
        // Delete image
        $stmt = $db->prepare("SELECT image FROM softedu_courses WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetchColumn();
        if ($image && file_exists(__DIR__ . '/../../uploads/courses/' . $image)) {
            unlink(__DIR__ . '/../../uploads/courses/' . $image);
        }
        $stmt = $db->prepare("DELETE FROM softedu_courses WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Course deleted.']);
    }
} elseif ($action === 'get') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id) {
        $stmt = $db->prepare("SELECT * FROM softedu_courses WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }
}
// Add full add/edit logic as needed
?>