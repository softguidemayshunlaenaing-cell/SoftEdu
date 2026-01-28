<?php
require_once __DIR__ . '/../config/db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int) ($_POST['user_id'] ?? 0);

    if (!$user_id || empty($_FILES['doc'])) {
        $response['message'] = 'Missing user ID or file.';
        echo json_encode($response);
        exit;
    }

    $file = $_FILES['doc'];
    $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $response['message'] = 'File type not allowed.';
        echo json_encode($response);
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $response['message'] = 'File too large.';
        echo json_encode($response);
        exit;
    }

    $newName = uniqid() . '.' . $ext;
    $dest = __DIR__ . '/../uploads/user_docs/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("INSERT INTO softedu_user_docs (user_id, file_name, file_path) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $file['name'], $newName]);
        $response['success'] = true;
        $response['message'] = 'Document uploaded!';
    } else {
        $response['message'] = 'Upload failed.';
    }
}

echo json_encode($response);
