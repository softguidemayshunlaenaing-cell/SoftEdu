<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password required.'
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("
    SELECT id, name, email, password, role, profile_image, force_password_change
    FROM softedu_users
    WHERE email = ?
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ❌ Wrong login
if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email or password.'
    ]);
    exit;
}

/*
 STEP 3:
 If student uses default password,
 force them to change it first
*/
if ($user['role'] === 'student' && (int) $user['force_password_change'] === 1) {

    // set session first
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode([
        'success' => true,
        'redirect' => 'student_onboarding.php'
    ]);
    exit;
}

// ✅ Normal login
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_profile_image'] = $user['profile_image'] ?? '';

echo json_encode([
    'success' => true,
    'redirect' => 'dashboard.php'
]);
