<?php
session_start();
header('Content-Type: application/json');

// Authorization check
if ($_SESSION['user_role'] !== 'staff' && $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

// Get POST data
$course_id = (int) ($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date = $_POST['due_date'] ?? '';
$late_days = (int) ($_POST['late_days'] ?? 3);
$late_penalty = (int) ($_POST['late_penalty'] ?? 5);

// Validate required fields
if (!$course_id || empty($title) || empty($due_date)) {
    echo json_encode(['success' => false, 'message' => 'Course, title, and due date are required.']);
    exit;
}

// Validate due date format
$mysql_due_date = date('Y-m-d H:i:s', strtotime($due_date));
if ($mysql_due_date === false) {
    echo json_encode(['success' => false, 'message' => 'Invalid due date format.']);
    exit;
}

// Handle file upload (OPTIONAL)
$file_path = null;
if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
    $allowed_exts = ['pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png'];
    $file = $_FILES['assignment_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($ext, $allowed_exts)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_exts)
        ]);
        exit;
    }

    // Validate size (10MB limit)
    if ($file['size'] > 10 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 10MB limit.']);
        exit;
    }

    // Set CORRECT upload path (critical fix!)
    $uploadDir = __DIR__ . '/../../uploads/assignments/'; // Relative to project root
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate safe filename
    $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file['name']);
    $filename = time() . '_' . $safeName;
    $destination = $uploadDir . $filename;

    // Move file with error handling
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        // Log actual error for debugging
        error_log("Upload failed: " . $file['tmp_name'] . " -> " . $destination);
        error_log("PHP Upload Error Code: " . $file['error']);
        error_log("Upload Dir Writable: " . (is_writable($uploadDir) ? 'Yes' : 'No'));

        echo json_encode(['success' => false, 'message' => 'File upload failed. Check server permissions.']);
        exit;
    }

    $file_path = 'uploads/assignments/' . $filename;
}
// Handle upload errors (user-friendly messages)
elseif (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server size limit (php.ini)',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    $code = $_FILES['assignment_file']['error'];
    $msg = $errors[$code] ?? 'Unknown upload error (Code: ' . $code . ')';

    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

// Insert assignment into database
try {
    $stmt = $db->prepare("
        INSERT INTO softedu_assignments 
        (course_id, title, description, due_date, file_path, late_days, late_penalty, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $course_id,
        $title,
        $description,
        $mysql_due_date,
        $file_path,
        $late_days,
        $late_penalty
    ]);

    echo json_encode([
        'success' => true,
        'message' => $file_path
            ? 'Assignment added with file!'
            : 'Assignment added successfully!'
    ]);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error. Contact administrator.'
    ]);
}
?>