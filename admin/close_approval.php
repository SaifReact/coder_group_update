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
        // update account_close status and waiver
        $stmtUpd = $pdo->prepare("UPDATE account_close SET status = 'A', waiver = ? WHERE id = ?");
        $stmtUpd->execute([$waiver, $request_id]);

        // set user_login status = 'C'
        $stmtUL = $pdo->prepare("UPDATE user_login SET status = 'C' WHERE member_id = ?");
        $stmtUL->execute([$member_id]);

        // Find member_project rows for this member and update each row's status to 'C'
        $stmtFindMP = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? OR member_code = ?");
        $stmtFindMP->execute([$member_id, $member_code]);
        $mpRows = $stmtFindMP->fetchAll(PDO::FETCH_ASSOC);
        if ($mpRows) {
            $stmtUpdateSingle = $pdo->prepare("UPDATE member_project SET status = 'C' WHERE id = ?");
            foreach ($mpRows as $mpRow) {
                $stmtUpdateSingle->execute([$mpRow['id']]);
            }
        }

        // Find member_payments rows for this member and update each row's status to 'C'
        $stmtFindPay = $pdo->prepare("SELECT id FROM member_payments WHERE member_id = ? OR member_code = ?");
        $stmtFindPay->execute([$member_id, $member_code]);
        $payRows = $stmtFindPay->fetchAll(PDO::FETCH_ASSOC);
        if ($payRows) {
            $stmtUpdatePaySingle = $pdo->prepare("UPDATE member_payments SET status = 'C' WHERE id = ?");
            foreach ($payRows as $payRow) {
                $stmtUpdatePaySingle->execute([$payRow['id']]);
            }
        }

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
                                <th width="15%">Member Info</th>
                                <th width="15%">Reason</th>
                                <th width="5%">Deposited</th>
                                <th width="5%">None Refund</th>
                                <th width="5%">Total Amt</th>
                                <th width="5%">Deduction</th>
                                <th width="5%">Refund Amt</th>
                                <th width="5%">Agreed</th>
                                <th width="35%">Action</th>
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
                                <td><?= htmlspecialchars(number_format((float)$r['total_amt'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['deduction'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['refund_amt'],2)) ?></td>
                                <td><?= $r['agreed'] ? 'স্বজ্ঞানে সম্মতি' : 'ভুলক্রমে' ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($r['status'] === 'A'): ?>
                                            <button type="button" class="btn btn-secondary btn-sm me-2 view-close-btn" data-id="<?= htmlspecialchars($r['id']) ?>" title="Print Letter">
                                                <i class="fa fa-file-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                        <form method="post" class="d-flex align-items-center">
                                            <input type="hidden" name="user_id" value="<?= $r['id'] ?>">
                                            <select name="status" class="form-select form-select-sm me-2" style="width:120px;">
                                                <option value="A" <?= ($r['status'] === 'A') ? 'selected' : '' ?>>✅ Approve</option>
                                                <option value="I" <?= ($r['status'] === 'I') ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                <option value="R" <?= ($r['status'] === 'R') ? 'selected' : '' ?>>❌ Reject</option>
                                            </select>
                                            <div class="flex-grow-1 me-2">
                                                <input type="number" name="waiver" class="form-control form-control-sm" step="0.01" placeholder="Waiver Amount" value="<?= htmlspecialchars($r['waiver'] ?? 'Waiver Amount') ?>">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Update (হালনাগাদ)</button>
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
        <h5 class="modal-title">Account Close Letter</h5>
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
