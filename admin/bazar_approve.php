<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg']   ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Pending / Processing / Rejected
$stmt = $pdo->query(
    "SELECT b.member_id, b.month, b.status,
            COUNT(b.id) AS item_count,
            m.member_code, m.name_bn, m.name_en, m.mobile
     FROM   monthly_bazar b
     JOIN   members_info  m ON m.id = b.member_id
     WHERE  b.status IN ('I','P','R')
     GROUP  BY b.member_id, b.month
     ORDER  BY b.member_id DESC"
);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Approved — joined with bazar_hisab for financial data
$stmt_a = $pdo->query(
    "SELECT b.member_id, b.month, b.status,
            COUNT(b.id)                   AS item_count,
            m.member_code, m.name_bn, m.name_en, m.mobile,
            COALESCE(h.total_price, 0)    AS total_price,
            COALESCE(h.paid_amt, 0)       AS paid_amt,
            COALESCE(h.due_amt, 0)        AS due_amt
     FROM   monthly_bazar b
     JOIN   members_info  m ON m.id = b.member_id
     LEFT JOIN bazar_hisab h ON h.member_id = b.member_id AND h.month = b.month
     WHERE  b.status = 'A'
     GROUP  BY b.member_id, b.month
     ORDER  BY b.member_id DESC"
);
$approved_rows = $stmt_a->fetchAll(PDO::FETCH_ASSOC);

$badgeMap = [
    'I' => ['bg-warning text-dark', '⏳ Pending'],
    'P' => ['bg-info text-dark',    '⏳ Processing'],
    'A' => ['bg-success',           '✅ Approved'],
    'R' => ['bg-danger',            '❌ Rejected'],
];
?>

<?php
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0 text-primary fw-bold">
                        Bazar Approval <span class="text-secondary fs-5">( বাজার অনুমোদন )</span>
                    </h3>
                    <button type="button" class="btn btn-success"
                            data-bs-toggle="modal" data-bs-target="#approvedListModal">
                        ✅ Approved List
                        <span class="badge bg-white text-success ms-1"><?= count($approved_rows) ?></span>
                    </button>
                </div>
                <hr class="mb-4" />

                <?php if ($success_msg): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($success_msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error_msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Pending / Processing / Rejected Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="12%">সদস্য কোড</th>
                                <th width="24%">সদস্যের নাম</th>
                                <th width="12%">মাস</th>
                                <th width="8%">পণ্য</th>
                                <th width="8%">স্ট্যাটাস</th>
                                <th width="26%">কর্মকান্ড</th>
                                <th width="5%" class="text-center">বিস্তারিত</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rows)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        কোনো অপেক্ষমান বাজার তালিকা নেই।
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rows as $i => $row): ?>
                                    <?php [$bc, $bl] = $badgeMap[$row['status']] ?? $badgeMap['I']; ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($row['member_code']) ?></strong></td>
                                        <td>
                                            <?= htmlspecialchars($row['name_bn']) ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($row['name_en']) ?></small><br>
                                            <small class="text-secondary"><?= htmlspecialchars($row['mobile']) ?></small>
                                        </td>
                                        <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($row['month']) ?></span></td>
                                        <td class="text-center"><span class="badge bg-secondary fs-6"><?= $row['item_count'] ?> টি</span></td>
                                        <td class="text-center"><span class="badge <?= $bc ?> fs-6"><?= $bl ?></span></td>
                                        <td>
                                            <form method="post" action="../process/monthly_bazar_process.php"
                                                  class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="action"    value="update_status">
                                                <input type="hidden" name="member_id" value="<?= $row['member_id'] ?>">
                                                <input type="hidden" name="month"     value="<?= htmlspecialchars($row['month']) ?>">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="I" <?= $row['status']==='I' ? 'selected' : '' ?>>⏳ Pending</option>
                                                    <option value="P" <?= $row['status']==='P' ? 'selected' : '' ?>>⏳ Processing</option>
                                                    <option value="A" <?= $row['status']==='A' ? 'selected' : '' ?>>✅ Approved</option>
                                                    <option value="R" <?= $row['status']==='R' ? 'selected' : '' ?>>❌ Rejected</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm text-nowrap">Update</button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm view-bazar-btn"
                                                    data-member-id="<?= $row['member_id'] ?>"
                                                    data-month="<?= htmlspecialchars($row['month']) ?>"
                                                    data-name="<?= htmlspecialchars($row['name_bn']) ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- ===== Pending Detail Modal ===== -->
        <div class="modal fade" id="viewBazarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:92vw;">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewBazarModalLabel">Bazar Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="viewMemberBazarModalBody">
                        <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Approved List Modal ===== -->
        <div class="modal fade" id="approvedListModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:90vw;">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            ✅ Approved Bazar List
                            <span class="fw-normal small ms-1">( অনুমোদিত বাজার )</span>
                            <span class="badge bg-white text-success ms-2"><?= count($approved_rows) ?></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-success">
                                    <tr>
                                        <th width="4%">#</th>
                                        <th width="10%">কোড</th>
                                        <th width="18%">সদস্য</th>
                                        <th width="9%">মাস</th>
                                        <th width="6%">পণ্য</th>
                                        <th width="10%">মোট মূল্য (৳)</th>
                                        <th width="10%">পরিশোধ (৳)</th>
                                        <th width="10%">বাকি (৳)</th>
                                        <th width="9%" class="text-center">💰 হিসাব</th>
                                        <th width="6%" class="text-center">বিস্তারিত</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($approved_rows)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                কোনো অনুমোদিত বাজার তালিকা নেই।
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($approved_rows as $i => $arow): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><strong><?= htmlspecialchars($arow['member_code']) ?></strong></td>
                                                <td>
                                                    <?= htmlspecialchars($arow['name_bn']) ?><br>
                                                    <small class="text-muted"><?= htmlspecialchars($arow['name_en']) ?></small>
                                                </td>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars($arow['month']) ?></span></td>
                                                <td class="text-center"><span class="badge bg-secondary"><?= $arow['item_count'] ?> টি</span></td>
                                                <td class="text-end fw-semibold">
                                                    <?= number_format($arow['total_price'], 2) ?>
                                                </td>
                                                <td class="text-end text-success fw-semibold">
                                                    <?= number_format($arow['paid_amt'], 2) ?>
                                                </td>
                                                <td class="text-end fw-bold <?= $arow['due_amt'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                                    <?= number_format($arow['due_amt'], 2) ?>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-warning btn-sm hisab-edit-btn"
                                                            data-member-id="<?= $arow['member_id'] ?>"
                                                            data-month="<?= htmlspecialchars($arow['month']) ?>"
                                                            data-name="<?= htmlspecialchars($arow['name_bn']) ?>"
                                                            data-total="<?= $arow['total_price'] ?>"
                                                            data-paid="<?= $arow['paid_amt'] ?>"
                                                            title="Edit Hisab">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-info btn-sm view-approved-btn"
                                                            data-member-id="<?= $arow['member_id'] ?>"
                                                            data-month="<?= htmlspecialchars($arow['month']) ?>"
                                                            data-name="<?= htmlspecialchars($arow['name_bn']) ?>"
                                                            title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Hisab Edit Modal ===== -->
        <div class="modal fade" id="hisabModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h6 class="modal-title fw-bold" id="hisabModalLabel">💰 Payment Hisab</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hisabMemberId">
                        <input type="hidden" id="hisabMonth">
                        <div id="hisabAlert" class="d-none mb-2 small"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">মোট মূল্য (৳) <span class="text-secondary">(Total Price)</span></label>
                            <input type="number" class="form-control form-control-sm" id="hisabTotalPrice"
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">পরিশোধ (৳) <span class="text-secondary">(Paid Amount)</span></label>
                            <input type="number" class="form-control form-control-sm" id="hisabPaidAmt"
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold small">বাকি (৳) <span class="text-secondary">(Due Amount)</span></label>
                            <input type="number" class="form-control form-control-sm" id="hisabDueAmt"
                                   style="background:#f8f9fa; font-weight:bold;" readonly placeholder="0.00">
                        </div>
                        <div class="mt-2 small text-muted text-center">বাকি = মোট মূল্য − পরিশোধ</div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">বাতিল</button>
                        <button type="button" class="btn btn-primary btn-sm" id="hisabSaveBtn" onclick="saveHisab()">
                            <span id="hisabBtnText"><i class="bi bi-save me-1"></i> সংরক্ষণ করুন</span>
                            <span id="hisabBtnSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Approved Detail View Modal ===== -->
        <div class="modal fade" id="viewApprovedBazarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:92vw;">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="viewApprovedBazarModalLabel">Bazar Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="viewApprovedBazarModalBody">
                        <div class="text-center py-4"><div class="spinner-border text-success" role="status"></div></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
</div>
</div>

<script>
// ===== Pending list view detail =====
document.querySelectorAll('.view-bazar-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        loadBazarDetail(
            this.getAttribute('data-member-id'),
            this.getAttribute('data-month'),
            this.getAttribute('data-name'),
            'viewBazarModal', 'viewBazarModalLabel', 'viewMemberBazarModalBody'
        );
    });
});

// ===== Approved list: close list modal, open detail modal =====
document.querySelectorAll('.view-approved-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var memberId = this.getAttribute('data-member-id');
        var month    = this.getAttribute('data-month');
        var name     = this.getAttribute('data-name');

        var listModalEl = document.getElementById('approvedListModal');
        var listModal   = bootstrap.Modal.getInstance(listModalEl);
        if (listModal) listModal.hide();

        listModalEl.addEventListener('hidden.bs.modal', function handler() {
            listModalEl.removeEventListener('hidden.bs.modal', handler);
            loadBazarDetail(memberId, month, name,
                'viewApprovedBazarModal', 'viewApprovedBazarModalLabel', 'viewApprovedBazarModalBody');
        });
    });
});

// ===== Common: load bazar_details.php into any modal =====
function loadBazarDetail(memberId, month, name, modalId, labelId, bodyId) {
    document.getElementById(labelId).innerHTML =
        'Bazar Details — <span class="fw-normal">' + name + ' / ' + month + '</span>';

    var body = document.getElementById(bodyId);
    body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';

    new bootstrap.Modal(document.getElementById(modalId)).show();

    // Store member_id & month so saveBazarPrices can send them
    body.dataset.memberId = memberId;
    body.dataset.month    = month;

    fetch('bazar_details.php?member_id=' + encodeURIComponent(memberId) + '&month=' + encodeURIComponent(month))
        .then(r => r.text())
        .then(function(html) {
            body.innerHTML = html;
            initBazarPrices(body);
        })
        .catch(function() {
            body.innerHTML = '<div class="alert alert-danger">Could not load details.</div>';
        });
}

// ===== Price calculation =====
function initBazarPrices(container) {
    container.querySelectorAll('.price-input').forEach(function(inp) {
        inp.addEventListener('input', function() { updatePriceCalc(container); });
    });
    updatePriceCalc(container);
}

function updatePriceCalc(container) {
    var totalBuyer = 0, totalSeller = 0, totalProfit = 0;
    container.querySelectorAll('tr.bazar-product-row').forEach(function(row) {
        var buyer  = parseFloat(row.querySelector('.buyer-price').value)  || 0;
        var seller = parseFloat(row.querySelector('.seller-price').value) || 0;
        var profit = seller - buyer;
        var profitEl = row.querySelector('.profit-val');
        profitEl.value = (buyer || seller) ? profit.toFixed(2) : '';
        profitEl.style.color = profit >= 0 ? '#198754' : '#dc3545';
        totalBuyer  += buyer;
        totalSeller += seller;
        totalProfit += profit;
    });
    var sumRow = container.querySelector('#priceSum');
    if (sumRow) {
        sumRow.querySelector('.sum-buyer').textContent  = totalBuyer.toFixed(2);
        sumRow.querySelector('.sum-seller').textContent = totalSeller.toFixed(2);
        var sp = sumRow.querySelector('.sum-profit');
        sp.textContent = totalProfit.toFixed(2);
        sp.style.color = totalProfit >= 0 ? '#198754' : '#dc3545';
    }
}

// ===== Save prices to DB (called from bazar_details.php onclick) =====
function saveBazarPrices(btn) {
    var container = btn.closest('.modal-body');
    if (!container) return;

    var ids = [], buyers = [], sellers = [];
    container.querySelectorAll('tr.bazar-product-row').forEach(function(row) {
        ids.push(row.getAttribute('data-id'));
        buyers.push(row.querySelector('.buyer-price').value  || '0');
        sellers.push(row.querySelector('.seller-price').value || '0');
    });

    var alertEl = container.querySelector('#pricesSaveAlert');
    if (alertEl) alertEl.className = 'd-none';

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> সংরক্ষণ হচ্ছে...';

    var formData = new FormData();
    formData.append('action',    'update_prices');
    formData.append('member_id', container.dataset.memberId || '');
    formData.append('month',     container.dataset.month    || '');
    ids.forEach(function(id)     { formData.append('ids[]', id); });
    buyers.forEach(function(b)   { formData.append('buyer_prices[]', b); });
    sellers.forEach(function(s)  { formData.append('seller_prices[]', s); });

    fetch('../process/monthly_bazar_process.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i> দাম সংরক্ষণ করুন';
            if (alertEl) {
                alertEl.className = data.success
                    ? 'alert alert-success alert-dismissible fade show small mb-2'
                    : 'alert alert-danger alert-dismissible fade show small mb-2';
                alertEl.innerHTML = (data.success ? '✅ ' : '❌ ') + data.message +
                    '<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>';
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i> দাম সংরক্ষণ করুন';
        });
}

// ===== CSV Download =====
function downloadBazarCSV(btn) {
    var container = btn.closest('.modal-body');
    if (!container) return;

    var rows = [['#', 'পণ্যের নাম', 'পরিমাণ', 'কোম্পানি', 'মন্তব্য', 'Status',
                 'ক্রয় মূল্য (৳)', 'বিক্রয় মূল্য (৳)', 'লাভ (৳)']];

    container.querySelectorAll('tr.bazar-product-row').forEach(function(row, i) {
        var cells = row.querySelectorAll('td');
        rows.push([
            i + 1,
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            row.querySelector('.buyer-price').value  || '0',
            row.querySelector('.seller-price').value || '0',
            row.querySelector('.profit-val').value   || '0'
        ]);
    });

    var sumRow = container.querySelector('#priceSum');
    if (sumRow) {
        rows.push(['মোট', '', '', '', '', '',
            sumRow.querySelector('.sum-buyer').textContent,
            sumRow.querySelector('.sum-seller').textContent,
            sumRow.querySelector('.sum-profit').textContent
        ]);
    }

    var csv = rows.map(function(r) {
        return r.map(function(v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(',');
    }).join('\n');

    var blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'bazar_details.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(a.href);
}

// ===== Hisab edit modal =====
document.querySelectorAll('.hisab-edit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('hisabMemberId').value   = this.getAttribute('data-member-id');
        document.getElementById('hisabMonth').value      = this.getAttribute('data-month');
        document.getElementById('hisabModalLabel').textContent =
            '💰 ' + this.getAttribute('data-name') + ' / ' + this.getAttribute('data-month');
        document.getElementById('hisabTotalPrice').value = this.getAttribute('data-total');
        document.getElementById('hisabPaidAmt').value    = this.getAttribute('data-paid');
        document.getElementById('hisabAlert').className  = 'd-none';
        updateHisabDue();
        new bootstrap.Modal(document.getElementById('hisabModal')).show();
    });
});

document.getElementById('hisabTotalPrice').addEventListener('input', updateHisabDue);
document.getElementById('hisabPaidAmt').addEventListener('input',    updateHisabDue);

function updateHisabDue() {
    var total = parseFloat(document.getElementById('hisabTotalPrice').value) || 0;
    var paid  = parseFloat(document.getElementById('hisabPaidAmt').value)    || 0;
    var due   = total - paid;
    var dueEl = document.getElementById('hisabDueAmt');
    dueEl.value      = due.toFixed(2);
    dueEl.style.color = due > 0 ? '#dc3545' : '#198754';
}

function saveHisab() {
    var alertEl = document.getElementById('hisabAlert');
    document.getElementById('hisabBtnText').classList.add('d-none');
    document.getElementById('hisabBtnSpinner').classList.remove('d-none');
    document.getElementById('hisabSaveBtn').disabled = true;

    var formData = new FormData();
    formData.append('action',       'upsert');
    formData.append('member_id',    document.getElementById('hisabMemberId').value);
    formData.append('month',        document.getElementById('hisabMonth').value);
    formData.append('total_price',  document.getElementById('hisabTotalPrice').value || '0');
    formData.append('paid_amt',     document.getElementById('hisabPaidAmt').value    || '0');

    fetch('../process/bazar_hisab_process.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(function(data) {
            document.getElementById('hisabBtnText').classList.remove('d-none');
            document.getElementById('hisabBtnSpinner').classList.add('d-none');
            document.getElementById('hisabSaveBtn').disabled = false;

            if (data.success) {
                alertEl.className = 'alert alert-success mb-2 small';
                alertEl.textContent = '✅ সফলভাবে সংরক্ষিত হয়েছে!';
                setTimeout(function() {
                    bootstrap.Modal.getInstance(document.getElementById('hisabModal')).hide();
                    window.location.reload();
                }, 900);
            } else {
                alertEl.className = 'alert alert-danger mb-2 small';
                alertEl.textContent = data.message;
            }
        })
        .catch(function() {
            document.getElementById('hisabBtnText').classList.remove('d-none');
            document.getElementById('hisabBtnSpinner').classList.add('d-none');
            document.getElementById('hisabSaveBtn').disabled = false;
            alertEl.className = 'alert alert-danger mb-2 small';
            alertEl.textContent = 'Server error. Please try again.';
        });
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
