<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fetch account close requests
$stmt = $pdo->query("SELECT a.*, m.member_code, m.name_en, m.name_bn
    FROM account_close a
    LEFT JOIN members_info m ON m.id = a.member_id
    ORDER BY a.id DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($method === 'POST' && isset($_POST['status'])) {
    $status = in_array($_POST['status'], ['A','I','R']) ? $_POST['status'] : 'I';
    $request_id = (int)($_POST['user_id'] ?? 0);
    $waiver = isset($_POST['waiver']) ? (float)$_POST['waiver'] : 0;

    // fetch request row
    $stmtR = $pdo->prepare("SELECT * FROM account_close WHERE id = ? LIMIT 1");
    $stmtR->execute([$request_id]);
    $row = $stmtR->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['error_msg'] = 'Record not found.';
        header('Location: ' . $_SERVER['REQUEST_URI']); exit;
    }

    $member_id = (int)$row['member_id'];
    $member_code = $row['member_code'];

    // If approving, update related tables
    if ($status === 'A') {

    // === Custom Logic for GL summary on Account Close ===
            // Set your GL IDs here (update as needed)
            $cash_at_bank_gl = 11; // <-- set correct glac_id for cash_at_bank
            $samity_share_issued_gl = 32; // <-- set correct glac_id for samity_share_issued
            $project_share_issued_gl = 34; // <-- set correct glac_id for project_share_issued
            $member_deposit_gl = 29; // <-- set correct glac_id for member deposit
            $member_deposit_deduction_gl = 93; // <-- set correct glac_id for deduction
            $member_closing_waiver_gl = 94; // <-- set correct glac_id for waiver

            $total_deposited = isset($row['total_deposited']) ? (float)$row['total_deposited'] : 0;
            $total_amt = isset($row['total_amt_nr']) ? (float)$row['total_amt_nr'] : 0;
            $none_refund = isset($row['none_refund']) ? (float)$row['none_refund'] : 0;
            $deduction = isset($row['deduction_amt']) ? (float)$row['deduction_amt'] : 0;
            $refund_amt = isset($row['refund_amt']) ? (float)$row['refund_amt'] : 0;

            $user_id = $_SESSION['user_id'] ?? 1;
            $tran_date = date('Y-m-d');

            $samity_share_amount = 0.0;
            $project_share_amount = 0.0;
            $monthly_deposit = 0.0;

            $stmtShare = $pdo->prepare("SELECT COALESCE(samity_share_amt,0) AS samity_share_amt FROM member_share WHERE member_id = ? LIMIT 1");
            $stmtShare->execute([$member_id]);
            $shareRow = $stmtShare->fetch(PDO::FETCH_ASSOC);
            $samity_share_amount = isset($shareRow['samity_share_amt']) ? (float)$shareRow['samity_share_amt'] : 0;

            $stmtProj = $pdo->prepare("SELECT COALESCE(SUM(paid_amount),0) AS paid_amount_sum FROM member_project WHERE member_id = ? AND status = 'A' AND project_id > 1");
            $stmtProj->execute([$member_id]);
            $projectRow = $stmtProj->fetch(PDO::FETCH_ASSOC);
            $project_share_amount = isset($projectRow['paid_amount_sum']) ? (float)$projectRow['paid_amount_sum'] : 0;

            $monthly_deposit = max(0, $total_amt - $samity_share_amount - $project_share_amount);

        // Find member_project rows for this member and update each row's status to 'C'
        $stmtFindMP = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? ");
        $stmtFindMP->execute([$member_id]);
        $mpRows = $stmtFindMP->fetchAll(PDO::FETCH_ASSOC);
        if ($mpRows) {
            $stmtUpdateSingle = $pdo->prepare("UPDATE member_project SET status = 'C' WHERE id = ?");
            foreach ($mpRows as $mpRow) {
                $stmtUpdateSingle->execute([$mpRow['id']]);
            }
        }

        // Find member_payments rows for this member and update each row's status to 'C'
        $stmtFindPay = $pdo->prepare("SELECT id FROM member_payments WHERE member_id = ?");
        $stmtFindPay->execute([$member_id]);
        $payRows = $stmtFindPay->fetchAll(PDO::FETCH_ASSOC);
        if ($payRows) {
            $stmtUpdatePaySingle = $pdo->prepare("UPDATE member_payments SET status = 'C' WHERE id = ?");
            foreach ($payRows as $payRow) {
                $stmtUpdatePaySingle->execute([$payRow['id']]);
            }
        }

            $cash_credit_total = 0;

            // echo "Calculations for member_id $member_id (Member Code: $member_code):\n";
            // echo "Total Deposited: $total_deposited\n";
            // echo "Total Amount (Non-Refundable): $total_amt\n";
            // echo "None Refund: $none_refund\n";
            // echo "Deduction: $deduction\n";
            // echo "Refund Amount: $refund_amt\n";
            // echo "Samity Share Amount: $samity_share_amount\n";
            // echo "Project Share Amount: $project_share_amount\n";
            // echo "Unallocated Deposit: $monthly_deposit\n";

            // echo "GL summary updated. Total cash credit: $cash_at_bank_gl, $cash_credit_total\n";
            // echo "GL summary updated. Total waiver: $member_closing_waiver_gl, $waiver\n";
            // echo "GL summary updated. Total Deduction: $member_deposit_deduction_gl, $deduction\n";
            // echo "GL summary updated. Total member deposit: $member_deposit_gl, $monthly_deposit\n";
            // echo "GL summary updated. Total project share: $project_share_issued_gl, $project_share_amount\n";
            // echo "GL summary updated. Total samity share: $samity_share_issued_gl, $samity_share_amount\n";

            // die();

            $updateGlSummary = function($glac_id, $debit_amount, $credit_amount) use ($pdo, $tran_date, $user_id) {
                if ($debit_amount <= 0 && $credit_amount <= 0) {
                    return;
                }
                $stmtCheck = $pdo->prepare("SELECT glac_id FROM gl_summary WHERE glac_id = ? LIMIT 1");
                $stmtCheck->execute([$glac_id]);
                if ($stmtCheck->fetch()) {
                    if ($debit_amount > 0) {
                        $stmtUpd = $pdo->prepare("UPDATE gl_summary SET tran_date = ?, debit_amount = debit_amount + ?, created_by = ? WHERE glac_id = ?");
                        $stmtUpd->execute([$tran_date, $debit_amount, $user_id, $glac_id]);
                    }
                    if ($credit_amount > 0) {
                        $stmtUpd = $pdo->prepare("UPDATE gl_summary SET tran_date = ?, credit_amount = credit_amount + ?, created_by = ? WHERE glac_id = ?");
                        $stmtUpd->execute([$tran_date, $credit_amount, $user_id, $glac_id]);
                    }
                } else {
                    $stmtIns = $pdo->prepare("INSERT INTO gl_summary (tran_date, glac_id, debit_amount, credit_amount, created_by) VALUES (?, ?, ?, ?, ?)");
                    $stmtIns->execute([$tran_date, $glac_id, $debit_amount, $credit_amount, $user_id]);
                }
            };

            

            if ($samity_share_amount > 0) {
                $updateGlSummary($samity_share_issued_gl, $samity_share_amount, 0);
                $cash_credit_total += $samity_share_amount;
            }

            if ($project_share_amount > 0) {
                $updateGlSummary($project_share_issued_gl, $project_share_amount, 0);
                $cash_credit_total += $project_share_amount;
            }

            if ($monthly_deposit > 0) {
                $updateGlSummary($member_deposit_gl, $monthly_deposit, 0);
                $cash_credit_total += $monthly_deposit;
            }

            if ($deduction > 0) {
                $updateGlSummary($member_deposit_deduction_gl, $deduction, 0);
                $cash_credit_total += $deduction;
            }

            if ($waiver > 0) {
                $updateGlSummary($member_closing_waiver_gl, $waiver, 0);
                $cash_credit_total += $waiver;
            }

            if ($cash_credit_total > 0) {
                $updateGlSummary($cash_at_bank_gl, 0, $cash_credit_total);
            }   

            $stmtMS = $pdo->prepare("UPDATE member_share SET no_share = 0, samity_share = 0, samity_share_amt = 0, other_fee = 150, for_install = 0, project_id = 0 WHERE member_id = ?");
            $stmtMS->execute([$member_id]);

            // update account_close status and waiver
        $stmtUpd = $pdo->prepare("UPDATE account_close SET status = 'A', waiver = ? WHERE id = ?");
        $stmtUpd->execute([$waiver, $request_id]);

        // set user_login status = 'C'
        $stmtUL = $pdo->prepare("UPDATE user_login SET status = 'C' WHERE member_id = ?");
        $stmtUL->execute([$member_id]);

        $_SESSION['success_msg'] = '✅ Account close approved and related records updated.';
        // mark this id so the modal can be auto-shown after redirect
        $_SESSION['last_closed_id'] = $request_id;
    } else {
        // mark request status accordingly and update waiver
        $stmtUpd = $pdo->prepare("UPDATE account_close SET status = ?, waiver = ? WHERE id = ?");
        $stmtUpd->execute([$status, $waiver, $request_id]);

        if ($status === 'I') {
            $_SESSION['success_msg'] = '⚠️ Account close request marked inactive.';
        } else {
            $_SESSION['success_msg'] = '❌ Account close request rejected.';
        }
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>

<?php include_once __DIR__ . '/../includes/open.php'; ?>
<?php include_once __DIR__ . '/../includes/side_bar.php'; ?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Account Close Approval <span class="text-secondary">( হিসাব বন্ধ অনুমোদন )</span></h3>
                <hr class="mb-4" />
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Member Info</th>
                                <th width="10%">Reason</th>
                                <th width="5%">Deposited</th>
                                <th width="10%">None Refund</th>
                                <th width="5%">Total Amt</th>
                                <th width="5%">Deduction</th>
                                <th width="5%">Refund Amt</th>
                                <th width="5%">Agreed</th>
                                <th width="40%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['id']) ?></td>
                                <td><?= htmlspecialchars($r['member_code']) ?>
                                <br/><?= htmlspecialchars($r['name_bn']) ?><br/><?= htmlspecialchars($r['name_en']) ?></td>
                                <td><?= nl2br(htmlspecialchars($r['reasons'])) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['total_deposited'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['none_refund'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['total_amt_nr'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['deduction_amt'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['refund_amt'],2)) ?></td>
                                <td><?= $r['agreed'] ? 'স্বজ্ঞানে সম্মতি' : 'ভুলক্রমে' ?></td>
                                <td>
                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                        <?php if ($r['status'] === 'A'): ?>
                                            <button type="button" class="btn btn-secondary btn-sm view-close-btn" data-id="<?= htmlspecialchars($r['id']) ?>" title="Print Letter">
                                                <i class="fa fa-file-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                        <form method="post" class="d-flex gap-2 align-items-center flex-wrap">
                                            <input type="hidden" name="user_id" value="<?= $r['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" style="width:auto; min-width:120px;">
                                                <option value="A" <?= ($r['status'] === 'A') ? 'selected' : '' ?>>✅ Approve</option>
                                                <option value="I" <?= ($r['status'] === 'I') ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                <option value="R" <?= ($r['status'] === 'R') ? 'selected' : '' ?>>❌ Reject</option>
                                            </select>
                                            <input type="number" name="waiver" class="form-control form-control-sm" style="width:auto; min-width:120px;" step="0.01" placeholder="Waiver Amount" value="<?= (isset($r['waiver']) && floatval($r['waiver']) !== 0.0) ? htmlspecialchars($r['waiver']) : '' ?>">
                                            <?php if ($r['status'] === 'I'): ?>
                                                <button type="submit" class="btn btn-primary btn-sm">Close (সদস্যপদ বাতিল)</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
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
<!-- Close Details Modal -->
<div class="modal fade" id="closeDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Membership Close Application ( সদস্যপদ বাতিলের আবেদন )</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="closeDetailsModalBody">
        <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="printCloseLetter">Print</button>
      </div>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-close-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var modalBody = document.getElementById('closeDetailsModalBody');
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
            var modalEl = document.getElementById('closeDetailsModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
            fetch('close_details.php?id=' + encodeURIComponent(id))
                .then(resp => resp.text())
                .then(html => { modalBody.innerHTML = html; })
                .catch(() => { modalBody.innerHTML = '<div class="alert alert-danger">Could not load details.</div>'; });
        });
    });

    // auto-open recently approved close details (if server marked one)
    var lastClosedId = <?php echo json_encode($_SESSION['last_closed_id'] ?? null); ?>;
    if (lastClosedId) {
        var modalBody = document.getElementById('closeDetailsModalBody');
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
        var modalEl = document.getElementById('closeDetailsModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
        fetch('close_details.php?id=' + encodeURIComponent(lastClosedId))
            .then(resp => resp.text())
            .then(html => { modalBody.innerHTML = html; })
            .catch(() => { modalBody.innerHTML = '<div class="alert alert-danger">Could not load details.</div>'; });
    }
    <?php if(isset($_SESSION['last_closed_id'])){ unset($_SESSION['last_closed_id']); } ?>

    // Print button
    var printBtn = document.getElementById('printCloseLetter');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            var modalBody = document.getElementById('closeDetailsModalBody');
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Close Letter</title>');
            printWindow.document.write('<link rel="stylesheet" href="../assets/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(modalBody.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(function(){ printWindow.print(); printWindow.close(); }, 500);
        });
    }
});
</script>
