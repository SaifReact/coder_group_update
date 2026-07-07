<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    http_response_code(403);
    echo '<div class="alert alert-danger">Unauthorized.</div>';
    exit;
}
include_once __DIR__ . '/../config/config.php';

$member_id = (int)($_GET['member_id'] ?? 0);
$month     = trim($_GET['month'] ?? '');
if (!$member_id || !$month) {
    echo '<div class="alert alert-warning">Invalid request.</div>';
    exit;
}

// Member info
$stmt = $pdo->prepare(
    "SELECT m.member_code, m.name_bn, m.name_en, m.mobile, m.email,
            mo.present_address AS address
     FROM   members_info m
     LEFT JOIN member_office mo ON m.id = mo.member_id
     WHERE  m.id = ?"
);
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

// Bazar entries with stored seller_price
$stmt = $pdo->prepare(
    "SELECT id, product_name, quantity, company, remarks, status,
            COALESCE(seller_price, 0) AS seller_price
     FROM   monthly_bazar
     WHERE  member_id = ? AND month = ?
     ORDER  BY id ASC"
);
$stmt->execute([$member_id, $month]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$no_product  = count($entries);
$total_price = (float)array_sum(array_column($entries, 'seller_price'));

// Check existing bazar_transaction
$existingTx = null;
try {
    $txStmt = $pdo->prepare(
        "SELECT * FROM bazar_transaction WHERE member_id = ? AND month = ? LIMIT 1"
    );
    $txStmt->execute([$member_id, $month]);
    $existingTx = $txStmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (Exception $e) { $existingTx = null; }

$statusLabel = [
    'I' => ['label' => '⏳ Pending',    'class' => 'bg-warning text-dark'],
    'P' => ['label' => '⏳ Processing', 'class' => 'bg-info text-dark'],
    'A' => ['label' => '✅ Approved',   'class' => 'bg-success'],
    'R' => ['label' => '❌ Rejected',   'class' => 'bg-danger'],
];
?>

<!-- Toolbar -->
<div class="d-flex justify-content-end px-2 mb-2">
    <button type="button" class="btn btn-success btn-sm" onclick="downloadBazarCSV(this)">
        <i class="bi bi-file-earmark-arrow-down me-1"></i> CSV Download
    </button>
</div>

<!-- Member Summary -->
<div class="row g-3 mb-3 px-2">
    <div class="col-md-6">
        <table class="table table-sm table-bordered mb-0">
            <tr>
                <th class="bg-light" width="40%">সদস্য কোড</th>
                <td><strong><?= htmlspecialchars($member['member_code'] ?? '') ?></strong></td>
            </tr>
            <tr>
                <th class="bg-light">নাম (বাংলা)</th>
                <td><?= htmlspecialchars($member['name_bn'] ?? '') ?></td>
            </tr>
            <tr>
                <th class="bg-light">Name (English)</th>
                <td><?= htmlspecialchars($member['name_en'] ?? '') ?></td>
            </tr>
            <tr>
                <th class="bg-light">মোবাইল</th>
                <td><?= htmlspecialchars($member['mobile'] ?? '') ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-sm table-bordered mb-0">
            <tr>
                <th class="bg-light">ইমেইল</th>
                <td><?= htmlspecialchars($member['email'] ?? '') ?></td>
            </tr>
            <tr>
                <th class="bg-light">মাস</th>
                <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($month) ?></span></td>
            </tr>
            <tr>
                <th class="bg-light">মোট পণ্য</th>
                <td><span class="badge bg-secondary fs-6"><?= $no_product ?> টি</span></td>
            </tr>
            <tr>
                <th class="bg-light">ঠিকানা</th>
                <td><?= htmlspecialchars($member['address'] ?? '') ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Bazar Entries Table — read-only -->
<div class="table-responsive px-2 pb-1">
    <table class="table table-bordered table-hover table-sm align-middle" id="bazarDetailTable">
        <thead class="table-dark">
            <tr>
                <th width="4%">#</th>
                <th>পণ্যের নাম</th>
                <th width="10%">পরিমাণ</th>
                <th width="13%">কোম্পানি</th>
                <th width="12%">মন্তব্য</th>
                <th width="13%" class="text-end text-nowrap">বিক্রয় মূল্য <small class="fw-normal">(৳)</small></th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($entries)): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">No entries found.</td></tr>
            <?php else: ?>
                <?php foreach ($entries as $i => $entry):
                    $s = $statusLabel[$entry['status']] ?? $statusLabel['I'];
                ?>
                    <tr data-id="<?= $entry['id'] ?>">
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($entry['product_name']) ?></strong></td>
                        <td><?= htmlspecialchars($entry['quantity']) ?></td>
                        <td><?= htmlspecialchars($entry['company']) ?></td>
                        <td><?= htmlspecialchars($entry['remarks']) ?></td>
                        <td class="text-end fw-semibold">
                            <?php if ($entry['seller_price'] > 0): ?>
                                <span class="text-success">৳<?= number_format((float)$entry['seller_price'], 2) ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- Total row -->
                <tr class="table-secondary fw-bold">
                    <td colspan="5" class="text-end pe-3">
                        মোট <span class="fw-normal small">(Total)</span> :
                    </td>
                    <td class="text-end text-primary">৳<?= number_format($total_price, 2) ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ── Transaction / Approval Section ─────────────────────────────── -->
<?php if ($existingTx): ?>

<div class="card border-success mx-2 mb-3">
    <div class="card-header bg-success text-white fw-bold py-2">
        ✅ অনুমোদিত লেনদেন — <?= htmlspecialchars($month) ?>
    </div>
    <div class="card-body pb-2">
        <div class="row g-2">
            <div class="col-6 col-md-4">
                <label class="form-label fw-semibold small mb-1">মূল্য (Price) ৳</label>
                <input type="text" class="form-control form-control-sm bg-light"
                       value="৳<?= number_format($existingTx['price'], 2) ?>" readonly>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label fw-semibold small mb-1">ছাড় (Discount) ৳</label>
                <input type="text" class="form-control form-control-sm bg-light"
                       value="৳<?= number_format($existingTx['discount'], 2) ?>" readonly>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label fw-semibold small mb-1">সার্ভিস চার্জ ৳</label>
                <input type="text" class="form-control form-control-sm bg-light"
                       value="৳<?= number_format($existingTx['service_charge'], 2) ?>" readonly>
            </div>
            <div class="col-6 col-md-6">
                <label class="form-label fw-semibold small mb-1">মোট মূল্য (Sum Price) ৳</label>
                <input type="text" class="form-control form-control-sm fw-bold"
                       value="৳<?= number_format($existingTx['sum_price'], 2) ?>" readonly
                       style="background:#eef4ff; color:#0d6efd;">
            </div>
            <div class="col-6 col-md-6">
                <label class="form-label fw-semibold small mb-1">বাকি (Due Price) ৳</label>
                <input type="text" class="form-control form-control-sm fw-bold"
                       value="৳<?= number_format($existingTx['due_price'], 2) ?>" readonly
                       style="background:#fff3cd; color:#dc3545;">
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="card border-primary mx-2 mb-3">
    <div class="card-header bg-primary text-white fw-bold py-2">
        💰 বাজার হিসাব ও অনুমোদন
    </div>
    <div class="card-body pb-2">
        <div id="txAlert" class="d-none mb-2 small"></div>
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-4">
                <label class="form-label fw-semibold small mb-1">মূল্য (Price) ৳</label>
                <input type="text" class="form-control form-control-sm fw-semibold text-primary bg-light"
                       id="tx_price" value="<?= number_format($total_price, 2, '.', '') ?>" readonly>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label fw-semibold small mb-1">ছাড় (Discount) ৳</label>
                <input type="text" inputmode="decimal" class="form-control form-control-sm"
                       id="tx_discount" value="0" placeholder="০.০০" oninput="calcTxSummary()">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1">সার্ভিস চার্জ (Service Charge) ৳</label>
                <input type="text" inputmode="decimal" class="form-control form-control-sm"
                       id="tx_service_charge" value="0" placeholder="০.০০" oninput="calcTxSummary()">
            </div>
            <div class="col-6 col-md-5">
                <label class="form-label fw-semibold small mb-1">মোট মূল্য (Sum Price) ৳</label>
                <input type="text" class="form-control form-control-sm fw-bold"
                       id="tx_sum_price" value="<?= number_format($total_price, 2, '.', '') ?>" readonly
                       style="background:#eef4ff; color:#0d6efd;">
            </div>
            <div class="col-6 col-md-5">
                <label class="form-label fw-semibold small mb-1">বাকি (Due Price) ৳</label>
                <input type="text" class="form-control form-control-sm fw-bold"
                       id="tx_due_price" value="<?= number_format($total_price, 2, '.', '') ?>" readonly
                       style="background:#fff3cd; color:#dc3545;">
            </div>
            <div class="col-12 col-md-2 text-end">
                <button type="button" class="btn btn-success btn-sm w-100"
                        onclick="approveBazar(this,
                            <?= $member_id ?>,
                            '<?= htmlspecialchars($month, ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($member['member_code'] ?? '', ENT_QUOTES) ?>',
                            <?= $no_product ?>,
                            <?= $total_price ?>)">
                    <i class="bi bi-check-circle me-1"></i> Approve
                </button>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
