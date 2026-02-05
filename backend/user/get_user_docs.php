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

$stmt = $db->prepare("SELECT nrc_front, nrc_back, thangounsayin_front, thangounsayin_back FROM softedu_students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo json_encode([]);
    exit;
}

$fields = ['nrc_front', 'nrc_back', 'thangounsayin_front', 'thangounsayin_back'];
$result = [];

foreach ($fields as $field) {
    if (!empty($student[$field])) {
        // ✅ Use absolute URL to avoid CORS
        $host = $_SERVER['HTTP_HOST'];
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = "$scheme://$host/softedu";
        $result[] = [
            'file_name' => $student[$field],
            'file_url' => $baseUrl . '/uploads/documents/' . $student[$field]
        ];
    }
}

echo json_encode($result);
?>