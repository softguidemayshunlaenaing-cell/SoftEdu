<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Name is required.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("UPDATE softedu_users SET name = ? WHERE id = ?");
$stmt->execute([$name, $_SESSION['user_id']]);
$_SESSION['user_name'] = $name;

echo json_encode(['success' => true, 'message' => 'Name updated successfully!']);
?>