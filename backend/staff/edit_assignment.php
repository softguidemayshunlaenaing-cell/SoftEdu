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

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date = $_POST['due_date'] ?? '';

if (!$id || empty($title) || empty($due_date)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Convert datetime-local to MySQL format
$mysql_due_date = date('Y-m-d H:i:s', strtotime($due_date));

try {
    $stmt = $db->prepare("
        UPDATE softedu_assignments 
        SET title = ?, description = ?, due_date = ?
        WHERE id = ?
    ");
    $stmt->execute([$title, $description, $mysql_due_date, $id]);

    echo json_encode(['success' => true, 'message' => 'Assignment updated successfully!']);
} catch (Exception $e) {
    error_log("Edit assignment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>