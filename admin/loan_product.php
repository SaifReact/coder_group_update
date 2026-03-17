<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

// Fetch all loan products
// Edit mode
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM loan_products WHERE id = ?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}
$stmt = $pdo->query("SELECT * FROM loan_products ORDER BY id ASC");
$products = $stmt->fetchAll();
?>
<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Loan Product <span class="text-secondary">( ঋণ প্রোডাক্ট )</span></h3>
                <hr class="mb-4" />
                <!-- Add/Edit Form -->
                <form action="../process/loan_product_process.php" method="post">
                    <div class="row">
                        <?php if ($editProduct): ?>
                            <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                        <?php endif; ?>
                        <!-- Product Type Dropdown -->
                                                <!-- Loan Start Date -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">শুরুর তারিখ <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="loan_start_date" required value="<?= $editProduct ? htmlspecialchars($editProduct['loan_start_date']) : '' ?>">
                                                </div>
                                                <!-- Loan End Date -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">শেষ তারিখ <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="loan_end_date" required value="<?= $editProduct ? htmlspecialchars($editProduct['loan_end_date']) : '' ?>">
                                                </div>
                                                <!-- Loan Purpose -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">ঋণের উদ্দেশ্য <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="loan_purpose" required value="<?= $editProduct ? htmlspecialchars($editProduct['loan_purpose']) : '' ?>">
                                                </div>
                                                <!-- Loan Amount -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">ঋণের পরিমাণ <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="loan_amount" required value="<?= $editProduct ? htmlspecialchars($editProduct['loan_amount']) : '' ?>">
                                                </div>
                                                <!-- Installment Amount -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">কিস্তির পরিমাণ <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="installment_amount" required value="<?= $editProduct ? htmlspecialchars($editProduct['installment_amount']) : '' ?>">
                                                </div>
                                                <!-- Interest Rate -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">সুদের হার (%) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control" name="interest_rate" required value="<?= $editProduct ? htmlspecialchars($editProduct['interest_rate']) : '' ?>">
                                                </div>
                                                <!-- Grace Period -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">গ্রেস পিরিয়ড (মাস) <span class="text-danger">*</span></label>
                                                    <input type="number" min="0" class="form-control" name="grace_period" required value="<?= $editProduct ? htmlspecialchars($editProduct['grace_period']) : '' ?>">
                                                </div>
                                                <!-- Insurance -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">ইনস্যুরেন্স <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="insurance" required>
                                                        <option value="">নির্বাচন করুন</option>
                                                        <option value="Yes" <?= ($editProduct && $editProduct['insurance'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                                        <option value="No" <?= ($editProduct && $editProduct['insurance'] == 'No') ? 'selected' : '' ?>>No</option>
                                                    </select>
                                                </div>
                                                <!-- Check Disbursement -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">চেক বিতরণ <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="check_disbursement" required>
                                                        <option value="">নির্বাচন করুন</option>
                                                        <option value="Yes" <?= ($editProduct && $editProduct['check_disbursement'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                                        <option value="No" <?= ($editProduct && $editProduct['check_disbursement'] == 'No') ? 'selected' : '' ?>>No</option>
                                                    </select>
                                                </div>
                                                <!-- User Group -->
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">ইউজার গ্রুপ <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="user_group" required>
                                                        <option value="">নির্বাচন করুন</option>
                                                        <option value="A" <?= ($editProduct && $editProduct['user_group'] == 'A') ? 'selected' : '' ?>>A</option>
                                                        <option value="B" <?= ($editProduct && $editProduct['user_group'] == 'B') ? 'selected' : '' ?>>B</option>
                                                        <option value="C" <?= ($editProduct && $editProduct['user_group'] == 'C') ? 'selected' : '' ?>>C</option>
                                                    </select>
                                                </div>
                                                <!-- Remarks/Notes -->
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">মন্তব্য / নোট</label>
                                                    <textarea class="form-control" name="remarks" rows="2"><?= $editProduct ? htmlspecialchars($editProduct['remarks']) : '' ?></textarea>
                                                </div>
                                                <!-- File Upload -->
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">ডকুমেন্ট আপলোড</label>
                                                    <input type="file" class="form-control" name="document_file">
                                                </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">প্রোডাক্ট টাইপ <span class="text-danger">*</span></label>
                            <select class="form-select" name="product_type" required>
                                <option value="">নির্বাচন করুন</option>
                                <option value="Nano" <?= ($editProduct && $editProduct['product_type'] == 'Nano') ? 'selected' : '' ?>>Nano</option>
                                <option value="Micro" <?= ($editProduct && $editProduct['product_type'] == 'Micro') ? 'selected' : '' ?>>Micro</option>
                                <option value="SME" <?= ($editProduct && $editProduct['product_type'] == 'SME') ? 'selected' : '' ?>>SME</option>
                            </select>
                        </div>
                        <!-- Product Code -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">প্রোডাক্ট কোড <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_code" required value="<?= $editProduct ? htmlspecialchars($editProduct['product_code']) : '' ?>">
                        </div>
                        <!-- Product Name -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">প্রোডাক্ট নাম <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" required value="<?= $editProduct ? htmlspecialchars($editProduct['product_name']) : '' ?>">
                        </div>
                        <!-- Start Range -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">শুরুর পরিমাণ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="start_range" required value="<?= $editProduct ? htmlspecialchars($editProduct['start_range']) : '' ?>">
                        </div>
                        <!-- End Range -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">শেষ পরিমাণ <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="end_range" required value="<?= $editProduct ? htmlspecialchars($editProduct['end_range']) : '' ?>">
                        </div>
                        <!-- Service Charge -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">সার্ভিস চার্জ (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="8" max="10" class="form-control" name="service_charge" required value="<?= $editProduct ? htmlspecialchars($editProduct['service_charge']) : '' ?>">
                        </div>
                        <!-- Tenure -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">মেয়াদ (মাস) <span class="text-danger">*</span></label>
                            <input type="number" min="3" max="12" class="form-control" name="tenure" required value="<?= $editProduct ? htmlspecialchars($editProduct['tenure']) : '' ?>">
                        </div>
                        <!-- Installment Type Dropdown -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">কিস্তির ধরন <span class="text-danger">*</span></label>
                            <select class="form-select" name="installment_type" required>
                                <option value="">নির্বাচন করুন</option>
                                <option value="Monthly" <?= ($editProduct && $editProduct['installment_type'] == 'Monthly') ? 'selected' : '' ?>>Monthly</option>
                                <option value="Weekly" <?= ($editProduct && $editProduct['installment_type'] == 'Weekly') ? 'selected' : '' ?>>Weekly</option>
                            </select>
                        </div>
                        <!-- Processing Fee -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">প্রসেসিং ফি (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="1" class="form-control" name="processing_fee" required value="<?= $editProduct ? htmlspecialchars($editProduct['processing_fee']) : '' ?>">
                        </div>
                        <!-- Late Fee -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">বিলম্ব ফি (প্রতি কিস্তি) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="late_fee" required value="<?= $editProduct ? htmlspecialchars($editProduct['late_fee']) : '' ?>">
                        </div>
                        <!-- Max Loan Per Member -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">প্রতি সদস্যে সর্বোচ্চ ঋণ <span class="text-danger">*</span></label>
                            <input type="number" min="1" max="1" class="form-control" name="max_loan_per_member" value="1" readonly>
                        </div>
                        <!-- Info Boxes (as in screenshot) -->
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">❓ <b>অতিরিক্ত পেমেন্ট গ্রহণযোগ্য?</b></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">নির্বাচন করুন</label>
                            <select class="form-select" name="allow_extra_payment">
                                <option value="">নির্বাচন করুন</option>
                                <option value="Yes" <?= ($editProduct && $editProduct['allow_extra_payment'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="No" <?= ($editProduct && $editProduct['allow_extra_payment'] == 'No') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">❓ <b>একাধিক ঋণ বিতরণ গ্রহণযোগ্য?</b></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">নির্বাচন করুন</label>
                            <select class="form-select" name="allow_multiple_disbursement">
                                <option value="">নির্বাচন করুন</option>
                                <option value="Yes" <?= ($editProduct && $editProduct['allow_multiple_disbursement'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="No" <?= ($editProduct && $editProduct['allow_multiple_disbursement'] == 'No') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" name="action" value="<?= $editProduct ? 'edit' : 'insert' ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                                <?= $editProduct ? 'Update Product' : 'Save Product' ?>
                            </button>
                        </div>
                    </div>
                </form>
                <hr class="my-4" />
                <!-- Product List Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Amount Range</th>
                                <th>Service Charge (%)</th>
                                <th>Tenure (Months)</th>
                                <th>Processing Fee (%)</th>
                                <th>Late Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $i => $product): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                <td><?= htmlspecialchars($product['start_range']) ?> - <?= htmlspecialchars($product['end_range']) ?></td>
                                <td><?= htmlspecialchars($product['service_charge']) ?></td>
                                <td><?= htmlspecialchars($product['tenure']) ?></td>
                                <td><?= htmlspecialchars($product['processing_fee']) ?></td>
                                <td><?= htmlspecialchars($product['late_fee']) ?></td>
                                <td>
                                    <a href="loan_product.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="../process/loan_product_process.php?delete=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
