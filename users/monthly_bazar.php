<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg']   ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

$months = [
    'January'  => 'January (জানুয়ারি)',
    'February' => 'February (ফেব্রুয়ারি)',
    'March'    => 'March (মার্চ)',
    'April'    => 'April (এপ্রিল)',
    'May'      => 'May (মে)',
    'June'     => 'June (জুন)',
    'July'     => 'July (জুলাই)',
    'August'   => 'August (আগস্ট)',
    'September'=> 'September (সেপ্টেম্বর)',
    'October'  => 'October (অক্টোবর)',
    'November' => 'November (নভেম্বর)',
    'December' => 'December (ডিসেম্বর)',
];

$current_month       = date('F');
$current_month_label = $months[$current_month] ?? $current_month;

$units = [
    'KG'     => 'KG (কেজি)',
    'Gram'   => 'Gram (গ্রাম)',
    'Liter'  => 'Liter (লিটার)',
    'ML'     => 'ML (মিলিলিটার)',
    'Piece'  => 'Piece (পিস)',
    'Dozen'  => 'Dozen (ডজন)',
    'Pack'   => 'Pack (প্যাক)',
    'Bag'    => 'Bag (বস্তা)',
    'Bottle' => 'Bottle (বোতল)',
    'Can'    => 'Can (ক্যান)',
    'Box'    => 'Box (বাক্স)',
    'Bundle' => 'Bundle (বান্ডিল)',
    'Tin'    => 'Tin (টিন)',
];

// Fetch bazar records
$stmt = $pdo->prepare("SELECT * FROM monthly_bazar WHERE member_id = ? ORDER BY id DESC");
$stmt->execute([$member_id]);
$bazars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories from DB
try {
    $categories = $pdo->query("SELECT * FROM category ORDER BY category_name_bn")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $categories = []; }

// Fetch active products for bazar (for_uses = b or be)
try {
    $products_db = $pdo->query(
        "SELECT id, category_id, product_name, product_name_bn, seller_price
         FROM product
         WHERE status = 'A' AND for_uses IN ('b','be')
         ORDER BY product_name_bn"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $products_db = []; }

// Fetch companies from pcompany table
try {
    $pcompanies = $pdo->query("SELECT * FROM pcompany ORDER BY company_name_bn")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $pcompanies = []; }

// Build unit HTML
$unitOptHtml = '<option value="">একক</option>';
foreach ($units as $val => $label) {
    $unitOptHtml .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . '</option>';
}

// Build category HTML
$categoryOptHtml = '<option value="">-- সব ক্যাটাগরি --</option>';
foreach ($categories as $cat) {
    $categoryOptHtml .= '<option value="' . (int)$cat['id'] . '">'
        . htmlspecialchars($cat['category_name_bn']) . ' (' . htmlspecialchars($cat['category_name']) . ')</option>';
}

// Build pcompany HTML
$pcompanyOptHtml = '<option value="">-- কোম্পানী নির্বাচন করুন --</option>';
foreach ($pcompanies as $co) {
    $pcompanyOptHtml .= '<option value="' . htmlspecialchars($co['company_name_bn']) . '">'
        . htmlspecialchars($co['company_name_bn']) . ' (' . htmlspecialchars($co['company_name']) . ')</option>';
}

// Build products JSON for JS
$productsJson = json_encode(array_map(function ($p) {
    return [
        'cat_id'  => (int)$p['category_id'],
        'name_bn' => $p['product_name_bn'],
        'name_en' => $p['product_name'],
        'price'   => (float)$p['seller_price'],
    ];
}, $products_db), JSON_UNESCAPED_UNICODE);

// Build product option HTML for edit modal (all products)
$productOptHtml = '<option value="">-- পণ্য নির্বাচন করুন --</option>';
foreach ($products_db as $p) {
    $productOptHtml .= '<option value="' . htmlspecialchars($p['product_name_bn']) . '"'
        . ' data-price="' . (float)$p['seller_price'] . '"'
        . ' data-catid="' . (int)$p['category_id'] . '">'
        . htmlspecialchars($p['product_name_bn']) . ' (' . htmlspecialchars($p['product_name']) . ')</option>';
}
?>

<?php
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
<div class="row px-2">

    <!-- Add Form Card -->
    <div class="card shadow-lg rounded-3 border-0 mb-4">
        <div class="card-body p-4">

            <h3 class="mb-1 text-primary fw-bold">
                Monthly Bazar <span class="text-secondary">( মাসিক বাজার )</span>
            </h3>
            <hr class="mb-4"/>

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

            <form action="../process/monthly_bazar_process.php" method="post" id="bazarForm" autocomplete="off">
                <input type="hidden" name="action" value="insert_bulk">
                <input type="hidden" name="member_id" value="<?= htmlspecialchars($member_id) ?>">

                <!-- Month row -->
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">
                            Month <span class="text-secondary small">(মাস)</span>
                        </label>
                        <input type="text"
                               class="form-control fw-bold text-primary"
                               value="<?= htmlspecialchars($current_month_label) ?>"
                               readonly
                               style="background:#eef4ff;cursor:not-allowed;">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($current_month) ?>">
                    </div>
                    <div class="col-12 col-md-9 d-flex align-items-end justify-content-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-success px-4" onclick="addRow()">
                            <i class="bi bi-plus-circle me-1"></i> Add Row (রো যুক্ত করুন)
                        </button>
                    </div>
                </div>

                <!-- Dynamic rows table -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="inputTable" style="min-width:1100px;">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:38px">#</th>
                                <th style="min-width:160px">ক্যাটাগরি <span class="fw-normal small">(Category)</span></th>
                                <th style="min-width:210px">পণ্যের নাম <span class="fw-normal small">(Product)</span></th>
                                <th style="min-width:100px">মূল্য <span class="fw-normal small">(Price ৳)</span></th>
                                <th style="min-width:185px">পরিমাণ <span class="fw-normal small">(Quantity)</span></th>
                                <th style="min-width:190px">কোম্পানি <span class="fw-normal small">(Company)</span></th>
                                <th style="min-width:130px">মন্তব্য <span class="fw-normal small">(Remarks)</span></th>
                                <th style="width:46px" class="text-center"><i class="bi bi-trash text-danger"></i></th>
                            </tr>
                        </thead>
                        <tbody id="rowContainer"></tbody>
                    </table>
                </div>

                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-save me-1"></i> Save All Records ( সংরক্ষণ করুন )
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Records table -->
    <div class="card shadow-lg rounded-3 border-0">
        <div class="card-body p-4">
            <h5 class="mb-3 fw-bold text-secondary">
                Bazar List <span class="text-primary">( বাজার তালিকা )</span>
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>মাস (Month)</th>
                            <th>পণ্যের নাম (Product)</th>
                            <th class="text-end">মূল্য (Price)</th>
                            <th>পরিমাণ (Quantity)</th>
                            <th>কোম্পানি (Company)</th>
                            <th>মন্তব্য (Remarks)</th>
                            <th class="text-center">কার্যকলাপ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bazars)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    কোনো তথ্য পাওয়া যায়নি।
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bazars as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($row['month']) ?></span></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="text-end">
                                        <?php if (!empty($row['seller_price'])): ?>
                                            <span class="text-success fw-semibold">৳<?= number_format((float)$row['seller_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                                    <td><?= htmlspecialchars($row['company']) ?></td>
                                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                                    <td class="text-center text-nowrap">
                                        <button class="btn btn-sm btn-warning me-1"
                                            onclick="openEditModal(<?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="../process/monthly_bazar_process.php" method="post"
                                              class="d-inline"
                                              onsubmit="return confirm('এই তথ্যটি মুছে ফেলবেন?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
</main>
</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="../process/monthly_bazar_process.php" method="post" autocomplete="off">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Record <span class="fw-normal small">( তথ্য সম্পাদনা )</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">মাস (Month)</label>
                            <select class="form-select" name="month" id="edit_month" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($months as $val => $label): ?>
                                    <option value="<?= $val ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরি (Category)</label>
                            <select class="form-select" id="edit_cat_id" onchange="rebuildEditProducts()">
                                <option value="">-- সব ক্যাটাগরি --</option>
                                <?= $categoryOptHtml ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">পণ্যের নাম (Product)</label>
                            <select class="form-select" name="product_name" id="edit_product_name"
                                    onchange="onEditProductChange()" required>
                                <?= $productOptHtml ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">মূল্য (Price ৳)</label>
                            <input type="text" inputmode="decimal" class="form-control"
                                   name="seller_price" id="edit_price" value="0" placeholder="০.০০">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">পরিমাণ (Quantity)</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control"
                                       name="edit_qty_amount" id="edit_qty_amount" placeholder="পরিমান" required>
                                <select class="form-select" name="edit_qty_unit" id="edit_qty_unit"
                                        style="max-width:100px;" required>
                                    <?= $unitOptHtml ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">কোম্পানি (Company)</label>
                            <select class="form-select" name="company" id="edit_company">
                                <?= $pcompanyOptHtml ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">মন্তব্য (Remarks)</label>
                            <textarea class="form-control" name="remarks" id="edit_remarks" rows="2"></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update ( হালনাগাদ করুন )
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Product list from DB
const PRODUCTS = <?= $productsJson ?>;

// Static HTML snippets
const CATEGORY_HTML = <?= json_encode($categoryOptHtml) ?>;
const PCOMPANY_HTML = <?= json_encode($pcompanyOptHtml) ?>;
const UNIT_HTML     = <?= json_encode($unitOptHtml) ?>;

let rowCounter = 0;

function esc(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Build product <option> HTML for a row: exclude usedElsewhere, filter by catId (0 = all)
function buildProductOpts(catId, usedElsewhere) {
    let html = '<option value="">-- পণ্য নির্বাচন করুন --</option>';
    PRODUCTS.forEach(p => {
        if (usedElsewhere.has(p.name_bn)) return;
        if (catId && p.cat_id !== catId) return;
        const label = p.name_bn + (p.name_en ? ' (' + p.name_en + ')' : '');
        html += '<option value="' + esc(p.name_bn) + '" data-price="' + p.price + '">' + esc(label) + '</option>';
    });
    return html;
}

// Rebuild product options for a single row (respects its own category filter + dedup)
function rebuildProductsForRow(rowId) {
    const row     = document.getElementById(rowId);
    if (!row) return;
    const catSel  = row.querySelector('.cat-select');
    const prodSel = row.querySelector('.product-select');
    const catId   = catSel ? (parseInt(catSel.value) || 0) : 0;
    const ownVal  = prodSel.value;

    // Collect values selected in OTHER rows
    const usedElsewhere = new Set();
    document.querySelectorAll('.product-select').forEach(sel => {
        if (sel.dataset.rowid !== rowId && sel.value) usedElsewhere.add(sel.value);
    });

    prodSel.innerHTML = buildProductOpts(catId, usedElsewhere);
    if (ownVal) {
        prodSel.value = ownVal;
        if (!prodSel.value) setPrice(rowId, 0); // category filter removed it
    }
}

// Rebuild ALL rows (used after product selection changes for dedup)
function refreshAllRows() {
    document.querySelectorAll('.product-select').forEach(sel => {
        rebuildProductsForRow(sel.dataset.rowid);
    });
}

// Called when category changes in a row
function onCategoryChange(rowId) {
    rebuildProductsForRow(rowId);
}

// Called when product selection changes in a row
function onProductChange(rowId) {
    const row     = document.getElementById(rowId);
    const prodSel = row.querySelector('.product-select');
    const opt     = prodSel.options[prodSel.selectedIndex];
    setPrice(rowId, opt ? parseFloat(opt.dataset.price) || 0 : 0);
    refreshAllRows(); // update dedup for other rows
}

function setPrice(rowId, price) {
    const row = document.getElementById(rowId);
    const pi  = row ? row.querySelector('.price-input') : null;
    if (pi) pi.value = price.toFixed(2);
}

function addRow() {
    rowCounter++;
    const id = 'row_' + rowCounter;
    const tr = document.createElement('tr');
    tr.id = id;
    tr.innerHTML =
        '<td class="text-center text-muted fw-bold row-num"></td>' +

        '<td>' +
            '<select class="form-select form-select-sm cat-select" name="category_id[]"' +
                    ' data-rowid="' + id + '"' +
                    ' onchange="onCategoryChange(\'' + id + '\')">' +
                CATEGORY_HTML +
            '</select>' +
        '</td>' +

        '<td>' +
            '<select class="form-select form-select-sm product-select" name="product_name[]"' +
                    ' data-rowid="' + id + '"' +
                    ' onchange="onProductChange(\'' + id + '\')" required>' +
            '</select>' +
        '</td>' +

        '<td>' +
            '<input type="text" inputmode="decimal" class="form-control form-control-sm price-input"' +
                   ' name="seller_price[]" data-rowid="' + id + '"' +
                   ' value="0" placeholder="০.০০">' +
        '</td>' +

        '<td>' +
            '<div class="input-group input-group-sm">' +
                '<input type="text" inputmode="decimal" class="form-control" name="qty_amount[]"' +
                       ' placeholder="পরিমান" required>' +
                '<select class="form-select" name="qty_unit[]" style="max-width:82px;" required>' +
                    UNIT_HTML +
                '</select>' +
            '</div>' +
        '</td>' +

        '<td>' +
            '<select class="form-select form-select-sm" name="company[]">' +
                PCOMPANY_HTML +
            '</select>' +
        '</td>' +

        '<td>' +
            '<input type="text" class="form-control form-control-sm" name="remarks[]" placeholder="Optional">' +
        '</td>' +

        '<td class="text-center">' +
            '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(\'' + id + '\')">' +
                '<i class="bi bi-x-lg"></i>' +
            '</button>' +
        '</td>';

    document.getElementById('rowContainer').appendChild(tr);
    reNumber();
    rebuildProductsForRow(id); // populate product options
}

function removeRow(id) {
    const row = document.getElementById(id);
    if (row) {
        row.remove();
        refreshAllRows();
        reNumber();
    }
}

function reNumber() {
    document.querySelectorAll('#rowContainer tr .row-num').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

// Require at least one row on submit
document.getElementById('bazarForm').addEventListener('submit', function(e) {
    if (document.querySelectorAll('#rowContainer tr').length === 0) {
        e.preventDefault();
        alert('অন্তত একটি পণ্য যোগ করুন।');
    }
});

// ── Edit modal ─────────────────────────────────────────────────────────────

function rebuildEditProducts() {
    const catSel  = document.getElementById('edit_cat_id');
    const prodSel = document.getElementById('edit_product_name');
    const catId   = parseInt(catSel.value) || 0;
    const curVal  = prodSel.value;

    let html = '<option value="">-- পণ্য নির্বাচন করুন --</option>';
    PRODUCTS.forEach(p => {
        if (catId && p.cat_id !== catId) return;
        const label = p.name_bn + (p.name_en ? ' (' + p.name_en + ')' : '');
        html += '<option value="' + esc(p.name_bn) + '" data-price="' + p.price + '">' + esc(label) + '</option>';
    });
    prodSel.innerHTML = html;
    if (curVal) prodSel.value = curVal;
}

function onEditProductChange() {
    const prodSel = document.getElementById('edit_product_name');
    const opt     = prodSel.options[prodSel.selectedIndex];
    const price   = opt ? parseFloat(opt.dataset.price) || 0 : 0;
    document.getElementById('edit_price').value = price.toFixed(2);
}

function openEditModal(row) {
    document.getElementById('edit_id').value    = row.id;
    document.getElementById('edit_month').value = row.month;

    // Try to find product in PRODUCTS list to get category
    const prod = PRODUCTS.find(p => p.name_bn === row.product_name);
    document.getElementById('edit_cat_id').value = prod ? prod.cat_id : '';

    rebuildEditProducts();
    document.getElementById('edit_product_name').value = row.product_name;

    // Price: use stored seller_price if available, else from product
    const storedPrice = parseFloat(row.seller_price) || 0;
    document.getElementById('edit_price').value = (storedPrice || (prod ? prod.price : 0)).toFixed(2);

    // Quantity: split "2.5 KG" → amount & unit
    const qtyParts = (row.quantity || '').trim().split(' ');
    document.getElementById('edit_qty_amount').value = qtyParts[0] || '';
    document.getElementById('edit_qty_unit').value   = qtyParts[1] || 'KG';

    document.getElementById('edit_company').value  = row.company  || '';
    document.getElementById('edit_remarks').value  = row.remarks  || '';

    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Start with one empty row on load
addRow();
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
