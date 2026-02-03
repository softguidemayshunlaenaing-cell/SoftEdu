<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

// 1️⃣ Check student authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = (new Database())->getConnection();

$assignmentId = (int) ($_POST['assignment_id'] ?? 0);
$studentId = $_SESSION['user_id'];

// 2️⃣ Fetch assignment info
$stmt = $db->prepare("
    SELECT title, due_date, late_days, late_penalty
    FROM softedu_assignments
    WHERE id = ?
");
$stmt->execute([$assignmentId]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    echo json_encode(['success' => false, 'message' => 'Assignment not found']);
    exit;
}

$deadline = strtotime($assignment['due_date']);
$lateDays = (int) $assignment['late_days'];
$penaltyPerDay = (int) $assignment['late_penalty'];
$now = time();
$isLate = false;
$daysLate = 0;
$appliedPenalty = 0;

// 3️⃣ Check deadline rules
if ($now > $deadline) {
    if ($lateDays <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Deadline passed. Submission closed.'
        ]);
        exit;
    }

    $lateDeadline = strtotime("+{$lateDays} days", $deadline);

    if ($now > $lateDeadline) {
        echo json_encode([
            'success' => false,
            'message' => 'Late submission window closed.'
        ]);
        exit;
    }

    $isLate = true;
    $daysLate = ceil(($now - $deadline) / 86400);
    $appliedPenalty = min($daysLate * $penaltyPerDay, 100); // max 100%
}

// 4️⃣ Check existing submission
$stmt = $db->prepare("
    SELECT id, file_path
    FROM softedu_assignment_submissions
    WHERE assignment_id = ? AND student_id = ?
");
$stmt->execute([$assignmentId, $studentId]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

// 5️⃣ Validate uploaded file
if (!isset($_FILES['solution_file']) || $_FILES['solution_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Invalid file upload']);
    exit;
}

// Allowed extensions
$allowedExtensions = ['pdf', 'doc', 'docx', 'zip', 'php', 'js', 'css'];
$ext = strtolower(pathinfo($_FILES['solution_file']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'File type not allowed']);
    exit;
}

// Optional: generate a safe random filename
$newFileName = "assign_{$assignmentId}_student_{$studentId}_" . time() . "." . $ext;

$uploadDir = __DIR__ . '/../../uploads/assignments/';
if (!is_dir($uploadDir))
    mkdir($uploadDir, 0777, true);

$filePath = $uploadDir . $newFileName;

// Move uploaded file
if (!move_uploaded_file($_FILES['solution_file']['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'message' => 'File upload failed']);
    exit;
}

// Make sure .php, .js, .css are not executable
if (in_array($ext, ['php', 'js', 'css'])) {
    chmod($filePath, 0644); // readable but not executable
}

// 7️⃣ Insert or update submission
$relativePath = 'uploads/assignments/' . $newFileName;


if ($existing) {
    // Remove old file
    if (!empty($existing['file_path']) && file_exists(__DIR__ . '/../../' . $existing['file_path'])) {
        unlink(__DIR__ . '/../../' . $existing['file_path']);
    }

    // Update record
    $stmt = $db->prepare("
        UPDATE softedu_assignment_submissions
        SET file_path = ?, submitted_at = NOW(), is_late = ?, penalty = ?
        WHERE id = ?
    ");
    $stmt->execute([$relativePath, $isLate ? 1 : 0, $appliedPenalty, $existing['id']]);
} else {
    // Insert new record
    $stmt = $db->prepare("
        INSERT INTO softedu_assignment_submissions
        (assignment_id, student_id, file_path, submitted_at, is_late, penalty)
        VALUES (?, ?, ?, NOW(), ?, ?)
    ");
    $stmt->execute([$assignmentId, $studentId, $relativePath, $isLate ? 1 : 0, $appliedPenalty]);
}

// 8️⃣ Response
$message = $isLate
    ? "Late submission successful. Penalty applied: {$appliedPenalty}%."
    : "Assignment submitted successfully!";

echo json_encode([
    'success' => true,
    'late' => $isLate,
    'days_late' => $daysLate,
    'penalty' => $appliedPenalty,
    'message' => $message
]);
