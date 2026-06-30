<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id     = $_SESSION['member_id'];
$current_month       = date('F'); // stored value (English key)
$current_month_label = $months[$current_month] ?? $current_month; // display with Bangla

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg']   ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

$stmt = $pdo->prepare("SELECT * FROM monthly_bazar WHERE member_id = ? ORDER BY id DESC");
$stmt->execute([$member_id]);
$bazars = $stmt->fetchAll();

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

// Bangladesh grocery product list (key = stored value, value = display label)
$product_groups = [
    'Rice (চাল)' => [
        'Miniket Rice'    => 'Miniket Rice (মিনিকেট চাল)',
        'Nazirshail Rice' => 'Nazirshail Rice (নাজিরশাইল চাল)',
        'BR28 Rice'       => 'BR28 Rice (বিআর২৮ চাল)',
        'Paijam Rice'     => 'Paijam Rice (পাইজাম চাল)',
        'Kalijira Rice'   => 'Kalijira Rice (কালিজিরা চাল)',
    ],
    'Dal / Lentils (ডাল)' => [
        'Musur Dal'    => 'Musur Dal (মসুর ডাল)',
        'Mung Dal'     => 'Mung Dal (মুগ ডাল)',
        'Maskalai Dal' => 'Maskalai Dal (মাসকলাই ডাল)',
        'Chola Dal'    => 'Chola Dal (ছোলার ডাল)',
        'Motor Dal'    => 'Motor Dal (মটর ডাল)',
    ],
    'Oil (তেল)' => [
        'Soybean Oil'  => 'Soybean Oil (সয়াবিন তেল)',
        'Mustard Oil'  => 'Mustard Oil (সরিষার তেল)',
        'Palm Oil'     => 'Palm Oil (পাম তেল)',
    ],
    'Spices (মশলা)' => [
        'Onion'            => 'Onion (পেঁয়াজ)',
        'Garlic'           => 'Garlic (রসুন)',
        'Ginger'           => 'Ginger (আদা)',
        'Green Chili'      => 'Green Chili (কাঁচা মরিচ)',
        'Dry Chili'        => 'Dry Chili (শুকনো মরিচ)',
        'Turmeric Powder'  => 'Turmeric Powder (হলুদ গুঁড়া)',
        'Coriander Powder' => 'Coriander Powder (ধনিয়া গুঁড়া)',
        'Cumin Powder'     => 'Cumin Powder (জিরা গুঁড়া)',
        'Red Chili Powder' => 'Red Chili Powder (লাল মরিচ গুঁড়া)',
        'Garam Masala'     => 'Garam Masala (গরম মশলা)',
        'Bay Leaf'         => 'Bay Leaf (তেজপাতা)',
    ],
    'Essentials (প্রয়োজনীয়)' => [
        'Sugar'              => 'Sugar (চিনি)',
        'Salt'               => 'Salt (লবণ)',
        'Flour / Atta'       => 'Flour / Atta (আটা)',
        'Maida'              => 'Maida (ময়দা)',
        'Semolina / Suji'    => 'Semolina / Suji (সুজি)',
        'Vermicelli / Semai' => 'Vermicelli / Semai (সেমাই)',
        'Noodles'            => 'Noodles (নুডলস)',
        'Biscuit'            => 'Biscuit (বিস্কুট)',
        'Bread'              => 'Bread (পাউরুটি)',
    ],
    'Vegetables (সবজি)' => [
        'Potato'               => 'Potato (আলু)',
        'Tomato'               => 'Tomato (টমেটো)',
        'Eggplant / Brinjal'   => 'Eggplant / Brinjal (বেগুন)',
        'Cauliflower'          => 'Cauliflower (ফুলকপি)',
        'Cabbage'              => 'Cabbage (বাঁধাকপি)',
        'Pumpkin'              => 'Pumpkin (মিষ্টি কুমড়া)',
        'Bitter Gourd'         => 'Bitter Gourd (করলা)',
        'Bottle Gourd'         => 'Bottle Gourd (লাউ)',
        'Lady Finger / Okra'   => 'Lady Finger / Okra (ঢেঁড়স)',
        'Spinach'              => 'Spinach (পালং শাক)',
        'Red Spinach'          => 'Red Spinach (লাল শাক)',
        'Banana'               => 'Banana (কলা)',
        'Coconut'              => 'Coconut (নারকেল)',
    ],
    'Protein (প্রোটিন)' => [
        'Egg'                  => 'Egg (ডিম)',
        'Chicken'              => 'Chicken (মুরগি)',
        'Beef'                 => 'Beef (গরুর মাংস)',
        'Mutton'               => 'Mutton (খাসির মাংস)',
        'Hilsha Fish'          => 'Hilsha Fish (ইলিশ মাছ)',
        'Rui Fish'             => 'Rui Fish (রুই মাছ)',
        'Catla Fish'           => 'Catla Fish (কাতলা মাছ)',
        'Pangash Fish'         => 'Pangash Fish (পাঙ্গাশ মাছ)',
        'Tilapia Fish'         => 'Tilapia Fish (তেলাপিয়া মাছ)',
        'Dried Fish / Shutki'  => 'Dried Fish / Shutki (শুঁটকি)',
    ],
    'Dairy (দুগ্ধজাত)' => [
        'Milk'   => 'Milk (দুধ)',
        'Ghee'   => 'Ghee (ঘি)',
        'Butter' => 'Butter (মাখন)',
    ],
    'Beverages (পানীয়)' => [
        'Tea'    => 'Tea (চা পাতা)',
        'Coffee' => 'Coffee (কফি)',
    ],
    'Household (গৃহস্থালি)' => [
        'Soap'              => 'Soap (সাবান)',
        'Detergent Powder'  => 'Detergent Powder (ডিটারজেন্ট পাউডার)',
        'Shampoo'           => 'Shampoo (শ্যাম্পু)',
        'Toothpaste'        => 'Toothpaste (টুথপেস্ট)',
        'Toilet Cleaner'    => 'Toilet Cleaner (টয়লেট ক্লিনার)',
    ],
    'Fuel / Others (জ্বালানি)' => [
        'Gas Cylinder' => 'Gas Cylinder (গ্যাস সিলিন্ডার)',
        'Kerosene'     => 'Kerosene (কেরোসিন)',
        'Match Box'    => 'Match Box (দিয়াশলাই)',
    ],
];

// Bangladesh grocery company list
$company_groups = [
    'Food & Beverage (খাদ্য ও পানীয়)' => [
        'PRAN'                  => 'PRAN (প্রাণ)',
        'ACI Consumer Brands'   => 'ACI Consumer Brands (এসিআই)',
        'Square Food & Beverage'=> 'Square Food & Beverage (স্কয়ার)',
        'Akij Food & Beverage'  => 'Akij Food & Beverage (আকিজ)',
        'Bombay Sweets'         => 'Bombay Sweets (বম্বে সুইটস)',
        'Danish Foods'          => 'Danish Foods (ড্যানিশ)',
        'Fresh (Meghna Group)'  => 'Fresh / Meghna Group (ফ্রেশ)',
        'Bashundhara Food'      => 'Bashundhara Food (বসুন্ধরা)',
        'Olympic Industries'    => 'Olympic Industries (অলিম্পিক)',
        'Abul Khair Group'      => 'Abul Khair Group (আবুল খায়ের)',
        'TK Food'               => 'TK Food (টিকে ফুড)',
        'Cocola Food'           => 'Cocola Food (কোকোলা)',
        'Bengal Meat'           => 'Bengal Meat (বেঙ্গল মিট)',
        'Kazi Farms'            => 'Kazi Farms (কাজী ফার্মস)',
        'CP Bangladesh'         => 'CP Bangladesh (সিপি বাংলাদেশ)',
        'Nestle Bangladesh'     => 'Nestle Bangladesh (নেসলে)',
    ],
    'Dairy (দুগ্ধজাত)' => [
        'Aarong Dairy'  => 'Aarong Dairy / BRAC (আড়ং ডেইরি)',
        'Milk Vita'     => 'Milk Vita (মিল্ক ভিটা)',
        'Arla / Dano'   => 'Arla / Dano (আরলা/ডানো)',
        'Farm Fresh'    => 'Farm Fresh (ফার্ম ফ্রেশ)',
        'Igloo'         => 'Igloo / Abdul Monem (ইগলু)',
    ],
    'Oil & Spice (তেল ও মশলা)' => [
        'Rupchanda (City Group)' => 'Rupchanda / City Group (রূপচাঁদা)',
        'Teer (City Group)'      => 'Teer / City Group (তীর)',
        'Radhuni (PRAN)'         => 'Radhuni / PRAN (রাঁধুনি)',
        'Meizan'                 => 'Meizan (মেইজান)',
        'Sunflower'              => 'Sunflower (সানফ্লাওয়ার)',
    ],
    'Household & Personal Care (গৃহস্থালি)' => [
        'Unilever Bangladesh'    => 'Unilever Bangladesh (ইউনিলিভার)',
        'Keya Cosmetics'         => 'Keya Cosmetics (কেয়া)',
        'Square Toiletries'      => 'Square Toiletries (স্কয়ার)',
        'RFL Group'              => 'RFL Group (আরএফএল)',
    ],
    'Other (অন্যান্য)' => [
        'TCB (Government)'  => 'TCB / Govt. (সরকারি টিসিবি)',
        'Local Market'      => 'Local Market (স্থানীয় বাজার)',
        'Imported'          => 'Imported (আমদানিকৃত)',
        'Other'             => 'Other (অন্যান্য)',
    ],
];

// Flat product array for JS (enables rebuild-on-change deduplication)
$productData = [];
foreach ($product_groups as $group => $items) {
    foreach ($items as $val => $label) {
        $productData[] = ['value' => $val, 'label' => $label, 'group' => $group];
    }
}

// Static HTML for company & unit (no deduplication needed)
$companyOptHtml = '<option value="">-- কোম্পানী নির্বাচন করুন --</option>';
foreach ($company_groups as $group => $items) {
    $companyOptHtml .= '<optgroup label="' . htmlspecialchars($group) . '">';
    foreach ($items as $val => $label) {
        $companyOptHtml .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . '</option>';
    }
    $companyOptHtml .= '</optgroup>';
}

$unitOptHtml = '<option value="">একক</option>';
foreach ($units as $val => $label) {
    $unitOptHtml .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . '</option>';
}

// Product opt HTML for edit modal (static, no deduplication)
$productOptHtml = '<option value="">-- পণ্য নির্বাচন করুন --</option>';
foreach ($product_groups as $group => $items) {
    $productOptHtml .= '<optgroup label="' . htmlspecialchars($group) . '">';
    foreach ($items as $val => $label) {
        $productOptHtml .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($label) . '</option>';
    }
    $productOptHtml .= '</optgroup>';
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
                    <?php echo htmlspecialchars($success_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo htmlspecialchars($error_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="../process/monthly_bazar_process.php" method="post" id="bazarForm" autocomplete="off">
                <input type="hidden" name="action" value="insert_bulk">
                <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member_id); ?>">

                <!-- Month row -->
                <div class="row mb-3 align-items-center">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">
                            Month <span class="text-secondary small">(মাস)</span>
                        </label>
                        <input type="text"
                               class="form-control fw-bold text-primary"
                               value="<?php echo htmlspecialchars($current_month_label); ?>"
                               readonly
                               style="background:#eef4ff;cursor:not-allowed;">
                        <input type="hidden" name="month" value="<?php echo htmlspecialchars($current_month); ?>">
                    </div>
                    <div class="col-12 col-md-9 d-flex align-items-end justify-content-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-success px-4" onclick="addRow()">
                            <i class="bi bi-plus-circle me-1"></i> Add Row (রো যুক্ত করুন)
                        </button>
                    </div>
                </div>

                <!-- Dynamic rows table -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="inputTable" style="min-width:900px;">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:42px">#</th>
                                <th style="min-width:210px">
                                    Product Name <span class="fw-normal small">(পণ্যের নাম)</span>
                                </th>
                                <th style="min-width:180px">
                                    Quantity <span class="fw-normal small">(পরিমাণ)</span>
                                </th>
                                <th style="min-width:200px">
                                    Company <span class="fw-normal small">(কোম্পানি)</span>
                                </th>
                                <th style="min-width:140px">
                                    Remarks <span class="fw-normal small">(মন্তব্য)</span>
                                </th>
                                <th style="width:50px" class="text-center">
                                    <i class="bi bi-trash text-danger"></i>
                                </th>
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
                            <th>Month (মাস)</th>
                            <th>Product Name (পণ্যের নাম)</th>
                            <th>Quantity (পরিমাণ)</th>
                            <th>Company (কোম্পানি)</th>
                            <th>Remarks (মন্তব্য)</th>
                            <th class="text-center">Action (কার্যকলাপ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bazars)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No records found. ( কোনো তথ্য পাওয়া যায়নি। )
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bazars as $i => $row): ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($row['month']); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td class="text-center text-nowrap">
                                        <button class="btn btn-sm btn-warning me-1"
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="../process/monthly_bazar_process.php" method="post"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this record?\n(এই তথ্যটি মুছে ফেলবেন?)')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
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
                    <h5 class="modal-title">
                        Edit Record <span class="fw-normal small">( তথ্য সম্পাদনা )</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Month <span class="text-secondary small">(মাস)</span></label>
                            <select class="form-select" name="month" id="edit_month" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($months as $val => $label): ?>
                                    <option value="<?php echo $val; ?>"><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product Name <span class="text-secondary small">(পণ্যের নাম)</span></label>
                            <select class="form-select" name="product_name" id="edit_product_name" required>
                                <?php echo $productOptHtml; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-secondary small">(পরিমাণ)</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="edit_qty_amount"
                                       id="edit_qty_amount" min="0" step="any" placeholder="Amount" required>
                                <select class="form-select" name="edit_qty_unit" id="edit_qty_unit" style="max-width:100px;" required>
                                    <?php echo $unitOptHtml; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company <span class="text-secondary small">(কোম্পানি)</span></label>
                            <select class="form-select" name="company" id="edit_company" required>
                                <?php echo $companyOptHtml; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarks <span class="text-secondary small">(মন্তব্য)</span></label>
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
// Product list as structured JSON — used to REBUILD options dynamically
const PRODUCTS = <?php echo json_encode($productData, JSON_UNESCAPED_UNICODE); ?>;

// Static HTML strings for company & unit (no deduplication needed)
const COMPANY_HTML = <?php echo json_encode($companyOptHtml); ?>;
const UNIT_HTML    = <?php echo json_encode($unitOptHtml); ?>;

let rowCounter = 0;

// Escape HTML for option text/values
function esc(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Build product <option> HTML, EXCLUDING values in excludeSet
function buildProductOpts(excludeSet) {
    let html = '<option value="">-- পণ্য নির্বাচন করুন --</option>';
    let lastGroup = '';
    PRODUCTS.forEach(p => {
        if (excludeSet.has(p.value)) return; // completely skip taken products
        if (p.group !== lastGroup) {
            if (lastGroup) html += '</optgroup>';
            html += '<optgroup label="' + esc(p.group) + '">';
            lastGroup = p.group;
        }
        html += '<option value="' + esc(p.value) + '">' + esc(p.label) + '</option>';
    });
    if (lastGroup) html += '</optgroup>';
    return html;
}

// Rebuild every product dropdown: remove products already chosen by OTHER rows
function refreshProductOptions() {
    // Snapshot current value of every product select
    const current = {};
    document.querySelectorAll('.product-select').forEach(sel => {
        current[sel.dataset.rowid] = sel.value;
    });

    document.querySelectorAll('.product-select').forEach(sel => {
        const rowId  = sel.dataset.rowid;
        const ownVal = current[rowId] || '';

        // Products picked by every OTHER row
        const excludeSet = new Set(
            Object.entries(current)
                .filter(([rid, v]) => rid !== rowId && v !== '')
                .map(([, v]) => v)
        );

        // Rebuild options (taken products simply won't appear)
        sel.innerHTML = buildProductOpts(excludeSet);

        // Restore this row's own selection
        if (ownVal) sel.value = ownVal;
    });
}

function addRow() {
    rowCounter++;
    const id = 'row_' + rowCounter;
    const tr = document.createElement('tr');
    tr.id = id;
    tr.innerHTML =
        '<td class="text-center text-muted fw-bold row-num"></td>' +
        '<td>' +
            '<select class="form-select form-select-sm product-select"' +
                    ' name="product_name[]"' +
                    ' onchange="refreshProductOptions()"' +
                    ' data-rowid="' + id + '" required>' +
            '</select>' +
        '</td>' +
        '<td>' +
            '<div class="input-group input-group-sm">' +
                '<input type="number" class="form-control" name="qty_amount[]"' +
                       ' min="0" step="any" placeholder="পরিমান" required>' +
                '<select class="form-select" name="qty_unit[]"' +
                        ' style="max-width:82px;" required>' +
                    UNIT_HTML +
                '</select>' +
            '</div>' +
        '</td>' +
        '<td>' +
            '<select class="form-select form-select-sm" name="company[]" required>' +
                COMPANY_HTML +
            '</select>' +
        '</td>' +
        '<td>' +
            '<input type="text" class="form-control form-control-sm"' +
                   ' name="remarks[]" placeholder="Optional">' +
        '</td>' +
        '<td class="text-center">' +
            '<button type="button" class="btn btn-sm btn-outline-danger"' +
                    ' onclick="removeRow(\'' + id + '\')">' +
                '<i class="bi bi-x-lg"></i>' +
            '</button>' +
        '</td>';

    document.getElementById('rowContainer').appendChild(tr);
    reNumber();
    refreshProductOptions(); // apply current exclusions to new row immediately
}

function removeRow(id) {
    const row = document.getElementById(id);
    if (row) {
        row.remove();
        refreshProductOptions(); // restore freed product to all remaining rows
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
        alert('Please add at least one product row.\n(অন্তত একটি পণ্য যোগ করুন।)');
    }
});

// Edit modal opener
function openEditModal(row) {
    document.getElementById('edit_id').value           = row.id;
    document.getElementById('edit_month').value        = row.month;
    document.getElementById('edit_product_name').value = row.product_name;

    // Split stored "2.5 KG" → amount & unit
    const qtyParts = (row.quantity || '').trim().split(' ');
    document.getElementById('edit_qty_amount').value = qtyParts[0] || '';
    document.getElementById('edit_qty_unit').value   = qtyParts[1] || 'KG';

    document.getElementById('edit_company').value = row.company;
    document.getElementById('edit_remarks').value = row.remarks || '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Start with one empty row on load
addRow();
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
