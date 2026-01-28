<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (empty($name) || empty($email) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'All fields marked * are required.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Check duplicate
$stmt = $db->prepare("SELECT id FROM softedu_applications WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'An application with this email already exists.']);
    exit;
}

$stmt = $db->prepare("
    INSERT INTO softedu_applications (name, email, phone, address, township, notes)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $name,
    $email,
    $phone,
    $_POST['address'] ?? '',
    $_POST['township'] ?? '',
    $_POST['notes'] ?? ''
]);

echo json_encode(['success' => true, 'message' => 'Application submitted successfully!']);
?>