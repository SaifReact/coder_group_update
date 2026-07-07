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

// Approved — joined with bazar_transaction
try {
    try { $pdo->exec("ALTER TABLE bazar_transaction ADD COLUMN paid_price DECIMAL(10,2) NOT NULL DEFAULT 0.00"); }
    catch (Exception $e) {} // column already exists

    $stmt_a = $pdo->query(
        "SELECT b.member_id, b.month, b.status,
                COUNT(b.id)                     AS item_count,
                m.member_code, m.name_bn, m.name_en, m.mobile,
                COALESCE(t.price, 0)            AS price,
                COALESCE(t.discount, 0)         AS discount,
                COALESCE(t.service_charge, 0)   AS service_charge,
                COALESCE(t.sum_price, 0)        AS sum_price,
                COALESCE(t.paid_price, 0)       AS paid_price,
                COALESCE(t.due_price, 0)        AS due_price
         FROM   monthly_bazar b
         JOIN   members_info  m ON m.id = b.member_id
         LEFT JOIN bazar_transaction t
                ON t.member_id = b.member_id AND t.month = b.month
         WHERE  b.status = 'A'
         GROUP  BY b.member_id, b.month
         ORDER  BY b.member_id DESC"
    );
    $approved_rows = $stmt_a->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $approved_rows = [];
}

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
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:94vw;">
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
                            <table class="table table-bordered table-striped align-middle" style="font-size:.9rem;">
                                <thead class="table-success">
                                    <tr>
                                        <th width="3%">#</th>
                                        <th width="8%">কোড</th>
                                        <th width="13%">সদস্য</th>
                                        <th width="7%">মাস</th>
                                        <th width="4%">পণ্য</th>
                                        <th width="7%" class="text-end">মূল্য (৳)</th>
                                        <th width="6%" class="text-end">ছাড় (৳)</th>
                                        <th width="7%" class="text-end">সার্ভিস চার্জ (৳)</th>
                                        <th width="7%" class="text-end">মোট (৳)</th>
                                        <th width="10%">পরিশোধ (৳)</th>
                                        <th width="7%" class="text-end">বাকি (৳)</th>
                                        <th width="5%" class="text-center">💾</th>
                                        <th width="5%" class="text-center">বিস্তারিত</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($approved_rows)): ?>
                                        <tr>
                                            <td colspan="13" class="text-center text-muted py-4">
                                                কোনো অনুমোদিত বাজার তালিকা নেই।
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($approved_rows as $i => $arow):
                                            $sum  = (float)$arow['sum_price'];
                                            $paid = (float)$arow['paid_price'];
                                            $due  = round($sum - $paid, 2);
                                        ?>
                                            <tr data-sum="<?= $sum ?>">
                                                <td><?= $i + 1 ?></td>
                                                <td><strong><?= htmlspecialchars($arow['member_code']) ?></strong></td>
                                                <td>
                                                    <?= htmlspecialchars($arow['name_bn']) ?><br>
                                                    <small class="text-muted"><?= htmlspecialchars($arow['name_en']) ?></small>
                                                </td>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars($arow['month']) ?></span></td>
                                                <td class="text-center"><span class="badge bg-secondary"><?= $arow['item_count'] ?> টি</span></td>
                                                <td class="text-end"><?= number_format($arow['price'], 2) ?></td>
                                                <td class="text-end text-danger"><?= number_format($arow['discount'], 2) ?></td>
                                                <td class="text-end"><?= number_format($arow['service_charge'], 2) ?></td>
                                                <td class="text-end fw-bold text-primary"><?= number_format($sum, 2) ?></td>
                                                <td>
                                                    <input type="text" inputmode="decimal"
                                                           class="form-control form-control-sm paid-input"
                                                           value="<?= number_format($paid, 2, '.', '') ?>"
                                                           placeholder="০.০০"
                                                           oninput="onPaidInput(this)">
                                                </td>
                                                <td class="text-end fw-bold">
                                                    <span class="live-due <?= $due > 0 ? 'text-danger' : 'text-success' ?>">
                                                        <?= number_format($due, 2) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-warning btn-sm save-paid-btn"
                                                            data-member-id="<?= $arow['member_id'] ?>"
                                                            data-month="<?= htmlspecialchars($arow['month']) ?>"
                                                            onclick="savePaidPrice(this)"
                                                            title="সংরক্ষণ করুন">
                                                        <i class="bi bi-save"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-info btn-sm view-approved-btn"
                                                            data-member-id="<?= $arow['member_id'] ?>"
                                                            data-month="<?= htmlspecialchars($arow['month']) ?>"
                                                            data-name="<?= htmlspecialchars($arow['name_bn']) ?>">
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
// ── Load detail into a modal ────────────────────────────────────────────────
function loadBazarDetail(memberId, month, name, modalId, labelId, bodyId) {
    document.getElementById(labelId).innerHTML =
        'Bazar Details — <span class="fw-normal">' + name + ' / ' + month + '</span>';

    var body = document.getElementById(bodyId);
    body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    body.dataset.memberId = memberId;
    body.dataset.month    = month;

    new bootstrap.Modal(document.getElementById(modalId)).show();

    fetch('bazar_details.php?member_id=' + encodeURIComponent(memberId) + '&month=' + encodeURIComponent(month))
        .then(r => r.text())
        .then(function(html) { body.innerHTML = html; })
        .catch(function()    { body.innerHTML = '<div class="alert alert-danger">Could not load details.</div>'; });
}

// ── Pending list: view detail ───────────────────────────────────────────────
document.querySelectorAll('.view-bazar-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        loadBazarDetail(
            this.dataset.memberId, this.dataset.month, this.dataset.name,
            'viewBazarModal', 'viewBazarModalLabel', 'viewMemberBazarModalBody'
        );
    });
});

// ── Approved list: close list modal, then open detail modal ────────────────
document.querySelectorAll('.view-approved-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var memberId = this.dataset.memberId;
        var month    = this.dataset.month;
        var name     = this.dataset.name;

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

// ── Transaction: auto-calc sum & due ───────────────────────────────────────
function calcTxSummary() {
    var price = parseFloat(document.getElementById('tx_price').value)          || 0;
    var disc  = parseFloat(document.getElementById('tx_discount').value)        || 0;
    var svc   = parseFloat(document.getElementById('tx_service_charge').value)  || 0;
    var sum   = Math.max(0, price - disc + svc);
    document.getElementById('tx_sum_price').value = sum.toFixed(2);
    document.getElementById('tx_due_price').value = sum.toFixed(2);
}

// ── Approve + insert bazar_transaction via AJAX ────────────────────────────
function approveBazar(btn, memberId, month, memberCode, noProduct, price) {
    var discount  = parseFloat(document.getElementById('tx_discount').value)       || 0;
    var svcCharge = parseFloat(document.getElementById('tx_service_charge').value) || 0;
    var sumPrice  = parseFloat(document.getElementById('tx_sum_price').value)      || 0;
    var duePrice  = parseFloat(document.getElementById('tx_due_price').value)      || 0;
    var alertEl   = document.getElementById('txAlert');

    btn.disabled  = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> প্রক্রিয়াকরণ...';
    if (alertEl) alertEl.className = 'd-none';

    var fd = new FormData();
    fd.append('action',         'approve_with_transaction');
    fd.append('member_id',      memberId);
    fd.append('member_code',    memberCode);
    fd.append('month',          month);
    fd.append('no_product',     noProduct);
    fd.append('price',          price);
    fd.append('discount',       discount);
    fd.append('service_charge', svcCharge);
    fd.append('sum_price',      sumPrice);
    fd.append('due_price',      duePrice);

    fetch('../process/monthly_bazar_process.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function(data) {
            if (data.success) {
                if (alertEl) {
                    alertEl.className = 'alert alert-success mb-2 small';
                    alertEl.innerHTML = '✅ ' + data.message;
                }
                setTimeout(function() { location.reload(); }, 1100);
            } else {
                btn.disabled  = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Approve';
                if (alertEl) {
                    alertEl.className = 'alert alert-danger mb-2 small';
                    alertEl.innerHTML = '❌ ' + data.message;
                }
            }
        })
        .catch(function() {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Approve';
        });
}

// ── Paid price: live due_price calculation ─────────────────────────────────
function onPaidInput(input) {
    var tr  = input.closest('tr');
    var sum = parseFloat(tr.dataset.sum) || 0;
    var paid = parseFloat(input.value)   || 0;
    var due = Math.max(0, sum - paid);
    var dueEl = tr.querySelector('.live-due');
    dueEl.textContent = due.toFixed(2);
    dueEl.className   = 'live-due fw-bold ' + (due > 0 ? 'text-danger' : 'text-success');
}

// ── Save paid_price + due_price via AJAX ───────────────────────────────────
function savePaidPrice(btn) {
    var tr       = btn.closest('tr');
    var memberId = btn.dataset.memberId;
    var month    = btn.dataset.month;
    var paid     = parseFloat(tr.querySelector('.paid-input').value) || 0;
    var due      = parseFloat(tr.querySelector('.live-due').textContent) || 0;

    btn.disabled  = true;
    var orig = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    var fd = new FormData();
    fd.append('action',     'update_paid_price');
    fd.append('member_id',  memberId);
    fd.append('month',      month);
    fd.append('paid_price', paid);
    fd.append('due_price',  due);

    fetch('../process/monthly_bazar_process.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function(data) {
            btn.disabled  = false;
            btn.innerHTML = data.success ? '<i class="bi bi-check-lg"></i>' : orig;
            btn.className = 'btn btn-sm save-paid-btn ' + (data.success ? 'btn-success' : 'btn-danger');
            if (data.success) {
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-save"></i>';
                    btn.className = 'btn btn-warning btn-sm save-paid-btn';
                }, 1800);
            }
        })
        .catch(function() {
            btn.disabled  = false;
            btn.innerHTML = orig;
        });
}

// ── CSV download (read-only table) ─────────────────────────────────────────
function downloadBazarCSV(btn) {
    var container = btn.closest('.modal-body');
    if (!container) return;

    var csvRows = [['#', 'পণ্যের নাম', 'পরিমাণ', 'কোম্পানি', 'মন্তব্য', 'বিক্রয় মূল্য (৳)', 'Status']];

    var i = 0;
    container.querySelectorAll('#bazarDetailTable tbody tr:not(.table-secondary)').forEach(function(row) {
        var cells = row.querySelectorAll('td');
        if (cells.length < 7) return;
        i++;
        csvRows.push([
            i,
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].textContent.trim()
        ]);
    });

    var csv = csvRows.map(function(r) {
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
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
