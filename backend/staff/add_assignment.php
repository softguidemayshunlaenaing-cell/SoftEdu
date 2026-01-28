<?php
session_start();
header('Content-Type: application/json');

// Only staff or admin can add assignments
if ($_SESSION['user_role'] !== 'staff' && $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$course_id = (int) ($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date = $_POST['due_date'] ?? '';

// Validate
if (!$course_id || empty($title) || empty($due_date)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Convert datetime-local to MySQL format
$mysql_due_date = date('Y-m-d H:i:s', strtotime($due_date));

try {
    $stmt = $db->prepare("
        INSERT INTO softedu_assignments (course_id, title, description, due_date)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$course_id, $title, $description, $mysql_due_date]);

    echo json_encode(['success' => true, 'message' => 'Assignment added!']);
} catch (Exception $e) {
    error_log("Add assignment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>