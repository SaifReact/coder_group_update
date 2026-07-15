<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php'); exit;
}
include_once __DIR__ . '/../config/config.php';

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg']   ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

$products   = $pdo->query("
    SELECT p.*, c.category_name_bn, co.company_name_bn
    FROM product p
    LEFT JOIN category c  ON c.id  = p.category_id
    LEFT JOIN pcompany co ON co.id = p.company_id
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT id, category_name, category_name_bn FROM category ORDER BY category_name_bn")->fetchAll(PDO::FETCH_ASSOC);
$companies  = $pdo->query("SELECT id, company_name, company_name_bn  FROM pcompany  ORDER BY company_name_bn")->fetchAll(PDO::FETCH_ASSOC);

$forUsesLabel = ['b' => '🛒 বাজার', 'e' => '🌐 ই-কমার্স', 'be' => '🛒🌐 উভয়'];
$statusLabel  = ['A' => '<span class="badge bg-success">সক্রিয়</span>', 'I' => '<span class="badge bg-secondary">নিষ্ক্রিয়</span>'];

include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Product <span class="text-secondary">( পণ্য ব্যবস্থাপনা )</span></h3>
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

                <!-- Insert Form -->
                <form method="post" action="../process/product_process.php">
                    <input type="hidden" name="action" value="insert">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ক্যাটাগরি <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" required>
                                <option value="">-- ক্যাটাগরি বেছে নিন --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name_bn']) ?> (<?= htmlspecialchars($cat['category_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">কোম্পানি</label>
                            <select class="form-select" name="company_id">
                                <option value="0">-- কোম্পানি বেছে নিন --</option>
                                <?php foreach ($companies as $co): ?>
                                    <option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['company_name_bn']) ?> (<?= htmlspecialchars($co['company_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ব্যবহার <span class="text-danger">*</span></label>
                            <select class="form-select" name="for_uses" required>
                                <option value="b">🛒 বাজার (Bazar)</option>
                                <option value="e">🌐 ই-কমার্স (Ecommerce)</option>
                                <option value="be">🛒🌐 উভয় (Both)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">পণ্যের নাম (English) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" placeholder="e.g. Rice (Miniket)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">পণ্যের নাম (বাংলা) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name_bn" placeholder="যেমন: চাল (মিনিকেট)" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ক্রয় মূল্য (Buyer Price)</label>
                            <input type="text" inputmode="decimal" class="form-control" name="buyer_price" id="ins_buyer" value="0" oninput="calcProfit('ins')" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">বিক্রয় মূল্য (Seller Price)</label>
                            <input type="text" inputmode="decimal" class="form-control" name="seller_price" id="ins_seller" value="0" oninput="calcProfit('ins')" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">মুনাফা (Profit)</label>
                            <input type="text" class="form-control" name="profit" id="ins_profit" value="0" readonly style="background:#f8f9fa;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">স্ট্যাটাস</label>
                            <select class="form-select" name="status">
                                <option value="A">✅ সক্রিয়</option>
                                <option value="I">❌ নিষ্ক্রিয়</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-plus"></i> পণ্য যোগ করুন
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-4" />

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" style="font-size:.93rem;">
                        <thead class="table-warning">
                            <tr>
                                <th>#</th>
                                <th>পণ্যের নাম</th>
                                <th>ক্যাটাগরি</th>
                                <th>কোম্পানি</th>
                                <th class="text-end">ক্রয় মূল্য</th>
                                <th class="text-end">বিক্রয় মূল্য</th>
                                <th class="text-end">মুনাফা</th>
                                <th class="text-center">ব্যবহার</th>
                                <th class="text-center">স্ট্যাটাস</th>
                                <th class="text-center" width="10%">কর্মকান্ড</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($products): ?>
                            <?php foreach ($products as $i => $p): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($p['product_name_bn']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($p['product_name']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($p['category_name_bn'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($p['company_name_bn']  ?? '—') ?></td>
                                <td class="text-end">৳<?= number_format($p['buyer_price'],  2) ?></td>
                                <td class="text-end">৳<?= number_format($p['seller_price'], 2) ?></td>
                                <td class="text-end <?= $p['profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    ৳<?= number_format($p['profit'], 2) ?>
                                </td>
                                <td class="text-center"><?= $forUsesLabel[$p['for_uses']] ?? $p['for_uses'] ?></td>
                                <td class="text-center"><?= $statusLabel[$p['status']] ?? $p['status'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-info btn-sm" onclick="editProduct(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form method="post" action="../process/product_process.php" style="display:inline-block;"
                                          onsubmit="return confirm('পণ্যটি মুছে ফেলবেন?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10" class="text-center text-muted">কোনো পণ্য নেই।</td></tr>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="../process/product_process.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="e_id">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">পণ্য সম্পাদনা</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ক্যাটাগরি</label>
                            <select class="form-select" name="category_id" id="e_category_id">
                                <option value="0">-- বেছে নিন --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name_bn']) ?> (<?= htmlspecialchars($cat['category_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">কোম্পানি</label>
                            <select class="form-select" name="company_id" id="e_company_id">
                                <option value="0">-- বেছে নিন --</option>
                                <?php foreach ($companies as $co): ?>
                                    <option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['company_name_bn']) ?> (<?= htmlspecialchars($co['company_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ব্যবহার</label>
                            <select class="form-select" name="for_uses" id="e_for_uses">
                                <option value="b">🛒 বাজার</option>
                                <option value="e">🌐 ই-কমার্স</option>
                                <option value="be">🛒🌐 উভয়</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">পণ্যের নাম (English)</label>
                            <input type="text" class="form-control" name="product_name" id="e_product_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">পণ্যের নাম (বাংলা)</label>
                            <input type="text" class="form-control" name="product_name_bn" id="e_product_name_bn" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ক্রয় মূল্য</label>
                            <input type="text" inputmode="decimal" class="form-control" name="buyer_price" id="e_buyer" oninput="calcProfit('e')" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">বিক্রয় মূল্য</label>
                            <input type="text" inputmode="decimal" class="form-control" name="seller_price" id="e_seller" oninput="calcProfit('e')" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">মুনাফা</label>
                            <input type="text" class="form-control" name="profit" id="e_profit" readonly style="background:#f8f9fa;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">স্ট্যাটাস</label>
                            <select class="form-select" name="status" id="e_status">
                                <option value="A">✅ সক্রিয়</option>
                                <option value="I">❌ নিষ্ক্রিয়</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary">হালনাগাদ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcProfit(prefix) {
    var buyer  = parseFloat(document.getElementById(prefix + '_buyer').value)  || 0;
    var seller = parseFloat(document.getElementById(prefix + '_seller').value) || 0;
    document.getElementById(prefix + '_profit').value = (seller - buyer).toFixed(2);
}

function editProduct(p) {
    document.getElementById('e_id').value             = p.id;
    document.getElementById('e_category_id').value    = p.category_id;
    document.getElementById('e_company_id').value     = p.company_id;
    document.getElementById('e_product_name').value   = p.product_name;
    document.getElementById('e_product_name_bn').value= p.product_name_bn;
    document.getElementById('e_buyer').value          = p.buyer_price;
    document.getElementById('e_seller').value         = p.seller_price;
    document.getElementById('e_profit').value         = p.profit;
    document.getElementById('e_status').value         = p.status;
    document.getElementById('e_for_uses').value       = p.for_uses;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
