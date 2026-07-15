<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$user_id = $_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

// Fetch all payments
$stmt = $pdo->query("SELECT 
    a.id,
    a.member_id,
    a.member_code,
    a.payment_year,
    a.payment_method,
    a.tran_type,
    a.payment_slip,
    a.remarks,
    CASE 
        WHEN a.project_id = 0 && a.payment_method = 'Monthly' THEN a.for_fees
        ELSE ''
    END AS for_fees,
    a.bank_pay_date,
    a.project_id,
    a.bank_trans_no,
    a.trans_no,
    a.amount,
    a.status,
    c.name_en,
    c.name_bn,
    c.mobile,
    COALESCE(b.id, 0) AS member_project_id,
    CASE 
        WHEN a.project_id = 0 && a.payment_method = 'Monthly' THEN ' (মাসিক ফি)'
        WHEN a.project_id = 0 && a.payment_method = 'Late' THEN ' (বিলম্ব ফি)'
        WHEN a.project_id = 0 THEN 'ভর্তি ফি'
        WHEN a.project_id = 1 THEN 'সমিতি শেয়ার ফি'
        ELSE p.project_name_bn
    END AS project_title,
    COALESCE(sh.total_extra_share, 0) AS no_share -- এখানে টোটাল শেয়ার ৩টি দেখাবে
FROM member_payments a
LEFT JOIN (
    SELECT mp.*
    FROM member_project mp
    INNER JOIN (
        SELECT member_id, member_code, project_id, MAX(id) AS max_id
        FROM member_project
        GROUP BY member_id, member_code, project_id
    ) latest
        ON mp.member_id = latest.member_id
       AND mp.member_code = latest.member_code
       AND mp.project_id = latest.project_id
       AND mp.id = latest.max_id
) b 
    ON a.member_id = b.member_id 
   AND a.member_code = b.member_code
   AND a.project_id = b.project_id 
INNER JOIN members_info c 
    ON a.member_id = c.id 
   AND a.member_code = c.member_code
LEFT JOIN project p 
    ON a.project_id = p.id
-- সমাধান অংশ: প্রজেক্ট আইডি ছাড়া শুধুমাত্র মেম্বার আইডি ধরে SUM করা হয়েছে
LEFT JOIN (
    SELECT member_id, member_code, SUM(extra_share) AS total_extra_share
    FROM member_share
    GROUP BY member_id, member_code
) sh
    ON a.member_id = sh.member_id
   AND a.member_code = sh.member_code -- এখানে প্রজেক্ট আইডি দেওয়া হয়নি
WHERE a.status IN ('R', 'I')
ORDER BY a.id DESC");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($method === 'POST' && isset($_POST['member_id'], $_POST['member_code'], $_POST['pay_id'], $_POST['status'])) {
    $member_id = (int)$_POST['member_id'];
    $member_code = $_POST['member_code'];
    $member_project_id = (int)$_POST['member_project_id'];
    $pay_id = (int)$_POST['pay_id'];
    $status = in_array($_POST['status'], ['A', 'I', 'R']) ? $_POST['status'] : 'I';

    // find project_id from member_project table using member_project_id
    $stmtProject = $pdo->prepare("SELECT project_id FROM member_project WHERE id = ?");
    $stmtProject->execute([$member_project_id]);
    $project_id = $stmtProject->fetchColumn();

    // echo "Received POST data: member_id=$member_id, member_code=$member_code, pay_id=$pay_id, status=$status";
    // echo "member_project_id=$member_project_id" ;
    // echo "project_id=$project_id";
    // die();

    // get the no_share value for the specific pay_id from the $payments array
    $payments = array_filter($payments, function($payment) use ($pay_id) {
        return $payment['id'] === $pay_id;
    });
    $addShareCount = reset($payments)['no_share'] ?? 0;

    // echo "Updating payment ID: $pay_id for member ID: $member_id with status: $status and share count: $addShareCount";
    // die();

    // Get payment details for the specific pay_id
    $stmt = $pdo->prepare("SELECT * FROM member_payments WHERE id = ?");
    $stmt->execute([$pay_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    $tran_type = $payment['tran_type'] ?? '';

    if (!$payment) {
        // Payment not found, set error message and stay on the same page
        $_SESSION['error_msg'] = "❌ পেমেন্ট তথ্য পাওয়া যায়নি!";
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    $for_install = round($payment['amount'] * 0.95, 2);
    $other_fee = round($payment['amount'] * 0.05, 2);
    $late_fee = round($payment['amount'], 2); // Assuming late fee is calculated elsewhere or is zero for this context

    if ($tran_type == 2) {
        $stmt = $pdo->prepare("UPDATE member_share SET extra_share = 0, for_install = for_install + ?, other_fee = other_fee + ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$for_install, $other_fee,  $member_id, $member_code]);
    } elseif ($tran_type == 6) {
        $stmt = $pdo->prepare("UPDATE member_share SET late_fee = late_fee + ? WHERE member_id = ? AND member_code = ?");
        $stmt->execute([$late_fee,  $member_id, $member_code]);
    } elseif ($tran_type == 3 || $tran_type == 5) {

        // project_share table insert or update logic
        $stmtProjectShare = $pdo->prepare("SELECT COUNT(*) as share_count FROM project_share WHERE member_id = ? AND member_code = ? AND project_id = ?");
        $stmtProjectShare->execute([$member_id, $member_code, $project_id]);

        // FETCH the result row
        $result = $stmtProjectShare->fetch(PDO::FETCH_ASSOC);
        $shareCount = $result['share_count'] ? (int)$result['share_count'] : 0;

        if ($tran_type == 3) {
            $type = 'uddokta';
        } else {
            $type = 'share';
        }

        // FIX 1: rowCount() এর বদলে প্রকৃত shareCount দিয়ে কন্ডিশন চেক করা হলো
        if ($shareCount > 0) {

            $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'A', NOW())");

            for ($i = 0; $i < $addShareCount; $i++) {
                $current_serial = $shareCount + $i + 1; // যদি shareCount ২০ হয়, তবে এটি ২১, ২২, ২৩... এভাবে বাড়বে
                $n = str_pad($current_serial, 3, '0', STR_PAD_LEFT);
                $share_id = "{$type}{$member_id}{$member_project_id}{$project_id}{$n}";
                $stmtInsert->execute([$member_project_id, $member_id, $member_code, $project_id, $share_id]);
            }
        } else {

            // Insert into project_share for the new member_project
            $stmtInsertShare = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'A', NOW())");
            
            for ($i = 0; $i < $addShareCount; $i++) {
                $n = str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                $share_id = "{$type}{$member_id}{$member_project_id}{$project_id}{$n}";
                $stmtInsertShare->execute([$member_project_id, $member_id, $member_code, $project_id, $share_id]);
            }
        }

                // update member_project with project_share, paid_amount, sundry_amount, status
        if ($tran_type == 3){
            $stmt = $pdo->prepare("UPDATE member_share SET no_share = no_share + ?, uddokta_share = uddokta_share + ?, uddokta_share_amt = uddokta_share_amt + ?, extra_share = extra_share - ? WHERE member_id = ? AND member_code = ?");
            $stmt->execute([$addShareCount, $addShareCount, $payment['amount'], $addShareCount, $member_id, $member_code]);
        } else if ($tran_type == 5){
            $stmt = $pdo->prepare("UPDATE member_share SET no_share = no_share + ?, extra_share = extra_share - ? WHERE member_id = ? AND member_code = ?");  
            $stmt->execute([$addShareCount, $addShareCount, $member_id, $member_code]);
        }
    } 

    $stmtUpdateProject = $pdo->prepare("UPDATE member_project SET  paid_amount = paid_amount + ?, sundry_amount = sundry_amount - ?, status = ? WHERE member_id = ? AND member_code = ? AND id = ? AND project_id = ?");
    $stmtUpdateProject->execute([$payment['amount'], $payment['amount'], $status, $member_id, $member_code, $member_project_id, $project_id]);
    
        // Update payment status
    $stmt = $pdo->prepare("UPDATE member_payments SET status = ? WHERE id = ? AND member_id = ? AND member_code = ?");
    $stmt->execute([$status, $pay_id, $member_id, $member_code]);

    // get gl_maaping for glac_id and contra_glac_id  where is_active = 1 from tran_type = $tran_type
    $stmtGl = $pdo->prepare("SELECT credit_glac_id, debit_glac_id FROM gl_mapping WHERE tran_type = ? AND is_active = 1");
    $stmtGl->execute([$payment['tran_type']]);
    $gl_mapping = $stmtGl->fetch(PDO::FETCH_ASSOC);

    if ($gl_mapping) {
        $credit_glac_id = $gl_mapping['credit_glac_id'];
        $debit_glac_id = $gl_mapping['debit_glac_id'];
        $cr_code = 'C';
        $dr_code = 'D';

        // Insert into gl_transaction and gl_summary based on status of member_payment
        if ($status === 'A') {

        // Insert credit transaction
            $stmtGlTrans = $pdo->prepare("INSERT INTO gl_transaction (member_id, glac_id, tran_date, tran_amount, drcr_code, remarks, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtGlTrans->execute([$member_id, $credit_glac_id, date('Y-m-d'), $payment['amount'], $cr_code, $member_code. ' - ' .$payment['remarks'], $user_id]);
            
            // Insert debit transaction
            $stmtGlTrans = $pdo->prepare("INSERT INTO gl_transaction (member_id, glac_id, tran_date, tran_amount, drcr_code, remarks, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtGlTrans->execute([$member_id, $debit_glac_id, date('Y-m-d'), $payment['amount'], $dr_code, $member_code. ' - ' .$payment['remarks'], $user_id]);

            // gl_summary te glac_id te credit_glac_id and debit_glac_id jodi na thak tahole insert korte hobe, thakle update korte hobe
            
            // Check and update/insert for credit_glac_id
            $stmtGlSumCheck = $pdo->prepare("SELECT glac_id FROM gl_summary WHERE glac_id = ?");
            $stmtGlSumCheck->execute([$credit_glac_id]);
            if ($stmtGlSumCheck->fetch()) {
                // Update existing credit summary
                $stmtGlSumUpdate = $pdo->prepare("UPDATE gl_summary SET tran_date = ?, credit_amount = credit_amount + ?, created_by = ? WHERE glac_id = ?");
                $stmtGlSumUpdate->execute([date('Y-m-d'), $payment['amount'], $user_id, $credit_glac_id]);
            } else {
                // Insert new credit summary
                $stmtGlSumInsert = $pdo->prepare("INSERT INTO gl_summary (tran_date, glac_id, credit_amount, debit_amount, created_by) VALUES (?, ?, ?, ?, ?)");
                $stmtGlSumInsert->execute([date('Y-m-d'), $credit_glac_id, $payment['amount'], 0, $user_id]);
            }
            
            // Check and update/insert for debit_glac_id
            $stmtGlSumCheck = $pdo->prepare("SELECT glac_id FROM gl_summary WHERE glac_id = ?");
            $stmtGlSumCheck->execute([$debit_glac_id]);
            if ($stmtGlSumCheck->fetch()) {
                // Update existing debit summary
                $stmtGlSumUpdate = $pdo->prepare("UPDATE gl_summary SET tran_date = ?, debit_amount = debit_amount + ?, created_by = ? WHERE glac_id = ?");
                $stmtGlSumUpdate->execute([date('Y-m-d'), $payment['amount'], $user_id, $debit_glac_id]);
            } else {
                // Insert new debit summary
                $stmtGlSumInsert = $pdo->prepare("INSERT INTO gl_summary (tran_date, glac_id, credit_amount, debit_amount, created_by) VALUES (?, ?, ?, ?, ?)");
                $stmtGlSumInsert->execute([date('Y-m-d'), $debit_glac_id, 0, $payment['amount'], $user_id]);
            }
        } 
    }

    // Set dynamic success message based on status
    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ সমিতিতে আপনার পেমেন্ট এর সকল তথ্য সঠিক, অনুমোদন দেয়া হইলো !";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ সমিতিতে আপনার পেমেন্টটি  অপেক্ষমান অবস্থায় আছে, নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ সমিতিতে আপনার পেমেন্ট এর কোনো তথ্য পাওয়া যায়নি,বাতিল করা হইলো !";
    }

    // Send SMS if the mobile number exists
    if ($payment['mobile']) {
        $sms_response = sms_send($payment['mobile'], $_SESSION['success_msg']);
        if ($sms_response === false) {
            $sms_error_msg = '❌ SMS পাঠানো যায়নি।';
        } else {
            $sms_result = json_decode($sms_response, true);
            if (isset($sms_result['error']) && $sms_result['error'] != 0) {
                $sms_error_msg = '❌ SMS পাঠানো যায়নি: ' . ($sms_result['message'] ?? 'Unknown error');
            } else {
                $sms_success_msg = '✅ SMS সফলভাবে পাঠানো হয়েছে।';
                $_SESSION['success_msg'] .= ' ' . $sms_success_msg;
            }
        }
    }

    // If there was an SMS error, append it to success message
    if (isset($sms_error_msg)) {
        $_SESSION['success_msg'] .= ' ' . $sms_error_msg;
    }

    // Stay on the same page after submission
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
                        <div class="row">
                            <div class="col-md-8"><h3 class="mb-3 text-primary fw-bold">Payment Approval <span class="text-secondary">( পেমেন্ট অনুমোদন )</span></h3></div>
                            <div class="col-md-4 text-end"><a href="../account/payment.php"><button type="button" class="btn btn-sm btn-success">
                                Payment ( পেমেন্ট )      </button></a></div>
                        </div>
                        <hr class="mb-4" /> 

                        <div class="mb-3">
                            <input type="search" id="tableSearch" class="form-control form-control-sm" placeholder="Search table... (যেকোনো তথ্য খুঁজুন)" aria-label="Search table">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Member Info</th>
                                        <th>Payment Method</th>
                                        <th>Bank Pay Info</th>
                                        <th>Trans No</th>
                                        <th>Payment Slip</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($payment['member_code']) ?><br/>
                                            <?= htmlspecialchars($payment['name_bn']) ?><br/>
                                            <?= htmlspecialchars($payment['name_en']) ?><br/>
                                            <?= htmlspecialchars($payment['mobile']) ?><br/>
                                            <small class="text-muted">শেয়ার: <?= (int)$payment['no_share'] ?></small>
                                        </td>
                                        <td><?= htmlspecialchars(ucfirst($payment['payment_method'])) ?> 
                                        - <?= htmlspecialchars($payment['for_fees']) ?> 
                                        <br/> <?= htmlspecialchars($payment['project_title']) ?> ,  
                                        <?= htmlspecialchars($payment['payment_year']) ?>, 
                                        <?= htmlspecialchars($payment['tran_type']) ?>

                                        </td>
                                        <td><?= htmlspecialchars($payment['bank_pay_date']) ?><br/>
                                            <?= htmlspecialchars($payment['bank_trans_no']) ?></td>
                                        <td><?= htmlspecialchars($payment['trans_no']) ?></td>
                                        <td>
                                            <?php if (!empty($payment['payment_slip'])):
                                                $slip = $payment['payment_slip'];
                                                $slip_url = '../payment/' . rawurlencode($slip);
                                                $ext = strtolower(pathinfo($slip, PATHINFO_EXTENSION));
                                                $image_exts = ['jpg','jpeg','png','gif','webp','bmp','svg'];
                                            ?>
                                                <?php if (in_array($ext, $image_exts)): ?>
                                                    <a href="<?= htmlspecialchars($slip_url) ?>" target="_blank" rel="noopener noreferrer">
                                                        <img src="<?= htmlspecialchars($slip_url) ?>" alt="Payment Slip" style="max-width:120px; max-height:90px; object-fit:contain; border:1px solid #ddd; padding:2px;" />
                                                    </a>
                                                <?php elseif ($ext === 'pdf'): ?>
                                                    <a href="<?= htmlspecialchars($slip_url) ?>" target="_blank" rel="noopener noreferrer" style="text-decoration:none; display:inline-block;">
                                                        <div style="width:120px;height:90px;border:1px solid #ddd;display:flex;align-items:center;justify-content:center;background:#f8f9fa;color:#333;font-weight:600;">📄 PDF</div>
                                                        <div style="font-size:12px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($slip) ?></div>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= htmlspecialchars($slip_url) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($slip) ?></a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= htmlspecialchars($payment['remarks'] ?? 'N/A') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($payment['amount']) ?></td>
                                        <td>
                                            <form method="post" class="d-flex flex-column align-items-start gap-2">
                                                <input type="hidden" name="member_id" value="<?= $payment['member_id'] ?>">
                                                <input type="hidden" name="member_code" value="<?= $payment['member_code'] ?>">
                                                <input type="hidden" name="member_project_id" value="<?= $payment['member_project_id'] ?>">
                                                <input type="hidden" name="pay_id" value="<?= $payment['id'] ?>">
                                                <select name="status" class="form-select form-select-sm me-2" style="min-width:120px;">
                                                    <option value="A" <?= $payment['status'] === 'A' ? 'selected' : '' ?>>✅ Approved</option>
                                                    <option value="I" <?= $payment['status'] === 'I' ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                    <option value="R" <?= $payment['status'] === 'R' ? 'selected' : '' ?>>❌ Rejected</option>
                                                </select>
                                                <button type="submit" class="btn btn-success btn-sm mt-2">Update (হালনাগাদ)</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                            <script>
                            document.addEventListener('DOMContentLoaded', function(){
                                const input = document.getElementById('tableSearch');
                                const table = document.querySelector('.table.table-bordered');
                                const tbody = table ? table.querySelector('tbody') : null;
                                if (!input || !tbody) return;

                                input.addEventListener('input', function(){
                                    const q = input.value.trim().toLowerCase();
                                    const rows = tbody.querySelectorAll('tr');
                                    if (q === ''){
                                        rows.forEach(r => r.style.display = '');
                                        return;
                                    }
                                    rows.forEach(r => {
                                        const text = r.textContent.toLowerCase();
                                        r.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                                    });
                                });
                            });
                            </script>
                    </div>
                </div>
            </div>
        </main>
  </div> 
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>


