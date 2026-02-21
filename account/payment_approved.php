<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$user_id = $_SESSION['user_id'];

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
        WHEN a.project_id = 1 THEN 'সমিতি শেয়ার ফি'
        ELSE p.project_name_bn
    END AS project_title
FROM member_payments a
LEFT JOIN (
    SELECT mp.*
    FROM member_project mp
    INNER JOIN (
        SELECT member_id, member_code, MAX(id) AS max_id
        FROM member_project
        GROUP BY member_id, member_code
    ) latest
        ON mp.member_id = latest.member_id
       AND mp.member_code = latest.member_code
       AND mp.id = latest.max_id
) b 
    ON a.member_id = b.member_id 
   AND a.member_code = b.member_code
INNER JOIN members_info c 
    ON a.member_id = c.id 
   AND a.member_code = c.member_code
LEFT JOIN project p 
    ON a.project_id = p.id
    WHERE a.status = 'A'
ORDER BY a.id DESC");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                            <div class="col-md-12"><h3 class="mb-3 text-primary fw-bold">Payment Approval <span class="text-secondary">( পেমেন্ট অনুমোদন )</span></h3></div>
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
                                            <?= htmlspecialchars($payment['mobile']) ?>
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
                                                <select name="status" class="form-select form-select-sm me-2" style="min-width:120px;">
                                                    <option value="A" <?= $payment['status'] === 'A' ? 'selected' : '' ?>>✅ Approved</option>
                                                </select>
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


