<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = (int) $_GET['user_id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT nrc_front, nrc_back, thangounsayin_front, thangounsayin_back FROM softedu_users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([]);
    exit;
}

$fields = ['nrc_front', 'nrc_back', 'thangounsayin_front', 'thangounsayin_back'];
$result = [];

foreach ($fields as $field) {
    if (!empty($user[$field])) {
        // ✅ Use absolute URL to avoid CORS
        $host = $_SERVER['HTTP_HOST'];
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = "$scheme://$host/softedu";
        $result[] = [
            'file_name' => $user[$field],
            'file_url' => $baseUrl . '/uploads/documents/' . $user[$field]
        ];
    }
}

echo json_encode($result);
?>