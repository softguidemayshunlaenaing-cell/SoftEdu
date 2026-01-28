<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/backend/config/db.php';
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT name, email, role, profile_image FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: index.php?error=Invalid session.');
    exit;
}

// Preserve welcome flag for students
$showWelcome = $_SESSION['show_welcome'] ?? false;

// Set session variables
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_profile_image'] = $user['profile_image'] ?? '';

// Route based on role
switch ($user['role']) {
    case 'admin':
        include 'admin_dashboard.php';
        break;
    case 'staff':
        include 'staff_dashboard.php';
        break;
    case 'student':
        // Preserve welcome flag for student dashboard
        if ($showWelcome) {
            $_SESSION['show_welcome'] = true;
        }
        include 'student_dashboard.php';
        break;
    default:
        session_destroy();
        header('Location: index.php');
        exit;
}
?>