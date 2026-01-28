<?php
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    http_response_code(403);
    echo json_encode(null);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    echo json_encode(null);
    exit;
}

$stmt = $db->prepare("SELECT * FROM softedu_courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($course); // Returns null if not found
?>