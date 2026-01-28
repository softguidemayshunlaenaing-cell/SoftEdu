<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

require_once '../config/db.php';

if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid image file.']);
    exit;
}

$file = $_FILES['profile_image'];
$uploadDir = __DIR__ . '/../../uploads/profiles/';
$maxSize = 2 * 1024 * 1024; // 2MB

// Check file size
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File size must be under 2MB.']);
    exit;
}

// Validate file extension (simple but effective)
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, or GIF images are allowed.']);
    exit;
}

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
        exit;
    }
}

// Generate unique filename
$filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
$targetPath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save image.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Delete old profile image if exists
$stmt = $db->prepare("SELECT profile_image FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$oldImage = $stmt->fetchColumn();

if ($oldImage && file_exists($uploadDir . $oldImage)) {
    unlink($uploadDir . $oldImage);
}

// Update database
$stmt = $db->prepare("UPDATE softedu_users SET profile_image = ? WHERE id = ?");
$stmt->execute([$filename, $_SESSION['user_id']]);

$_SESSION['user_profile_image'] = $filename;

echo json_encode([
    'success' => true,
    'message' => 'Profile picture updated successfully!',
    'filename' => $filename
]);
?>