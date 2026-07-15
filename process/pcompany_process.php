<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php'); exit;
}
include_once __DIR__ . '/../config/config.php';

// Auto-create table
$pdo->exec("CREATE TABLE IF NOT EXISTS pcompany (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    company_name     VARCHAR(150) NOT NULL,
    company_name_bn  VARCHAR(150) NOT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$action = $_POST['action'] ?? '';

try {
    if ($action === 'insert') {
        $name    = trim($_POST['company_name']    ?? '');
        $name_bn = trim($_POST['company_name_bn'] ?? '');
        if ($name === '' || $name_bn === '') throw new Exception('সব ঘর পূরণ করুন।');
        $pdo->prepare("INSERT INTO pcompany (company_name, company_name_bn) VALUES (?,?)")
            ->execute([$name, $name_bn]);
        $_SESSION['success_msg'] = '✅ Company যোগ করা হয়েছে।';
    }

    elseif ($action === 'update') {
        $id      = (int)($_POST['id'] ?? 0);
        $name    = trim($_POST['company_name']    ?? '');
        $name_bn = trim($_POST['company_name_bn'] ?? '');
        if (!$id || $name === '' || $name_bn === '') throw new Exception('সব ঘর পূরণ করুন।');
        $pdo->prepare("UPDATE pcompany SET company_name=?, company_name_bn=? WHERE id=?")
            ->execute([$name, $name_bn, $id]);
        $_SESSION['success_msg'] = '✅ Company হালনাগাদ হয়েছে।';
    }

    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) throw new Exception('Invalid ID.');
        $pdo->prepare("DELETE FROM pcompany WHERE id=?")->execute([$id]);
        $_SESSION['success_msg'] = '✅ Company মুছে ফেলা হয়েছে।';
    }

    else { throw new Exception('Invalid action.'); }

} catch (Exception $e) {
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
}

header('Location: ../admin/pcompany.php'); exit;
