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
       SELECT u.id, u.name, u.email, u.password, u.role, u.profile_image,
            IFNULL(s.force_password_change, 0) AS force_password_change,
            IFNULL(s.force_document_upload, 0) AS force_document_upload
       FROM softedu_users u
       LEFT JOIN softedu_students s ON s.user_id = u.id
       WHERE u.email = ?
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
    $_SESSION['show_welcome'] = true; // ✅ SET WELCOME FLAG

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
$_SESSION['show_welcome'] = true; // ✅ SET WELCOME FLAG

echo json_encode([
    'success' => true,
    'redirect' => 'dashboard.php'
]);
?>