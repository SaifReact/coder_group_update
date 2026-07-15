<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }

include_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$bank_trans = trim($_GET['bank_trans'] ?? '');
if ($bank_trans === '') {
    echo json_encode(['duplicate' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM member_payments WHERE bank_trans_no = ?");
$stmt->execute([$bank_trans]);
$count = (int)$stmt->fetchColumn();

echo json_encode(['duplicate' => $count > 0]);
