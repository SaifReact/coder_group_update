<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Add price columns to monthly_bazar if they don't exist yet
foreach (['buyer_price', 'seller_price', 'profit'] as $col) {
    try { $pdo->exec("ALTER TABLE monthly_bazar ADD COLUMN {$col} DECIMAL(10,2) DEFAULT NULL"); }
    catch (Exception $e) { /* column already exists */ }
}

$action = $_POST['action'] ?? '';

// Admin-only JSON action — must exit before member_id check
if ($action === 'update_prices') {
    header('Content-Type: application/json; charset=utf-8');
    if (($_SESSION['role'] ?? '') !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $ids           = $_POST['ids']           ?? [];
    $buyer_prices  = $_POST['buyer_prices']  ?? [];
    $seller_prices = $_POST['seller_prices'] ?? [];
    $target_member = (int)($_POST['member_id'] ?? 0);
    $target_month  = trim($_POST['month']      ?? '');

    try {
        // 1. Save buyer/seller/profit per row
        $stmt = $pdo->prepare(
            "UPDATE monthly_bazar SET buyer_price=?, seller_price=?, profit=? WHERE id=?"
        );
        foreach ($ids as $i => $id) {
            $id    = (int)$id;
            $buyer = (float)($buyer_prices[$i]  ?? 0);
            $seller= (float)($seller_prices[$i] ?? 0);
            if ($id) $stmt->execute([$buyer, $seller, round($seller - $buyer, 2), $id]);
        }

        // 2. Sync total_price in bazar_hisab from SUM(seller_price)
        if ($target_member && $target_month) {
            // Ensure bazar_hisab table exists
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

            // Get SUM of seller_price for this member+month
            $sumStmt = $pdo->prepare(
                "SELECT COALESCE(SUM(seller_price), 0) AS total
                 FROM monthly_bazar
                 WHERE member_id = ? AND month = ?"
            );
            $sumStmt->execute([$target_member, $target_month]);
            $total_price = (float)$sumStmt->fetchColumn();

            // Get existing paid_amt (keep it unchanged)
            $hisabStmt = $pdo->prepare(
                "SELECT COALESCE(paid_amt, 0) FROM bazar_hisab
                 WHERE member_id = ? AND month = ? LIMIT 1"
            );
            $hisabStmt->execute([$target_member, $target_month]);
            $paid_amt = (float)($hisabStmt->fetchColumn() ?: 0);
            $due_amt  = round($total_price - $paid_amt, 2);

            // Upsert bazar_hisab
            $upsert = $pdo->prepare(
                "INSERT INTO bazar_hisab (member_id, month, total_price, paid_amt, due_amt)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                     total_price = VALUES(total_price),
                     due_amt     = VALUES(due_amt)"
            );
            $upsert->execute([$target_member, $target_month, $total_price, $paid_amt, $due_amt]);

            echo json_encode([
                'success'     => true,
                'message'     => 'Prices saved!',
                'total_price' => number_format($total_price, 2),
                'paid_amt'    => number_format($paid_amt, 2),
                'due_amt'     => number_format($due_amt, 2),
            ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Prices saved!']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Admin-only: Approve bazar + insert bazar_transaction
if ($action === 'approve_with_transaction') {
    header('Content-Type: application/json; charset=utf-8');
    if (($_SESSION['role'] ?? '') !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $member_id     = (int)($_POST['member_id']      ?? 0);
    $member_code   = trim($_POST['member_code']      ?? '');
    $month         = trim($_POST['month']            ?? '');
    $no_product    = (int)($_POST['no_product']      ?? 0);
    $price         = (float)($_POST['price']         ?? 0);
    $discount      = (float)($_POST['discount']      ?? 0);
    $service_charge= (float)($_POST['service_charge']?? 0);
    $sum_price     = (float)($_POST['sum_price']     ?? 0);
    $due_price     = (float)($_POST['due_price']     ?? 0);

    if (!$member_id || !$month) {
        echo json_encode(['success' => false, 'message' => 'অবৈধ অনুরোধ।']);
        exit;
    }

    try {

        // Upsert bazar_transaction
        $pdo->prepare(
            "INSERT INTO bazar_transaction
                (member_id, member_code, month, no_product, price, discount, service_charge, sum_price, due_price)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                no_product=VALUES(no_product), price=VALUES(price),
                discount=VALUES(discount), service_charge=VALUES(service_charge),
                sum_price=VALUES(sum_price), due_price=VALUES(due_price)"
        )->execute([$member_id, $member_code, $month, $no_product,
                    $price, $discount, $service_charge, $sum_price, $due_price]);

        // Set monthly_bazar status = Approved
        $pdo->prepare(
            "UPDATE monthly_bazar SET status='A' WHERE member_id=? AND month=?"
        )->execute([$member_id, $month]);

        echo json_encode(['success' => true, 'message' => 'সফলভাবে অনুমোদন করা হয়েছে!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Admin-only: update paid_price and recalculate due_price
if ($action === 'update_paid_price') {
    header('Content-Type: application/json; charset=utf-8');
    if (($_SESSION['role'] ?? '') !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $member_id  = (int)($_POST['member_id']  ?? 0);
    $month      = trim($_POST['month']       ?? '');
    $paid_price = (float)($_POST['paid_price'] ?? 0);
    $due_price  = (float)($_POST['due_price']  ?? 0);

    if (!$member_id || !$month) {
        echo json_encode(['success' => false, 'message' => 'অবৈধ অনুরোধ।']);
        exit;
    }

    try {
        $pdo->prepare(
            "UPDATE bazar_transaction SET paid_price=?, due_price=? WHERE member_id=? AND month=?"
        )->execute([$paid_price, $due_price, $member_id, $month]);
        echo json_encode(['success' => true, 'message' => 'সফলভাবে সংরক্ষণ হয়েছে।']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$member_id = $_SESSION['member_id'] ?? 0;

try {
    if ($action === 'insert_bulk') {
        $month         = trim($_POST['month']        ?? '');
        $product_names = $_POST['product_name']      ?? [];
        $quantities    = $_POST['quantity']          ?? [];
        $companies     = $_POST['company']           ?? [];
        $remarks_arr   = $_POST['remarks']           ?? [];
        $seller_prices = $_POST['seller_price']      ?? [];

        if (!$month || empty($product_names)) {
            $_SESSION['error_msg'] = "Month and at least one product row are required!";
            header("Location: ../users/monthly_bazar.php");
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO monthly_bazar (member_id, month, product_name, quantity, company, remarks, seller_price, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'I')"
        );

        $qty_amounts = $_POST['qty_amount'] ?? [];
        $qty_units   = $_POST['qty_unit']   ?? [];

        $inserted = 0;
        foreach ($product_names as $i => $product_name) {
            $product_name = trim($product_name);
            $qty_amount   = trim($qty_amounts[$i] ?? '');
            $qty_unit     = trim($qty_units[$i]   ?? '');
            $quantity     = $qty_amount !== '' ? $qty_amount . ' ' . $qty_unit : trim($quantities[$i] ?? '');
            $company      = trim($companies[$i]   ?? '');
            $remarks      = trim($remarks_arr[$i] ?? '');
            $seller_price = (float)($seller_prices[$i] ?? 0);

            if ($product_name && $quantity) {
                $stmt->execute([$member_id, $month, $product_name, $quantity, $company, $remarks, $seller_price]);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            $_SESSION['success_msg'] = "✅ {$inserted} record(s) added successfully! ({$inserted}টি তথ্য সফলভাবে যোগ করা হয়েছে!)";
        } else {
            $_SESSION['error_msg'] = "No valid rows found. Product Name, Quantity and Company are required per row.";
        }
        header("Location: ../users/monthly_bazar.php");
        exit;
    }

    if ($action === 'update') {
        $id           = (int)($_POST['id']              ?? 0);
        $month        = trim($_POST['month']            ?? '');
        $product_name = trim($_POST['product_name']     ?? '');
        $qty_amount   = trim($_POST['edit_qty_amount']  ?? '');
        $qty_unit     = trim($_POST['edit_qty_unit']    ?? '');
        $quantity     = $qty_amount !== '' ? $qty_amount . ' ' . $qty_unit : '';
        $company      = trim($_POST['company']          ?? '');
        $remarks      = trim($_POST['remarks']          ?? '');
        $seller_price = (float)($_POST['seller_price']  ?? 0);

        if ($id && $month && $product_name && $quantity) {
            $stmt = $pdo->prepare(
                "UPDATE monthly_bazar
                 SET month=?, product_name=?, quantity=?, company=?, remarks=?, seller_price=?
                 WHERE id=? AND member_id=?"
            );
            $stmt->execute([$month, $product_name, $quantity, $company, $remarks, $seller_price, $id, $member_id]);
            $_SESSION['success_msg'] = "✅ Record updated successfully! (সফলভাবে হালনাগাদ করা হয়েছে!)";
        } else {
            $_SESSION['error_msg'] = "All required fields must be filled!";
        }
        header("Location: ../users/monthly_bazar.php");
        exit;
    }

    if ($action === 'update_status') {
        // Admin only
        if (($_SESSION['role'] ?? '') !== 'Admin') {
            header('Location: ../login.php');
            exit;
        }
        $target_member_id = (int)($_POST['member_id'] ?? 0);
        $month  = trim($_POST['month']  ?? '');
        $status = in_array($_POST['status'] ?? '', ['I','P','A','R']) ? $_POST['status'] : 'I';

        if ($target_member_id && $month) {
            $stmt = $pdo->prepare(
                "UPDATE monthly_bazar SET status=? WHERE member_id=? AND month=?"
            );
            $stmt->execute([$status, $target_member_id, $month]);

            $labels = ['I' => '⏳ Pending', 'P' => '⏳ Processing (প্রক্রিয়াধীন)', 'A' => '✅ Approved (অনুমোদিত)', 'R' => '❌ Rejected (বাতিল)'];
            $_SESSION['success_msg'] = ($labels[$status] ?? $status) . ' — ' . $month . ' বাজার তালিকা হালনাগাদ করা হয়েছে।';
        } else {
            $_SESSION['error_msg'] = 'Invalid request.';
        }
        header('Location: ../admin/bazar_approve.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM monthly_bazar WHERE id=? AND member_id=?");
            $stmt->execute([$id, $member_id]);
            $_SESSION['success_msg'] = "✅ Record deleted successfully! (সফলভাবে মুছে ফেলা হয়েছে!)";
        }
        header("Location: ../users/monthly_bazar.php");
        exit;
    }

    $_SESSION['error_msg'] = "Invalid action.";
    header("Location: ../users/monthly_bazar.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    header("Location: ../users/monthly_bazar.php");
    exit;
}
