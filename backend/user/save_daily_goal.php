<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$goal = trim($data['goal'] ?? '');
$today = date('Y-m-d');

$db = (new Database())->getConnection();

$stmt = $db->prepare("
    INSERT INTO softedu_daily_goals (student_id, goal_text, goal_date)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE goal_text = VALUES(goal_text)
");
$stmt->execute([$_SESSION['user_id'], $goal, $today]);

echo json_encode(['success' => true]);
