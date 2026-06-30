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

// Bazar entries — include saved prices
$stmt = $pdo->prepare(
    "SELECT id, product_name, quantity, company, remarks, status,
            COALESCE(buyer_price, '') AS buyer_price,
            COALESCE(seller_price,'') AS seller_price,
            COALESCE(profit, '')      AS profit
     FROM   monthly_bazar
     WHERE  member_id = ? AND month = ?
     ORDER  BY id ASC"
);
$stmt->execute([$member_id, $month]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusLabel = [
    'I' => ['label' => '⏳ Pending',    'class' => 'bg-warning text-dark'],
    'P' => ['label' => '⏳ Processing', 'class' => 'bg-info text-dark'],
    'A' => ['label' => '✅ Approved',   'class' => 'bg-success'],
    'R' => ['label' => '❌ Rejected',   'class' => 'bg-danger'],
];
?>

<!-- Toolbar -->
<div class="d-flex justify-content-between align-items-center px-2 mb-2 gap-2">
    <div id="pricesSaveAlert" class="d-none flex-grow-1"></div>
    <div class="d-flex gap-2 ms-auto">
        <button type="button" class="btn btn-primary btn-sm" onclick="saveBazarPrices(this)">
            <i class="bi bi-save me-1"></i> দাম সংরক্ষণ করুন
        </button>
        <button type="button" class="btn btn-success btn-sm" onclick="downloadBazarCSV(this)">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> CSV Download
        </button>
    </div>
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
                <td><span class="badge bg-secondary fs-6"><?= count($entries) ?> টি</span></td>
            </tr>
            <tr>
                <th class="bg-light">ঠিকানা</th>
                <td><?= htmlspecialchars($member['address'] ?? '') ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Bazar Entries Table with Prices -->
<div class="table-responsive px-2 pb-2">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th width="4%">#</th>
                <th>পণ্যের নাম</th>
                <th width="9%">পরিমাণ</th>
                <th width="12%">কোম্পানি</th>
                <th width="11%">মন্তব্য</th>
                <th width="9%" class="text-center">Status</th>
                <th width="11%" class="text-nowrap">ক্রয় মূল্য <small class="fw-normal">(৳)</small></th>
                <th width="11%" class="text-nowrap">বিক্রয় মূল্য <small class="fw-normal">(৳)</small></th>
                <th width="11%" class="text-nowrap">লাভ <small class="fw-normal">(৳)</small></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($entries)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-3">No entries found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($entries as $i => $entry): ?>
                    <?php $s = $statusLabel[$entry['status']] ?? $statusLabel['I']; ?>
                    <tr class="bazar-product-row" data-id="<?= $entry['id'] ?>">
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($entry['product_name']) ?></strong></td>
                        <td><?= htmlspecialchars($entry['quantity']) ?></td>
                        <td><?= htmlspecialchars($entry['company']) ?></td>
                        <td><?= htmlspecialchars($entry['remarks']) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
                        </td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm buyer-price price-input"
                                   min="0" step="0.01" placeholder="0.00"
                                   value="<?= $entry['buyer_price'] !== '' ? htmlspecialchars($entry['buyer_price']) : '' ?>">
                        </td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm seller-price price-input"
                                   min="0" step="0.01" placeholder="0.00"
                                   value="<?= $entry['seller_price'] !== '' ? htmlspecialchars($entry['seller_price']) : '' ?>">
                        </td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm profit-val"
                                   style="background:#f8f9fa; font-weight:600;"
                                   readonly placeholder="0.00"
                                   value="<?= $entry['profit'] !== '' ? htmlspecialchars($entry['profit']) : '' ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- Sum Row -->
                <tr class="table-secondary fw-bold" id="priceSum">
                    <td colspan="6" class="text-end pe-3">
                        মোট সমষ্টি <span class="fw-normal small">(Total)</span> :
                    </td>
                    <td class="text-primary"><span class="sum-buyer">0.00</span></td>
                    <td class="text-success"><span class="sum-seller">0.00</span></td>
                    <td><span class="sum-profit fw-bold">0.00</span></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
