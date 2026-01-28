<?php
header('Content-Type: application/json');
require_once '../config/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../vendor/autoload.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-admin@gmail.com';      // ← CHANGE
    $mail->Password = 'your-app-password';        // ← CHANGE
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom($email, $name);
    $mail->addAddress('your-admin@gmail.com', 'SoftEdu Admin');
    $mail->Subject = 'New Contact Message';
    $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Thank you! We’ll get back to you soon.']);
} catch (Exception $e) {
    error_log("Contact email failed: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
}
?>