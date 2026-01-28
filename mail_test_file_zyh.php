<?php
session_start();
header('Content-Type: application/json');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Make sure you include PHPMailer via Composer or manually
require '../../vendor/autoload.php';
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    http_response_code(403);
    exit;
}

require_once '../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id'] ?? 0);
$action = $input['action'] ?? '';
$generated_email = trim($input['generated_email'] ?? ''); // ✅ NEW: admin-edited email

if (!$id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Fetch application
$stmt = $db->prepare("SELECT * FROM softedu_applications WHERE id = ?");
$stmt->execute([$id]);
// $stmt->execute([$id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    echo json_encode(['success' => false, 'message' => 'Application not found.']);
    exit;
}

$db->beginTransaction();

try {
    if ($action === 'approve') {
        $name = $app['name'];
        $status = $app['status'];
        $toName = $app['name'];

        // Step 1: Generate default student email if admin did not provide one
        if (!$generated_email) {
            $norm = strtolower(preg_replace('/[^a-z0-9]/', '', $name));
            $generated_email = "softedu.$norm@gmail.com";
        }

        // Step 2: Check email uniqueness
        $stmt = $db->prepare("SELECT id FROM softedu_users WHERE email = ?");
        $stmt->execute([$generated_email]);
        if ($stmt->fetch()) {
            // Email already exists
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

        $mail = new PHPMailer(true); // Enable exceptions
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softguide.mayshunlaenaing@gmail.com'; // Your Gmail
        $mail->Password = 'odvb zdbx jidl epfa';                   // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Optional: For local testing if SSL verification fails
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Recipients
        $mail->setFrom('softguide.mayshunlaenaing@gmail.com', 'SoftGuide'); // Must match Gmail account


        $mail->addAddress($app['email'], $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Notification';
        $mail->Body = "
            <html>
                <body>
                    <p>Dear <b>{$toName}</b>,</p>
                    <p>We are pleased to inform you that your application has been <b>{$status}</b>.</p>
                    <p>Thank you for applying with us!</p>
                    <br>
                    <p>Best regards,<br>SoftGuide Team</p>
                </body>
            </html>
        ";
        $mail->AltBody = "Dear {$toName},\n\nYour application has been {$status}.\n\nThank you for applying with us!\n\nBest regards,\nSoftGuide Team";

        $mail->send();
        return ['success' => true, 'message' => 'Approval email sent successfully to '];
        // Step 5: Update application status
        $stmt = $db->prepare("UPDATE softedu_applications SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);

    } else {
        // Reject
        $stmt = $db->prepare("UPDATE softedu_applications SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => ucfirst($action) . 'd successfully.',
        'generated_email' => $generated_email // optional: return generated email
    ]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Action failed: ' . $e->getMessage() // ✅ show error for debugging
    ]);
}
