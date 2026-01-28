<?php
session_start();
header('Content-Type: application/json');

// Only students can submit assignments
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$assignment_id = (int) ($_POST['assignment_id'] ?? 0);
$student_id = (int) $_SESSION['user_id'];

if (!$assignment_id || !$student_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Validate file upload
if (!isset($_FILES['solution_file']) || $_FILES['solution_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Please upload a valid file.']);
    exit;
}

$file = $_FILES['solution_file'];

// Allowed MIME types for PDF and DOC/DOCX
$allowedTypes = [
    // Documents
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

    // Code files
    'text/x-php',           // .php
    'application/x-httpd-php',

    // Archives
    'application/zip',      // .zip
    'application/x-zip-compressed'
];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Only PDF or DOC/DOCX files are allowed.']);
    exit;
}

// Max file size: 10MB
if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File must be under 10MB.']);
    exit;
}

// Create upload directory if missing
$uploadDir = __DIR__ . '/../../uploads/assignments/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
        exit;
    }
}

// Generate unique filename
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'assign_' . $assignment_id . '_student_' . $student_id . '_' . time() . '.' . $ext;
$targetPath = $uploadDir . $filename;

// Save file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
    exit;
}

try {
    // Insert or update submission (allows re-submission)
    $stmt = $db->prepare("
        INSERT INTO softedu_assignment_submissions (assignment_id, student_id, file_path, submitted_at)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            file_path = VALUES(file_path),
            submitted_at = VALUES(submitted_at)
    ");
    $stmt->execute([$assignment_id, $student_id, $filename]);

    echo json_encode(['success' => true, 'message' => 'Assignment submitted successfully!']);

} catch (Exception $e) {
    error_log("Assignment submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
?>