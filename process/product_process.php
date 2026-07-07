<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php'); exit;
}
include_once __DIR__ . '/../config/config.php';

// Auto-create table
$pdo->exec("CREATE TABLE IF NOT EXISTS product (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT NOT NULL DEFAULT 0,
    company_id      INT NOT NULL DEFAULT 0,
    product_name    VARCHAR(200) NOT NULL,
    product_name_bn VARCHAR(200) NOT NULL,
    buyer_price     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    seller_price    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    profit          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status          ENUM('A','I') NOT NULL DEFAULT 'A',
    for_uses        ENUM('b','e','be') NOT NULL DEFAULT 'b',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$action = $_POST['action'] ?? '';

try {
    if ($action === 'insert') {
        $category_id     = (int)($_POST['category_id']     ?? 0);
        $company_id      = (int)($_POST['company_id']      ?? 0);
        $product_name    = trim($_POST['product_name']     ?? '');
        $product_name_bn = trim($_POST['product_name_bn']  ?? '');
        $buyer_price     = (float)($_POST['buyer_price']   ?? 0);
        $seller_price    = (float)($_POST['seller_price']  ?? 0);
        $profit          = round($seller_price - $buyer_price, 2);
        $status          = in_array($_POST['status'] ?? '', ['A','I']) ? $_POST['status'] : 'A';
        $for_uses        = in_array($_POST['for_uses'] ?? '', ['b','e','be']) ? $_POST['for_uses'] : 'b';

        if ($product_name === '' || $product_name_bn === '') throw new Exception('পণ্যের নাম দিতে হবে।');

        $pdo->prepare("INSERT INTO product
            (category_id, company_id, product_name, product_name_bn, buyer_price, seller_price, profit, status, for_uses)
            VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$category_id, $company_id, $product_name, $product_name_bn,
                       $buyer_price, $seller_price, $profit, $status, $for_uses]);
        $_SESSION['success_msg'] = '✅ পণ্য যোগ করা হয়েছে।';
    }

    elseif ($action === 'update') {
        $id              = (int)($_POST['id']               ?? 0);
        $category_id     = (int)($_POST['category_id']      ?? 0);
        $company_id      = (int)($_POST['company_id']       ?? 0);
        $product_name    = trim($_POST['product_name']      ?? '');
        $product_name_bn = trim($_POST['product_name_bn']   ?? '');
        $buyer_price     = (float)($_POST['buyer_price']    ?? 0);
        $seller_price    = (float)($_POST['seller_price']   ?? 0);
        $profit          = round($seller_price - $buyer_price, 2);
        $status          = in_array($_POST['status'] ?? '', ['A','I']) ? $_POST['status'] : 'A';
        $for_uses        = in_array($_POST['for_uses'] ?? '', ['b','e','be']) ? $_POST['for_uses'] : 'b';

        if (!$id || $product_name === '' || $product_name_bn === '') throw new Exception('সব ঘর পূরণ করুন।');

        $pdo->prepare("UPDATE product
            SET category_id=?, company_id=?, product_name=?, product_name_bn=?,
                buyer_price=?, seller_price=?, profit=?, status=?, for_uses=?
            WHERE id=?")
            ->execute([$category_id, $company_id, $product_name, $product_name_bn,
                       $buyer_price, $seller_price, $profit, $status, $for_uses, $id]);
        $_SESSION['success_msg'] = '✅ পণ্য হালনাগাদ হয়েছে।';
    }

    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) throw new Exception('Invalid ID.');
        $pdo->prepare("DELETE FROM product WHERE id=?")->execute([$id]);
        $_SESSION['success_msg'] = '✅ পণ্য মুছে ফেলা হয়েছে।';
    }

    else { throw new Exception('Invalid action.'); }

} catch (Exception $e) {
    $_SESSION['error_msg'] = '❌ ' . $e->getMessage();
}

header('Location: ../admin/product.php'); exit;
