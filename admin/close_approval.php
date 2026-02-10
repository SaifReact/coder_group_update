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
        // update account_close status
        $stmtUpd = $pdo->prepare("UPDATE account_close SET status = 'A', updated_at = NOW(), updated_by = ? WHERE id = ?");
        $stmtUpd->execute([$_SESSION['user_id'], $request_id]);

        // set user_login status = 'C'
        $stmtUL = $pdo->prepare("UPDATE user_login SET status = 'C' WHERE member_id = ?");
        $stmtUL->execute([$member_id]);        

        // Find member_project rows for this member and update each row's status to 'C'
        $stmtFindMP = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? OR member_code = ?");
        $stmtFindMP->execute([$member_id, $member_code]);
        $mpRows = $stmtFindMP->fetchAll(PDO::FETCH_ASSOC);
        if ($mpRows) {
            $stmtUpdateSingle = $pdo->prepare("UPDATE member_project SET status = 'C', updated_at = NOW(), updated_by = ? WHERE id = ?");
            foreach ($mpRows as $mpRow) {
                $stmtUpdateSingle->execute([$_SESSION['user_id'], $mpRow['id']]);
            }
        }

        // Find member_payments rows for this member and update each row's status to 'C'
        $stmtFindPay = $pdo->prepare("SELECT id FROM member_payments WHERE member_id = ? OR member_code = ?");
        $stmtFindPay->execute([$member_id, $member_code]);
        $payRows = $stmtFindPay->fetchAll(PDO::FETCH_ASSOC);
        if ($payRows) {
            $stmtUpdatePaySingle = $pdo->prepare("UPDATE member_payments SET status = 'C', updated_at = NOW(), updated_by = ? WHERE id = ?");
            foreach ($payRows as $payRow) {
                $stmtUpdatePaySingle->execute([$_SESSION['user_id'], $payRow['id']]);
            }
        }

        $_SESSION['success_msg'] = '✅ Account close approved and related records updated.';
    } else {
        // mark request status accordingly
        $stmtUpd = $pdo->prepare("UPDATE account_close SET status = ?, updated_at = NOW(), updated_by = ? WHERE id = ?");
        $stmtUpd->execute([$status, $_SESSION['user_id'], $request_id]);

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
                                <th>#</th>
                                <th>Member Code</th>
                                <th>Member Name</th>
                                <th>Reason</th>
                                <th>Total Amt</th>
                                <th>Refund Amt</th>
                                <th>Agreed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['id']) ?></td>
                                <td><?= htmlspecialchars($r['member_code']) ?></td>
                                <td><?= htmlspecialchars($r['name_bn']) ?><br/><?= htmlspecialchars($r['name_en']) ?></td>
                                <td><?= nl2br(htmlspecialchars($r['reasons'])) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['total_amt'],2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)$r['refund_amt'],2)) ?></td>
                                <td><?= $r['agreed'] ? 'স্বজ্ঞানে সম্মতি' : 'ভুলক্রমে' ?></td>
                                <td>
                                    <form method="post" class="d-flex align-items-center">
                                        <input type="hidden" name="user_id" value="<?= $r['id'] ?>">
                                        <select name="status" class="form-select form-select-sm me-2">
                                            <option value="A" <?= ($r['status'] === 'A') ? 'selected' : '' ?>>✅ Approve</option>
                                            <option value="I" <?= ($r['status'] === 'I') ? 'selected' : '' ?>>⏸️ Inactive</option>
                                            <option value="R" <?= ($r['status'] === 'R') ? 'selected' : '' ?>>❌ Reject</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
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

<?php include_once __DIR__ . '/../includes/end.php'; ?>
