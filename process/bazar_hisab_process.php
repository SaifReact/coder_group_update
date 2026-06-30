<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

// Auto-create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS bazar_hisab (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    member_id   INT NOT NULL,
    month       VARCHAR(20) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    paid_amt    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    due_amt     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_member_month (member_id, month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$action      = $_POST['action']      ?? '';
$member_id   = (int)($_POST['member_id']   ?? 0);
$month       = trim($_POST['month']        ?? '');
$total_price = (float)($_POST['total_price'] ?? 0);
$paid_amt    = (float)($_POST['paid_amt']    ?? 0);
$due_amt     = round($total_price - $paid_amt, 2);

if ($action === 'upsert') {
    if (!$member_id || !$month) {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
        exit;
    }
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO bazar_hisab (member_id, month, total_price, paid_amt, due_amt)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                 total_price = VALUES(total_price),
                 paid_amt    = VALUES(paid_amt),
                 due_amt     = VALUES(due_amt)"
        );
        $stmt->execute([$member_id, $month, $total_price, $paid_amt, $due_amt]);
        echo json_encode([
            'success'     => true,
            'total_price' => number_format($total_price, 2),
            'paid_amt'    => number_format($paid_amt, 2),
            'due_amt'     => number_format($due_amt, 2),
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
