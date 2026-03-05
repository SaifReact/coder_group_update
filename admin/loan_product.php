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
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label">Loan Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?= $editProduct ? htmlspecialchars($editProduct['product_name']) : 'Nano Loan' ?>" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_range" class="form-label">Start Range</label>
                            <input type="text" class="form-control" id="start_range" name="start_range" value="<?= $editProduct ? htmlspecialchars($editProduct['start_range']) : '10000' ?>" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_range" class="form-label">End Range</label>
                            <input type="text" class="form-control" id="end_range" name="end_range" value="<?= $editProduct ? htmlspecialchars($editProduct['end_range']) : '100000' ?>" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="service_charge" class="form-label">Service Charge (%)</label>
                            <input type="number" step="0.01" min="8" max="10" class="form-control" id="service_charge" name="service_charge" required value="<?= $editProduct ? htmlspecialchars($editProduct['service_charge']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tenure" class="form-label">Loan Tenure (Months)</label>
                            <input type="number" min="3" max="12" class="form-control" id="tenure" name="tenure" required value="<?= $editProduct ? htmlspecialchars($editProduct['tenure']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="installment_type" class="form-label">Installment Type</label>
                            <input type="text" class="form-control" id="installment_type" name="installment_type" value="Monthly" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="processing_fee" class="form-label">Processing Fee (%)</label>
                            <input type="number" step="0.01" min="0" max="1" class="form-control" id="processing_fee" name="processing_fee" required value="<?= $editProduct ? htmlspecialchars($editProduct['processing_fee']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="late_fee" class="form-label">Late Fee (Per Installment)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="late_fee" name="late_fee" required value="<?= $editProduct ? htmlspecialchars($editProduct['late_fee']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_loan_per_member" class="form-label">Maximum Loan Per Member</label>
                            <input type="number" min="1" max="1" class="form-control" id="max_loan_per_member" name="max_loan_per_member" value="1" readonly>
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
