<?php
include_once __DIR__ . '/../config/config.php';
session_start();

try {
    
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;
    $member_id = $_SESSION['member_id'] ?? null;
    $member_code = $_SESSION['member_code'] ?? null;

    if ($action === 'create') {
        $reasons = trim($_POST['reasons'] ?? '');
        if (empty($reasons)) throw new Exception('Reason is required.');

        // Read computed amounts from POST (ensure numeric)
        $total_deposited = isset($_POST['total_deposited']) ? (float)str_replace(',', '', $_POST['total_deposited']) : 0.0;
        $none_refund = isset($_POST['none_refund']) ? (float)str_replace(',', '', $_POST['none_refund']) : 0.0;
        $samity_share = isset($_POST['samity_share']) ? (float)str_replace(',', '', $_POST['samity_share']) : 0.0;
        $project_share = isset($_POST['project_share']) ? (float)str_replace(',', '', $_POST['project_share']) : 0.0;
        $monthly_deposit = isset($_POST['monthly_deposit']) ? (float)str_replace(',', '', $_POST['monthly_deposit']) : 0.0;
        $total_amt_nr = isset($_POST['total_amt']) ? (float)str_replace(',', '', $_POST['total_amt']) : 0.0;
        $deduction_amt = isset($_POST['deduction']) ? (float)str_replace(',', '', $_POST['deduction']) : 0.0;
        $refund_amt = isset($_POST['refund_amt']) ? (float)str_replace(',', '', $_POST['refund_amt']) : 0.0;
        $agreed = isset($_POST['agreed']) && $_POST['agreed'] ? 1 : 0;
        $member_age = isset($_POST['member_age']) ? (int)$_POST['member_age'] : 0;
        $waiver = 0; // Default waiver to 0, can be updated by admin later
        

        $stmt = $pdo->prepare("INSERT INTO account_close (member_id, member_code, reasons, total_deposited, none_refund, samity_share, project_share, monthly_deposit, total_amt_nr, deduction_amt, refund_amt, waiver, status, agreed, member_age, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$member_id, $member_code, $reasons, $total_deposited, $none_refund, $samity_share, $project_share, $monthly_deposit, $total_amt_nr, $deduction_amt, $refund_amt, $waiver, 'I', $agreed, $member_age, $user_id]);

        $_SESSION['success_msg'] = '✅ হিসাব বন্ধের অনুরোধ জমা দেওয়া হয়েছে (Account close request submitted)..!';
        header('Location: ../users/account_close.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmtCheck = $pdo->prepare("SELECT * FROM account_close WHERE id = ? AND member_id = ? LIMIT 1");
        $stmtCheck->execute([$id, $member_id]);
        $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if (!$exists) throw new Exception('Record not found or access denied.');

        $stmt = $pdo->prepare("DELETE FROM account_close WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_msg'] = '✅ অনুরোধ মুছে ফেলা হয়েছে (Request deleted)..!';
        header('Location: ../users/account_close.php');
        exit;
    }

    // Unknown action
    throw new Exception('Invalid action.');

} catch (Exception $e) {
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
    header('Location: ../users/account_close.php');
    exit;
}
