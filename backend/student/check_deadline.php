<?php
session_start();
require '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$assignmentId = (int) ($_GET['assignment_id'] ?? 0);
$db = (new Database())->getConnection();

$stmt = $db->prepare("
    SELECT due_date, late_days, late_penalty 
    FROM softedu_assignments 
    WHERE id = ?
");
$stmt->execute([$assignmentId]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    echo json_encode(['is_late' => false]);
    exit;
}

$deadline = strtotime($assignment['due_date']);
$now = time();
$isLate = $now > $deadline;
$penalty = 0;

if ($isLate) {
    $daysLate = ceil(($now - $deadline) / 86400);
    $penalty = min($daysLate * ((int) $assignment['late_penalty'] ?? 0), 100);
}

echo json_encode([
    'is_late' => $isLate,
    'penalty' => $penalty,
    'days_late' => $daysLate ?? 0
]);