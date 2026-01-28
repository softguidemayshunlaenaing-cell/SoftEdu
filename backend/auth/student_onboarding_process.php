<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

require_once '../config/db.php';
$db = (new Database())->getConnection();

try {
    // Fetch user
    $stmt = $db->prepare("SELECT * FROM softedu_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    $needPassword = (int) $user['force_password_change'] === 1;
    $needDocuments = (int) $user['force_document_upload'] === 1;

    // === VALIDATION FIRST ===
    if ($needPassword) {
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$new || !$confirm) {
            throw new Exception('Password fields are required');
        }
        if ($new !== $confirm) {
            throw new Exception('Passwords do not match');
        }
    }

    $uploads = [];

    if ($needDocuments) {
        $requiredFiles = [
            'nrc_front',
            'nrc_back',
            'thangounsayin_front',
            'thangounsayin_back'
        ];

        foreach ($requiredFiles as $file) {
            if (!isset($_FILES[$file]) || $_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please upload all required documents');
            }

            $mime = mime_content_type($_FILES[$file]['tmp_name']);
            if (!in_array($mime, ['image/jpeg', 'image/png'])) {
                throw new Exception('Only JPG and PNG allowed');
            }
        }
    }

    // === START TRANSACTION ===
    $db->beginTransaction();

    // Password update
    if ($needPassword) {
        $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            UPDATE softedu_users 
            SET password = ?, force_password_change = 0 
            WHERE id = ?
        ");
        $stmt->execute([$hashed, $user['id']]);
    }

    // Document upload
    if ($needDocuments) {
        foreach ($requiredFiles as $file) {
            $ext = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
            $filename = "{$user['id']}_{$file}_" . time() . ".{$ext}";
            $path = "../../uploads/documents/{$filename}";

            if (!move_uploaded_file($_FILES[$file]['tmp_name'], $path)) {
                throw new Exception("Failed to upload {$file}");
            }

            $uploads[$file] = $filename;
        }

        $stmt = $db->prepare("
            UPDATE softedu_users 
            SET 
                nrc_front = ?,
                nrc_back = ?,
                thangounsayin_front = ?,
                thangounsayin_back = ?,
                force_document_upload = 0
            WHERE id = ?
        ");

        $stmt->execute([
            $uploads['nrc_front'],
            $uploads['nrc_back'],
            $uploads['thangounsayin_front'],
            $uploads['thangounsayin_back'],
            $user['id']
        ]);
    }

    // ✅ ALL GOOD
    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Onboarding completed successfully'
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
