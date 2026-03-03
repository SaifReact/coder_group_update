<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

header('Content-Type: application/json');
include_once __DIR__ . '/../config/config.php';

$response = ['success' => false];

try {
    if (!isset($_SESSION['member_id'])) {
        throw new Exception('Unauthorized.');
    }
    $member_id = $_SESSION['member_id'];
    $id = $_POST['id'] ?? null;
    $for_fees = $_POST['for_fees'] ?? '';
    $amount = $_POST['amount'] ?? '';

    if (!$id || !$for_fees || !$amount) {
        throw new Exception('All fields are required.');
    }

    // Only allow update if the payment belongs to the logged-in member and is still pending (status = 'I')
    $stmt = $pdo->prepare('SELECT id FROM member_payments WHERE id = ? AND member_id = ? AND status = ?');
    $stmt->execute([$id, $member_id, 'I']);
    if (!$stmt->fetch()) {
        throw new Exception('Payment not found or not editable.');
    }

    $payment_year = date('Y');
    $trans_no = 'TR' . strtoupper($for_fees) . $payment_year . $id;

    $stmt = $pdo->prepare('UPDATE member_payments SET trans_no = ?, for_fees = ?, amount = ? WHERE id = ? AND member_id = ? AND status = ?');
    $stmt->execute([$trans_no, $for_fees, $amount, $id, $member_id, 'I']);

    $response['success'] = true;
    $response['message'] = 'Payment updated successfully.';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);