<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'softguide.mayshunlaenaing@gmail.com';
    $mail->Password = 'odvb zdbx jidl epfa'; // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('softguide.mayshunlaenaing@gmail.com', 'SoftGuide');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('softguide.mayshunlaenaing@gmail.com', 'SoftGuide');

    $mail->isHTML(true);
    $mail->Subject = "Contact Form: $subject";
    $mail->Body = "
        <html><body>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Subject:</strong> {$subject}</p>
        <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
        </body></html>
    ";
    $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
} catch (Exception $e) {
    // Hide PHPMailer error to user
    echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
}
