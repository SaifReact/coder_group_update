<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$member_code = $_SESSION['member_code'];

// Get account creation date from user_login and compute account age in days
$stmtUser = $pdo->prepare("SELECT created_at FROM user_login WHERE id = ? LIMIT 1");
$stmtUser->execute([$_SESSION['user_id']]);
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
$account_age_days = '';
if (!empty($userRow['created_at'])) {
    try {
        $createdDT = new DateTime($userRow['created_at']);
        $nowDT = new DateTime();
        $diff = $nowDT->diff($createdDT);
        $account_age_days = (int)$diff->days;
    } catch (Exception $e) {
        $account_age_days = '';
    }
}

// Fetch existing account close requests for this member
$stmt = $pdo->prepare("SELECT * FROM account_close WHERE member_id = ? ORDER BY id DESC");
$stmt->execute([$member_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch member_share values
$stmtMs = $pdo->prepare("SELECT admission_fee, samity_share_amt FROM member_share WHERE member_id = ? AND member_code = ? LIMIT 1");
$stmtMs->execute([$member_id, $member_code]);
$ms = $stmtMs->fetch(PDO::FETCH_ASSOC);
$admission_fee = $ms['admission_fee'] ?? 0;
$samity_share_amt = $ms['samity_share_amt'] ?? 0;

// Sum paid_amount from member_project where project_id > 1
$stmtMp = $pdo->prepare("SELECT COALESCE(SUM(paid_amount),0) AS paid_amount_sum FROM member_project WHERE member_id = ? AND member_code = ? AND project_id > 1 AND status = 'A'");
$stmtMp->execute([$member_id, $member_code]);
$mp = $stmtMp->fetch(PDO::FETCH_ASSOC);
$paid_amount_sum = $mp['paid_amount_sum'] ?? 0;
$total_amt = $samity_share_amt + $paid_amount_sum;
// Determine deduction and refund based on account age
if ($account_age_days !== '' && $account_age_days < 730) {
    $deduction = ($total_amt * 10 / 100); // 10% deduction
    $refund_amt = max(0, $total_amt - $deduction);
} else {
    // If account age is >= 730 days or unknown, full deduction (no refund)
    $deduction = $total_amt;
    $refund_amt = $total_amt;
}
?>

<?php include_once __DIR__ . '/../includes/open.php'; ?>
<?php include_once __DIR__ . '/../includes/side_bar.php'; ?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-12 col-md-12">
                        <h3 class="mb-3 text-primary fw-bold">Account Close <span class="text-secondary">( হিসাব বন্ধ ) - বয়সীমা: <?php echo ($account_age_days !== '' ? htmlspecialchars($account_age_days) : 'N/A'); ?> দিন</span></h3>
                    </div>
                </div>

                <hr class="mb-4" />

                <form method="post" action="../process/account_close.php">
                    <input type="hidden" name="action" value="create">
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Admission Fee (অফেরৎযোগ্য)</label>
                            <input type="text" readonly class="form-control" value="<?php echo htmlspecialchars(number_format((float)$admission_fee,2)); ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Samity Share Fee (সমিতি শেয়ার ফি)</label>
                            <input type="text" readonly class="form-control" value="<?php echo htmlspecialchars(number_format((float)$samity_share_amt,2)); ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Project Share Fee (প্রজেক্ট শেয়ার ফি)</label>
                            <input type="text" readonly class="form-control" value="<?php echo htmlspecialchars(number_format((float)$paid_amount_sum,2)); ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Deposited Fee (জমাকৃত ফি)</label>
                            <input type="text" readonly class="form-control" value="<?php echo htmlspecialchars(number_format((float)$total_amt,2)); ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Deduction (কর্তন - 10%)</label>
                            <input type="text" readonly class="form-control" value="<?php echo htmlspecialchars(number_format((float)$deduction,2)); ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Refund Fee (ফেরতযোগ্য ফি)</label>
                            <input type="text" readonly class="form-control" value="<?php echo htmlspecialchars(number_format((float)$refund_amt,2)); ?>">
                        </div>
                        <input type="hidden" name="none_refund" value="<?php echo htmlspecialchars($admission_fee); ?>">
                        <input type="hidden" name="total_amt" value="<?php echo htmlspecialchars($total_amt); ?>">
                        <input type="hidden" name="deduction" value="<?php echo htmlspecialchars($deduction); ?>">
                        <input type="hidden" name="refund_amt" value="<?php echo htmlspecialchars($refund_amt); ?>">
                        <input type="hidden" name="agreed" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for account close</label>
                        <textarea name="reasons" class="form-control" rows="4" required placeholder="Enter reason..."></textarea>
                    </div>
                    <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="agreed" value="1" id="agreeRules">
                    <label class="form-check-label" for="agreeRules" style="color:#001d00"> যেহেতু হিসাবের বয়সীমা <?php echo ($account_age_days !== '' ? htmlspecialchars($account_age_days) : 'N/A'); ?> দিন - তাই আমি উপরোল্লিখিত সকল হিসাব নিকাশ বুঝিয়া এবং কারণ দর্শানো অনুযায়ী সমিতির সদস্যপদ ও হিসাবটি বন্ধ করার জন্য উদ্যোগ গ্রহণ করলাম। </label>
                    </div>
                    <div class="text-center mt-4">
                    <button id="closeAccountBtn" class="btn btn-success btn-lg rounded-pill px-5" style="display:none;letter-spacing:1px;" type="submit" >সদস্যপদ ও হিসাবটি বন্ধ করুন ( Close Membership & Account )</button>
                    </div>
                </form>

                <hr class="my-4" />
                
                <?php if (empty($requests)): ?>
                    <div class="alert alert-info">No account close requests yet.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Reason</th>
                                    <th>Total Amount</th>
                                    <th>None Refund</th>
                                    <th>Deduction</th>
                                    <th>Refund Amt</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($r['reasons'])); ?></td>
                                    <td><?php echo htmlspecialchars($r['total_amt']); ?></td>
                                    <td><?php echo htmlspecialchars($r['none_refund']); ?></td>
                                    <td><?php echo htmlspecialchars($r['deduction']); ?></td>
                                    <td><?php echo htmlspecialchars($r['refund_amt']); ?></td>
                                    <td>
                                            <?php
                                                if ($r['status'] == 'I') {
                                                    echo 'অপেক্ষমান';
                                                } elseif ($r['status'] == 'A') {
                                                    echo 'অনুমোদিত';
                                                } elseif ($r['status'] == 'R') {
                                                    echo 'বাতিল';
                                                } else {
                                                    echo htmlspecialchars($r['status']);
                                                }
                                            ?>
                                        </td>
                                    <td>
                                        <form method="post" action="../process/account_close.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                            <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This Service?');">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>
                            </div>
                        </div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var chk = document.getElementById('agreeRules');
    var btn = document.getElementById('closeAccountBtn');
    if (!chk || !btn) return;
    function toggle() {
        btn.style.display = chk.checked ? 'inline-block' : 'none';
    }
    chk.addEventListener('change', toggle);
    toggle();
});
</script>
