<?php
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID.']);
    exit;
}

// Fetch current image
$stmt = $db->prepare("SELECT image FROM softedu_courses WHERE id = ?");
$stmt->execute([$id]);
$currentImage = $stmt->fetchColumn();

if ($currentImage === false) {
    echo json_encode(['success' => false, 'message' => 'Course not found.']);
    exit;
}

$title = trim($_POST['title'] ?? '');
if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Course title is required.']);
    exit;
}

$description = trim($_POST['description'] ?? '');
$duration = trim($_POST['duration'] ?? '');
$fee = !empty($_POST['fee']) ? (float) $_POST['fee'] : null;
$status = in_array($_POST['status'], ['active', 'inactive']) ? $_POST['status'] : 'active';

$imageFilename = $currentImage;

// Handle new image
if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/courses/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
        exit;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, or GIF images are allowed.']);
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Image must be under 2MB.']);
        exit;
    }

    // Delete old image
    if ($currentImage && file_exists($uploadDir . $currentImage)) {
        unlink($uploadDir . $currentImage);
    }

    $imageFilename = 'course_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $targetPath = $uploadDir . $imageFilename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save image.']);
        exit;
    }
}

try {
    $stmt = $db->prepare("
        UPDATE softedu_courses 
        SET title = ?, description = ?, duration = ?, fee = ?, image = ?, status = ?
        WHERE id = ?
    ");
    $stmt->execute([$title, $description, $duration, $fee, $imageFilename, $status, $id]);

    echo json_encode(['success' => true, 'message' => 'Course updated successfully!']);

} catch (Exception $e) {
    error_log("Edit course error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
?>