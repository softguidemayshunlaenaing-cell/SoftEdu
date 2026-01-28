<?php
session_start();
header('Content-Type: application/json');

if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    http_response_code(403);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$course_id = (int) ($_GET['course_id'] ?? 0);
if (!$course_id) {
    echo json_encode([]);
    exit;
}

$stmt = $db->prepare("SELECT * FROM softedu_course_materials WHERE course_id = ?");
$stmt->execute([$course_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>