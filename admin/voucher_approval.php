<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$user_id = $_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

// Fetch all voucher_payments table with status 'I' (pending) and group debit-credit pairs by date and amount

$stmtVoucher = $pdo->query("
    SELECT 
        CONCAT(MIN(v.id), ',', MAX(v.id)) as transaction_ids,
        v.tran_date,
        v.tran_amount,
        v.remarks,
        MAX(CASE WHEN v.drcr_code = 'D' THEN v.id ELSE NULL END) as debit_id,
        MAX(CASE WHEN v.drcr_code = 'C' THEN v.id ELSE NULL END) as credit_id,
        MAX(CASE WHEN v.drcr_code = 'D' THEN g.glac_name ELSE NULL END) as debit_glac_name,
        MAX(CASE WHEN v.drcr_code = 'D' THEN g.glac_code ELSE NULL END) as debit_glac_code,
        MAX(CASE WHEN v.drcr_code = 'D' THEN g.id ELSE NULL END) as debit_glac_id,
        MAX(CASE WHEN v.drcr_code = 'C' THEN g.glac_name ELSE NULL END) as credit_glac_name,
        MAX(CASE WHEN v.drcr_code = 'C' THEN g.glac_code ELSE NULL END) as credit_glac_code,
        MAX(CASE WHEN v.drcr_code = 'C' THEN g.id ELSE NULL END) as credit_glac_id,
        MAX(CASE WHEN v.drcr_code = 'D' THEN v.status ELSE NULL END) as debit_status,
        MAX(CASE WHEN v.drcr_code = 'C' THEN v.status ELSE NULL END) as credit_status
    FROM voucher_payments v 
    JOIN glac_mst g ON v.glac_id = g.id 
    WHERE v.status = 'I' 
    GROUP BY v.tran_date, v.tran_amount, v.remarks
    ORDER BY v.id DESC
");
$voucherPayments = $stmtVoucher->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($method === 'POST' && isset($_POST['status'])) {

    $status = in_array($_POST['status'], ['A', 'I', 'R']) ? $_POST['status'] : 'I';

    // Get both debit and credit transaction IDs
    $debit_id = (int)($_POST['debit_id'] ?? 0);
    $credit_id = (int)($_POST['credit_id'] ?? 0);

    if ($debit_id > 0 && $credit_id > 0) {
        // Get debit transaction data
        $stmtDebit = $pdo->prepare("SELECT * FROM voucher_payments WHERE id = ? LIMIT 1");
        $stmtDebit->execute([$debit_id]);
        $debitData = $stmtDebit->fetch(PDO::FETCH_ASSOC);

        // Get credit transaction data
        $stmtCredit = $pdo->prepare("SELECT * FROM voucher_payments WHERE id = ? LIMIT 1");
        $stmtCredit->execute([$credit_id]);
        $creditData = $stmtCredit->fetch(PDO::FETCH_ASSOC);

        if ($debitData && $creditData) {
            $debit_glac_id = (int)$debitData['glac_id'];
            $credit_glac_id = (int)$creditData['glac_id'];
            $tran_amount = $debitData['tran_amount'] ?? 0;

            // If not approving, just update both transactions
            if ($status !== 'A') {
                $stmtUpdate = $pdo->prepare("UPDATE voucher_payments SET status = ? WHERE id IN (?, ?)");
                $stmtUpdate->execute([$status, $debit_id, $credit_id]);
            }

            // If approving, update GL summary for both debit and credit accounts
            if ($status === 'A') {
                // Update Debit Account GL Summary
                $stmtCheckDebitGL = $pdo->prepare("SELECT * FROM gl_summary WHERE glac_id = ? LIMIT 1");
                $stmtCheckDebitGL->execute([$debit_glac_id]);   
                $debitGLData = $stmtCheckDebitGL->fetch(PDO::FETCH_ASSOC);

                if ($debitGLData) {
                    $new_debit = $debitGLData['debit_amount'] + $tran_amount;
                    $stmtUpdateDebitGL = $pdo->prepare("UPDATE gl_summary SET tran_date = ?, debit_amount = ?, created_by = ? WHERE glac_id = ?");
                    $stmtUpdateDebitGL->execute([date('Y-m-d'), $new_debit, $user_id, $debit_glac_id]);
                } else {
                    $stmtInsertDebitGL = $pdo->prepare("INSERT INTO gl_summary (glac_id, tran_date, debit_amount, credit_amount, created_by) VALUES (?, ?, ?, ?, ?)");
                    $stmtInsertDebitGL->execute([$debit_glac_id, date('Y-m-d'), $tran_amount, 0, $user_id]);
                }

                // Update Credit Account GL Summary
                $stmtCheckCreditGL = $pdo->prepare("SELECT * FROM gl_summary WHERE glac_id = ? LIMIT 1");
                $stmtCheckCreditGL->execute([$credit_glac_id]);   
                $creditGLData = $stmtCheckCreditGL->fetch(PDO::FETCH_ASSOC);

                if ($creditGLData) {
                    $new_credit = $creditGLData['credit_amount'] + $tran_amount;
                    $stmtUpdateCreditGL = $pdo->prepare("UPDATE gl_summary SET tran_date = ?, credit_amount = ?, created_by = ? WHERE glac_id = ?");
                    $stmtUpdateCreditGL->execute([date('Y-m-d'), $new_credit, $user_id, $credit_glac_id]);
                } else {
                    $stmtInsertCreditGL = $pdo->prepare("INSERT INTO gl_summary (glac_id, tran_date, debit_amount, credit_amount, created_by) VALUES (?, ?, ?, ?, ?)");
                    $stmtInsertCreditGL->execute([$credit_glac_id, date('Y-m-d'), 0, $tran_amount, $user_id]);
                }

                // Update both transaction statuses
                $stmtApprove = $pdo->prepare("UPDATE voucher_payments SET status = ? WHERE id IN (?, ?)");
                $stmtApprove->execute([$status, $debit_id, $credit_id]);
            }
        }
    }
    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ ভাউচার পোস্টিং অনুমোদন দেয়া হলো !";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ ভাউচার পোস্টিং নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ ভাউচার পোস্টিং বাতিল করা হইলো !";
    }

    // Stay on the same page with success message
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>
    <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
            <div class="card shadow-lg rounded-3 border-0">
                <div class="card-body p-4">
                    <h3 class="mb-3 text-primary fw-bold">Voucher Posting Approval <span class="text-secondary">( ভাউচার পোস্টিং অনুমোদন )</span></h3> 
                    <hr class="mb-4" />
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>নং </th>
                                        <th>তারিখ</th>
                                        <th>ডেবিট জি.এল</th>
                                        <th>ডেবিট</th>
                                        <th>ক্রেডিট জি.এল</th>
                                        <th>ক্রেডিট</th>
                                        <th>মন্তব্য</th>
                                        <th colspan="1">কর্মকান্ড</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($voucherPayments as $transaction): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($transaction['transaction_ids']) ?></td>
                                        <td><?= htmlspecialchars($transaction['tran_date']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($transaction['debit_glac_id']) ?></br>
                                            <?= htmlspecialchars($transaction['debit_glac_name']) ?></br>
                                            <?= htmlspecialchars($transaction['debit_glac_code']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($transaction['tran_amount']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($transaction['credit_glac_id']) ?></br>
                                            <?= htmlspecialchars($transaction['credit_glac_name']) ?></br>
                                            <?= htmlspecialchars($transaction['credit_glac_code']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($transaction['tran_amount']) ?></td>
                                        <td><?= htmlspecialchars($transaction['remarks']) ?></td>
                                        <td>
                                            <form method="post" class="d-flex align-items-center">
                                                <input type="hidden" name="debit_id" value="<?= $transaction['debit_id'] ?>">
                                                <input type="hidden" name="credit_id" value="<?= $transaction['credit_id'] ?>">
                                                <select name="status" class="form-select form-select-sm me-2">
                                                    <option value="A">✅ Approved</option>
                                                    <option value="I" selected>⏸️ Inactive</option>
                                                    <option value="R">❌ Rejected</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">Update (হালনাগাদ)</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </main>
  </div>
</div>
<!-- Hero End -->

<?php include_once __DIR__ . '/../includes/end.php'; ?>


