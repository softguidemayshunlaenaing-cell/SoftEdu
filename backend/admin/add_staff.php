<?php
session_start();
header('Content-Type: application/json');

if ($_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// Check email uniqueness
$stmt = $db->prepare("SELECT id FROM softedu_users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Email already exists.']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$imageFilename = null;

// Handle image upload
if (!empty($_FILES['profile_image']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/profiles/';
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0755, true);

    $file = $_FILES['profile_image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
        $imageFilename = 'staff_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $imageFilename);
    }
}

try {
    $stmt = $db->prepare("
        INSERT INTO softedu_users (name, email, password, role, profile_image)
        VALUES (?, ?, ?, 'staff', ?)
    ");
    $stmt->execute([$name, $email, $hashedPassword, $imageFilename]);
    echo json_encode(['success' => true, 'message' => 'Staff added successfully!']);
} catch (Exception $e) {
    error_log("Add staff error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>