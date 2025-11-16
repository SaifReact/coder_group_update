<?php
include_once __DIR__ . '/../config/config.php';
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    echo json_encode(['error' => 'Invalid project id']);
    exit;
}
$project_id = (int)$_GET['project_id'];
$stmt = $pdo->prepare("SELECT * FROM project WHERE id = ? LIMIT 1");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) {
    echo json_encode(['error' => 'This is not Project']);
    exit;
}
echo json_encode($project);
