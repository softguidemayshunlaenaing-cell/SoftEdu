<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/backend/config/db.php';
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT name, email, role, profile_image, force_password_change, force_document_upload FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: index.php?error=Invalid session.');
    exit;
}

// Set session variables
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_profile_image'] = $user['profile_image'] ?? '';

// Check onboarding status for students
$needPassword = (int) $user['force_password_change'] === 1;
$needDocuments = (int) $user['force_document_upload'] === 1;

// Route based on role AND onboarding status
switch ($user['role']) {
    case 'admin':
        include 'admin_dashboard.php';
        break;
    case 'staff':
        include 'staff_dashboard.php';
        break;
    case 'student':
        if ($needPassword || $needDocuments) {
            // Force onboarding before dashboard access
            header('Location: student_onboarding.php');
            exit;
        }

        // Preserve welcome flag
        $showWelcome = $_SESSION['show_welcome'] ?? false;
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