<?php
session_start();
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);
$action = $input['action'] ?? '';
$generated_email = trim($input['generated_email'] ?? ''); // Admin-provided email (optional)

if (!$id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Fetch application
$stmt = $db->prepare("SELECT * FROM softedu_applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    echo json_encode(['success' => false, 'message' => 'Application not found.']);
    exit;
}

$db->beginTransaction();

try {
    $name = $app['name'];
    $toName = $name;

    if ($action === 'approve') {

        // Step 1: Generate default student email if admin did not provide one
        if (!$generated_email) {
            $norm = strtolower(preg_replace('/[^a-z0-9]/', '', $name));
            $generated_email = "softedu.$norm@gmail.com";
        }

        // Step 2: Check email uniqueness
        $stmt = $db->prepare("SELECT id FROM softedu_users WHERE email = ?");
        $stmt->execute([$generated_email]);
        if ($stmt->fetch()) {
            $db->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists. Please choose a unique email.'
            ]);
            exit;
        }

        // Step 3: Default password
        $plainPassword = 'softguide.123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Step 4: Insert new user
        $stmt = $db->prepare("
            INSERT INTO softedu_users (name, email, password, role, force_password_change)
            VALUES (?, ?, ?, 'student', 1)
        ");
        $stmt->execute([$name, $generated_email, $hashedPassword]);

        // Step 5: Send approval email
        $toEmail = $app['email'];

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softguide.mayshunlaenaing@gmail.com';
        $mail->Password = 'odvb zdbx jidl epfa'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Optional: local SSL bypass
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('softguide.mayshunlaenaing@gmail.com', 'SoftGuide');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Application Approved - SoftGuide';
        $mail->Body = "
            <html>
                <body>
                    <p>Dear <b>{$toName}</b>,</p>
                    <p>We are pleased to inform you that your application has been <b>approved</b>.</p>
                    <p>Here are your login credentials for SoftGuide:</p>
                    <ul>
                        <li><b>Email:</b> {$generated_email}</li>
                        <li><b>Password:</b> {$plainPassword}</li>
                    </ul>
                    <p>Please log in and change your password after your first login for security.</p>
                    <br>
                    <p>Thank you for applying with us!</p>
                    <p>Best regards,<br>SoftGuide Team</p>
                </body>
            </html>
        ";
        $mail->AltBody = "Dear {$toName},\n\n"
            . "We are pleased to inform you that your application has been approved.\n\n"
            . "Login credentials:\n"
            . "Email: {$toEmail}\n"
            . "Password: {$plainPassword}\n\n"
            . "Please log in and change your password after first login.\n\n"
            . "Thank you for applying with us!\n"
            . "Best regards,\nSoftGuide Team";

        $mail->send();

        // Step 6: Update application status
        $stmt = $db->prepare("UPDATE softedu_applications SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);

        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Application approved and email sent successfully.',
            'generated_email' => $generated_email
        ]);

    } else {
        // Reject
        $stmt = $db->prepare("UPDATE softedu_applications SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);

        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Application rejected successfully.'
        ]);
    }

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Action failed: ' . $e->getMessage()
    ]);
}
