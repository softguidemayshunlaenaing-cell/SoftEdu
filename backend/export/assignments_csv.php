<?php
session_start();
if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    http_response_code(403);
    exit('Unauthorized');
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Build query with filters
$sql = "
    SELECT a.*, c.title as course_title
    FROM softedu_assignments a
    JOIN softedu_courses c ON a.course_id = c.id
    WHERE 1=1
";
$params = [];

if (!empty($_GET['course_id'])) {
    $sql .= " AND a.course_id = ?";
    $params[] = (int) $_GET['course_id'];
}

if (!empty($_GET['student_id'])) {
    $sql .= " AND a.id IN (
        SELECT assignment_id FROM softedu_assignment_submissions 
        WHERE student_id = ?
    )";
    $params[] = (int) $_GET['student_id'];
}

$sql .= " ORDER BY a.due_date DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare CSV
$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header row
fputcsv($output, [
    'Course',
    'Assignment Title',
    'Due Date',
    'Submission Count',
    'Submitted By'
]);

// Data rows
foreach ($assignments as $assign) {
    // Get submission count and student names
    $subSql = "SELECT COUNT(*), GROUP_CONCAT(u.name SEPARATOR '; ') 
                FROM softedu_assignment_submissions s
                JOIN softedu_users u ON s.student_id = u.id
                WHERE s.assignment_id = ?";
    $subParams = [$assign['id']];

    if (!empty($_GET['student_id'])) {
        $subSql .= " AND s.student_id = ?";
        $subParams[] = (int) $_GET['student_id'];
    }

    $subStmt = $db->prepare($subSql);
    $subStmt->execute($subParams);
    $subResult = $subStmt->fetch(PDO::FETCH_NUM);

    $submissionCount = $subResult[0] ?? 0;
    $studentNames = $subResult[1] ?? 'None';

    fputcsv($output, [
        $assign['course_title'],
        $assign['title'],
        date('Y-m-d H:i', strtotime($assign['due_date'])),
        $submissionCount,
        $studentNames
    ]);
}

fclose($output);

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="softedu_assignments_' . date('Y-m-d') . '.csv"');

exit;
?>