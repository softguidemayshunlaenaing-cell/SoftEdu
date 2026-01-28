<?php
header('Content-Type: application/json');

session_start();
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')
) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

require_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

/* ======================
   Get & validate inputs
   ====================== */
$course_id = (int) ($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$material_type = $_POST['material_type'] ?? '';
$source = $_POST['source'] ?? '';
$material_url = trim($_POST['material_url'] ?? '');

if (!$course_id || empty($title) || empty($material_url)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!in_array($material_type, ['video', 'pdf'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid material type.']);
    exit;
}

if (!in_array($source, ['youtube', 'google_drive', 'external'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid source.']);
    exit;
}

/* ======================
   STEP 2: Check course exists
   (NOT checking status)
   ====================== */
$stmt = $db->prepare("SELECT id FROM softedu_courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    echo json_encode(['success' => false, 'message' => 'Course not found.']);
    exit;
}

/* ======================
   Insert material
   ====================== */
try {
    $stmt = $db->prepare("
        INSERT INTO softedu_course_materials
        (course_id, title, material_type, source, material_url, uploaded_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $course_id,
        $title,
        $material_type,
        $source,
        $material_url,
        $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Material added successfully!']);

} catch (Exception $e) {
    error_log('Add material error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to add material.']);
}
