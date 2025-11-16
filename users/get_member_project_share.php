<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include_once __DIR__ . '/../config/config.php';

if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    echo json_encode(['error' => 'Invalid project id']);
    exit;
}

$project_id = (int)$_GET['project_id'];
$member_id = $_SESSION['member_id'];
$member_code = $_SESSION['member_code'];

// Fetch member's project share for the specific project
$stmt = $pdo->prepare("SELECT project_share FROM member_project WHERE member_id = ? AND member_code = ? AND project_id = ? LIMIT 1");
$stmt->execute([$member_id, $member_code, $project_id]);
$member_project = $stmt->fetch(PDO::FETCH_ASSOC);

if ($member_project) {
    echo json_encode(['project_share' => $member_project['project_share']]);
} else {
    echo json_encode(['project_share' => '0']);
}
?>