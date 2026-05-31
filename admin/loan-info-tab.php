<?php
// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_info_delete'])) {
    $id = intval($_POST['loan_info_id'] ?? 0);
    
    if ($id > 0) {
        try {
            $deleteSql = "DELETE FROM loan_info WHERE id = ?";
            $stmt = $pdo->prepare($deleteSql);
            $stmt->execute([$id]);
            $successMessage = 'Loan info deleted successfully.';
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_info_update'])) {
    $id = intval($_POST['loan_info_id'] ?? 0);
    $product_type = trim($_POST['product_type'] ?? '');
    $product_code = trim($_POST['product_code'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $savings_percentage = $_POST['savings_percentage'] !== '' ? floatval($_POST['savings_percentage']) : null;
    $share_percentage = $_POST['share_percentage'] !== '' ? floatval($_POST['share_percentage']) : null;
    $max_loan_amount = trim($_POST['max_loan_amount'] ?? '');
    $min_loan_amount = trim($_POST['min_loan_amount'] ?? '');
    $loan_term = trim($_POST['loan_term'] ?? '');
    $installment_frequency = trim($_POST['installment_frequency'] ?? '');
    $service_charge_calculation_method = trim($_POST['service_charge_calculation_method'] ?? '');
    $installment_measurement_method = trim($_POST['installment_measurement_method'] ?? '');
    $product_gl = trim($_POST['product_gl'] ?? '');
    $capital_gl = trim($_POST['capital_gl'] ?? '');
    $service_charge_gl = trim($_POST['service_charge_gl'] ?? '');
    $capital_recovery_order = trim($_POST['capital_recovery_order'] ?? '');
    $service_charge_recovery_order = trim($_POST['service_charge_recovery_order'] ?? '');
    $late_charge_recovery_order = trim($_POST['late_charge_recovery_order'] ?? '');
    $has_check_disbursement = trim($_POST['has_check_disbursement'] ?? '');

    $errors = [];
    if ($id <= 0) {
        $errors[] = 'Invalid loan info ID.';
    }
    if ($product_type === '') {
        $errors[] = 'Product type is required.';
    }
    if ($product_code === '') {
        $errors[] = 'Product code is required.';
    }
    if ($product_name === '') {
        $errors[] = 'Product name is required.';
    }
    if ($start_date === '') {
        $errors[] = 'Start date is required.';
    }
    if ($max_loan_amount === '') {
        $errors[] = 'Max loan amount is required.';
    }
    if ($min_loan_amount === '') {
        $errors[] = 'Min loan amount is required.';
    }
    if ($loan_term === '') {
        $errors[] = 'Loan term is required.';
    }

    if (empty($errors)) {
        try {
            $updateSql = "UPDATE loan_info SET 
                product_type = ?,
                product_code = ?,
                product_name = ?,
                start_date = ?,
                savings_percentage = ?,
                share_percentage = ?,
                max_loan_amount = ?,
                min_loan_amount = ?,
                loan_term = ?,
                installment_frequency = ?,
                service_charge_calculation_method = ?,
                installment_measurement_method = ?,
                product_gl = ?,
                capital_gl = ?,
                service_charge_gl = ?,
                capital_recovery_order = ?,
                service_charge_recovery_order = ?,
                late_charge_recovery_order = ?,
                has_check_disbursement = ?
                WHERE id = ?";
            
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([
                $product_type,
                $product_code,
                $product_name,
                $start_date,
                $savings_percentage,
                $share_percentage,
                floatval($max_loan_amount),
                floatval($min_loan_amount),
                intval($loan_term),
                $installment_frequency,
                $service_charge_calculation_method,
                $installment_measurement_method,
                $product_gl,
                $capital_gl,
                $service_charge_gl,
                $capital_recovery_order,
                $service_charge_recovery_order,
                $late_charge_recovery_order,
                $has_check_disbursement,
                $id
            ]);

            $successMessage = 'Loan info updated successfully.';
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get loan info for editing if edit_id is present
$editLoanInfo = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM loan_info WHERE id = ?");
        $stmt->execute([$edit_id]);
        $editLoanInfo = $stmt->fetch();
    } catch (PDOException $e) {
        // Ignore if edit_id is invalid
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_info_insert'])) {
    $product_type = trim($_POST['product_type'] ?? '');
    $product_code = trim($_POST['product_code'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $savings_percentage = $_POST['savings_percentage'] !== '' ? floatval($_POST['savings_percentage']) : null;
    $share_percentage = $_POST['share_percentage'] !== '' ? floatval($_POST['share_percentage']) : null;
    $max_loan_amount = trim($_POST['max_loan_amount'] ?? '');
    $min_loan_amount = trim($_POST['min_loan_amount'] ?? '');
    $loan_term = trim($_POST['loan_term'] ?? '');
    $installment_frequency = trim($_POST['installment_frequency'] ?? '');
    $service_charge_calculation_method = trim($_POST['service_charge_calculation_method'] ?? '');
    $installment_measurement_method = trim($_POST['installment_measurement_method'] ?? '');
    $product_gl = trim($_POST['product_gl'] ?? '');
    $capital_gl = trim($_POST['capital_gl'] ?? '');
    $service_charge_gl = trim($_POST['service_charge_gl'] ?? '');
    $capital_recovery_order = trim($_POST['capital_recovery_order'] ?? '');
    $service_charge_recovery_order = trim($_POST['service_charge_recovery_order'] ?? '');
    $late_charge_recovery_order = trim($_POST['late_charge_recovery_order'] ?? '');
    $has_check_disbursement = trim($_POST['has_check_disbursement'] ?? '');

    $errors = [];
    if ($product_type === '') {
        $errors[] = 'Product type is required.';
    }
    if ($product_code === '') {
        $errors[] = 'Product code is required.';
    }
    if ($product_name === '') {
        $errors[] = 'Product name is required.';
    }
    if ($start_date === '') {
        $errors[] = 'Start date is required.';
    }
    if ($max_loan_amount === '') {
        $errors[] = 'Max loan amount is required.';
    }
    if ($min_loan_amount === '') {
        $errors[] = 'Min loan amount is required.';
    }
    if ($loan_term === '') {
        $errors[] = 'Loan term is required.';
    }

    if (empty($errors)) {
        try {
            $insertSql = "INSERT INTO loan_info (
                product_type,
                product_code,
                product_name,
                start_date,
                savings_percentage,
                share_percentage,
                max_loan_amount,
                min_loan_amount,
                loan_term,
                installment_frequency,
                service_charge_calculation_method,
                installment_measurement_method,
                product_gl,
                capital_gl,
                service_charge_gl,
                capital_recovery_order,
                service_charge_recovery_order,
                late_charge_recovery_order,
                has_check_disbursement,
                created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([
                $product_type,
                $product_code,
                $product_name,
                $start_date,
                $savings_percentage,
                $share_percentage,
                floatval($max_loan_amount),
                floatval($min_loan_amount),
                intval($loan_term),
                $installment_frequency,
                $service_charge_calculation_method,
                $installment_measurement_method,
                $product_gl,
                $capital_gl,
                $service_charge_gl,
                $capital_recovery_order,
                $service_charge_recovery_order,
                $late_charge_recovery_order,
                $has_check_disbursement,
                $_SESSION['user_id'] ?? null,
            ]);

            $successMessage = 'Loan info saved successfully into loan_info table.';
            // clear submitted values after success
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

$loanInfoRows = [];
try {
    $stmt = $pdo->query("SELECT id, product_type, product_code, product_name, start_date, savings_percentage, share_percentage, max_loan_amount, min_loan_amount, loan_term, created_at FROM loan_info ORDER BY id DESC");
    $loanInfoRows = $stmt->fetchAll();
} catch (PDOException $e) {
    // Table may not exist yet, ignore on first load.
}
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php elseif (!empty($successMessage)): ?>
    <div class="alert alert-success" id="successMessage">
        <?php echo htmlspecialchars($successMessage); ?>
        <?php if (isset($_GET['edit_id'])): ?>
            <a href="<?= BASE_URL ?>admin/loan.php" class="alert-link">Back to List</a>
        <?php endif; ?>
    </div>
    <script>
        window.addEventListener('load', function () {
            var msg = document.getElementById('successMessage');
            if (!msg) return;
            setTimeout(function () {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
                setTimeout(function () {
                    if (msg.parentNode) msg.parentNode.removeChild(msg);
                }, 500);
            }, 4000);
        });
    </script>
<?php endif; ?>

<?php if ($editLoanInfo && isset($_GET['edit_id'])): ?>
    <!-- EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Edit Loan Info - <?php echo htmlspecialchars($editLoanInfo['product_name']); ?></h5>
        </div>
        <div class="card-body">
            <a href="<?= BASE_URL ?>admin/loan.php" class="btn btn-secondary btn-sm mb-3">← Back to List</a>
            <form method="post" action="">
                <input type="hidden" name="loan_info_update" value="1">
                <input type="hidden" name="loan_info_id" value="<?php echo htmlspecialchars($editLoanInfo['id']); ?>">
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">প্রোডাক্ট টাইপ <span class="text-danger">*</span></label>
                        <select class="form-select" name="product_type" required>
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="Nano" <?php echo $editLoanInfo['product_type'] === 'Nano' ? 'selected' : ''; ?>>Nano</option>
                            <option value="SME" <?php echo $editLoanInfo['product_type'] === 'SME' ? 'selected' : ''; ?>>SME</option>
                            <option value="Home" <?php echo $editLoanInfo['product_type'] === 'Home' ? 'selected' : ''; ?>>Home</option>
                            <option value="Car" <?php echo $editLoanInfo['product_type'] === 'Car' ? 'selected' : ''; ?>>Car</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">প্রোডাক্ট কোড <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="product_code" value="<?php echo htmlspecialchars($editLoanInfo['product_code']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">প্রোডাক্ট নাম <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="product_name" value="<?php echo htmlspecialchars($editLoanInfo['product_name']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">শুরুর তারিখ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($editLoanInfo['start_date']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">সঞ্চয়ের শতকরা হার(%)</label>
                        <input type="number" step="0.01" class="form-control" name="savings_percentage" value="<?php echo htmlspecialchars($editLoanInfo['savings_percentage'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">শেয়ার এর শতকরা হার(%)</label>
                        <input type="number" step="0.01" class="form-control" name="share_percentage" value="<?php echo htmlspecialchars($editLoanInfo['share_percentage'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">সর্বোচ্চ ঋণের শতকরা হার(%) / টাকার পরিমান <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="max_loan_amount" value="<?php echo htmlspecialchars($editLoanInfo['max_loan_amount']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">সর্বনিম্ন ঋণের শতকরা হার(%) / টাকার পরিমান <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="min_loan_amount" value="<?php echo htmlspecialchars($editLoanInfo['min_loan_amount']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ঋণের সর্বোচ্চ মেয়াদ (মাস) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="loan_term" value="<?php echo htmlspecialchars($editLoanInfo['loan_term']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">কিস্তি আদায়ের ফ্রিকোয়েন্সি <span class="text-danger">*</span></label>
                        <select class="form-select" name="installment_frequency">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="M" <?php echo $editLoanInfo['installment_frequency'] === 'M' ? 'selected' : ''; ?>>Monthly (মাসিক)</option>
                            <option value="Q" <?php echo $editLoanInfo['installment_frequency'] === 'Q' ? 'selected' : ''; ?>>Quarterly (ত্রৈমাসিক)</option>
                            <option value="H" <?php echo $editLoanInfo['installment_frequency'] === 'H' ? 'selected' : ''; ?>>Half-Yearly (আধা বছর)</option>
                            <option value="Y" <?php echo $editLoanInfo['installment_frequency'] === 'Y' ? 'selected' : ''; ?>>Yearly (বার্ষিক)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">সার্ভিস চার্জ ক্যালকুলেশন পদ্ধতি <span class="text-danger">*</span></label>
                        <select class="form-select" name="service_charge_calculation_method">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="F" <?php echo $editLoanInfo['service_charge_calculation_method'] === 'F' ? 'selected' : ''; ?>>Flat (ফ্লাট)</option>
                            <option value="D" <?php echo $editLoanInfo['service_charge_calculation_method'] === 'D' ? 'selected' : ''; ?>>Declining (ডিক্লাইনিং)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">কিস্তির পরিমানের পদ্ধতি</label>
                        <select class="form-select" name="installment_measurement_method">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="SCDF" <?php echo $editLoanInfo['installment_measurement_method'] === 'SCDF' ? 'selected' : ''; ?>>Service Charge Deducted First</option>
                            <option value="SCAWL" <?php echo $editLoanInfo['installment_measurement_method'] === 'SCAWL' ? 'selected' : ''; ?>>Service Charge Added With Loan</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">প্রোডাক্ট জি.এল <span class="text-danger">*</span></label>
                        <select class="form-select" name="product_gl">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="GL1" <?php echo $editLoanInfo['product_gl'] === 'GL1' ? 'selected' : ''; ?>>GL1</option>
                            <option value="GL2" <?php echo $editLoanInfo['product_gl'] === 'GL2' ? 'selected' : ''; ?>>GL2</option>
                            <option value="GL3" <?php echo $editLoanInfo['product_gl'] === 'GL3' ? 'selected' : ''; ?>>GL3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">মূলধন/আসল জি.এল <span class="text-danger">*</span></label>
                        <select class="form-select" name="capital_gl">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="GL1" <?php echo $editLoanInfo['capital_gl'] === 'GL1' ? 'selected' : ''; ?>>GL1</option>
                            <option value="GL2" <?php echo $editLoanInfo['capital_gl'] === 'GL2' ? 'selected' : ''; ?>>GL2</option>
                            <option value="GL3" <?php echo $editLoanInfo['capital_gl'] === 'GL3' ? 'selected' : ''; ?>>GL3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">সার্ভিস চার্জ জি.এল <span class="text-danger">*</span></label>
                        <select class="form-select" name="service_charge_gl">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="GL1" <?php echo $editLoanInfo['service_charge_gl'] === 'GL1' ? 'selected' : ''; ?>>GL1</option>
                            <option value="GL2" <?php echo $editLoanInfo['service_charge_gl'] === 'GL2' ? 'selected' : ''; ?>>GL2</option>
                            <option value="GL3" <?php echo $editLoanInfo['service_charge_gl'] === 'GL3' ? 'selected' : ''; ?>>GL3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">আদায়ের ক্রমানুসারে মূলধন/আসল<span class="text-danger">*</span></label>
                        <select class="form-select" name="capital_recovery_order">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="GL1" <?php echo $editLoanInfo['capital_recovery_order'] === 'GL1' ? 'selected' : ''; ?>>GL1</option>
                            <option value="GL2" <?php echo $editLoanInfo['capital_recovery_order'] === 'GL2' ? 'selected' : ''; ?>>GL2</option>
                            <option value="GL3" <?php echo $editLoanInfo['capital_recovery_order'] === 'GL3' ? 'selected' : ''; ?>>GL3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">আদায়ের ক্রমানুসারে সার্ভিস চার্জ<span class="text-danger">*</span></label>
                        <select class="form-select" name="service_charge_recovery_order">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="GL1" <?php echo $editLoanInfo['service_charge_recovery_order'] === 'GL1' ? 'selected' : ''; ?>>GL1</option>
                            <option value="GL2" <?php echo $editLoanInfo['service_charge_recovery_order'] === 'GL2' ? 'selected' : ''; ?>>GL2</option>
                            <option value="GL3" <?php echo $editLoanInfo['service_charge_recovery_order'] === 'GL3' ? 'selected' : ''; ?>>GL3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">আদায়ের ক্রমানুসারে বিলম্বিত চার্জ <span class="text-danger">*</span></label>
                        <select class="form-select" name="late_charge_recovery_order">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="GL1" <?php echo $editLoanInfo['late_charge_recovery_order'] === 'GL1' ? 'selected' : ''; ?>>GL1</option>
                            <option value="GL2" <?php echo $editLoanInfo['late_charge_recovery_order'] === 'GL2' ? 'selected' : ''; ?>>GL2</option>
                            <option value="GL3" <?php echo $editLoanInfo['late_charge_recovery_order'] === 'GL3' ? 'selected' : ''; ?>>GL3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">চেক বিতরণ আছে?</label>
                        <select class="form-select" name="has_check_disbursement">
                            <option value="">- নির্বাচন করুন -</option>
                            <option value="Yes" <?php echo $editLoanInfo['has_check_disbursement'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?php echo $editLoanInfo['has_check_disbursement'] === 'No' ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm">
                            Update Loan Info (হালনাগাদ করুন)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- ADD NEW FORM -->
    <form class="mb-4" method="post" action="">
        <input type="hidden" name="loan_info_insert" value="1">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">প্রোডাক্ট টাইপ <span class="text-danger">*</span></label>
            <select class="form-select" name="product_type" required>
                <option value="">- নির্বাচন করুন -</option>
                <option value="Nano" <?php echo (isset($_POST['product_type']) && $_POST['product_type'] === 'Nano') ? 'selected' : ''; ?>>Nano</option>
                <option value="SME" <?php echo (isset($_POST['product_type']) && $_POST['product_type'] === 'SME') ? 'selected' : ''; ?>>SME</option>
                <option value="Home" <?php echo (isset($_POST['product_type']) && $_POST['product_type'] === 'Home') ? 'selected' : ''; ?>>Home</option>
                <option value="Car" <?php echo (isset($_POST['product_type']) && $_POST['product_type'] === 'Car') ? 'selected' : ''; ?>>Car</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট কোড <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="product_code" value="<?php echo htmlspecialchars($_POST['product_code'] ?? ''); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট নাম <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="product_name" value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">শুরুর তারিখ <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সঞ্চয়ের শতকরা হার(%)</label>
            <input type="number" step="0.01" class="form-control" name="savings_percentage" value="<?php echo htmlspecialchars($_POST['savings_percentage'] ?? ''); ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">শেয়ার এর শতকরা হার(%)</label>
            <input type="number" step="0.01" class="form-control" name="share_percentage" value="<?php echo htmlspecialchars($_POST['share_percentage'] ?? ''); ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সর্বোচ্চ ঋণের শতকরা হার(%) / টাকার পরিমান <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="max_loan_amount" value="<?php echo htmlspecialchars($_POST['max_loan_amount'] ?? ''); ?>" required>
        </div>
         <div class="col-md-4 mb-3">
            <label class="form-label">সর্বনিম্ন ঋণের শতকরা হার(%) / টাকার পরিমান <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="min_loan_amount" value="<?php echo htmlspecialchars($_POST['min_loan_amount'] ?? ''); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">ঋণের সর্বোচ্চ মেয়াদ (মাস) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="loan_term" value="<?php echo htmlspecialchars($_POST['loan_term'] ?? ''); ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">কিস্তি আদায়ের ফ্রিকোয়েন্সি <span class="text-danger">*</span></label>
            <select class="form-select" name="installment_frequency">
                <option value="">- নির্বাচন করুন -</option>
                <option value="M" <?php echo (isset($_POST['installment_frequency']) && $_POST['installment_frequency'] === 'M') ? 'selected' : ''; ?>>Monthly (মাসিক)</option>
                <option value="Q" <?php echo (isset($_POST['installment_frequency']) && $_POST['installment_frequency'] === 'Q') ? 'selected' : ''; ?>>Quarterly (ত্রৈমাসিক)</option>
                <option value="H" <?php echo (isset($_POST['installment_frequency']) && $_POST['installment_frequency'] === 'H') ? 'selected' : ''; ?>>Half-Yearly (আধা বছর)</option>
                <option value="Y" <?php echo (isset($_POST['installment_frequency']) && $_POST['installment_frequency'] === 'Y') ? 'selected' : ''; ?>>Yearly (বার্ষিক)</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সার্ভিস চার্জ ক্যালকুলেশন পদ্ধতি <span class="text-danger">*</span></label>
            <select class="form-select" name="service_charge_calculation_method">
                <option value="">- নির্বাচন করুন -</option>
                <option value="F" <?php echo (isset($_POST['service_charge_calculation_method']) && $_POST['service_charge_calculation_method'] === 'F') ? 'selected' : ''; ?>>Flat (ফ্লাট)</option>
                <option value="D" <?php echo (isset($_POST['service_charge_calculation_method']) && $_POST['service_charge_calculation_method'] === 'D') ? 'selected' : ''; ?>>Declining (ডিক্লাইনিং)</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">কিস্তির পরিমানের পদ্ধতি</label>
            <select class="form-select" name="installment_measurement_method">
                <option value="">- নির্বাচন করুন -</option>
                <option value="SCDF" <?php echo (isset($_POST['installment_measurement_method']) && $_POST['installment_measurement_method'] === 'SCDF') ? 'selected' : ''; ?>>Service Charge Deducted First</option>
                <option value="SCAWL" <?php echo (isset($_POST['installment_measurement_method']) && $_POST['installment_measurement_method'] === 'SCAWL') ? 'selected' : ''; ?>>Service Charge Added With Loan</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট জি.এল <span class="text-danger">*</span></label>
            <select class="form-select" name="product_gl">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1" <?php echo (isset($_POST['product_gl']) && $_POST['product_gl'] === 'GL1') ? 'selected' : ''; ?>>GL1</option>
                <option value="GL2" <?php echo (isset($_POST['product_gl']) && $_POST['product_gl'] === 'GL2') ? 'selected' : ''; ?>>GL2</option>
                <option value="GL3" <?php echo (isset($_POST['product_gl']) && $_POST['product_gl'] === 'GL3') ? 'selected' : ''; ?>>GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">মূলধন/আসল জি.এল <span class="text-danger">*</span></label>
            <select class="form-select" name="capital_gl">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1" <?php echo (isset($_POST['capital_gl']) && $_POST['capital_gl'] === 'GL1') ? 'selected' : ''; ?>>GL1</option>
                <option value="GL2" <?php echo (isset($_POST['capital_gl']) && $_POST['capital_gl'] === 'GL2') ? 'selected' : ''; ?>>GL2</option>
                <option value="GL3" <?php echo (isset($_POST['capital_gl']) && $_POST['capital_gl'] === 'GL3') ? 'selected' : ''; ?>>GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সার্ভিস চার্জ জি.এল <span class="text-danger">*</span></label>
            <select class="form-select" name="service_charge_gl">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1" <?php echo (isset($_POST['service_charge_gl']) && $_POST['service_charge_gl'] === 'GL1') ? 'selected' : ''; ?>>GL1</option>
                <option value="GL2" <?php echo (isset($_POST['service_charge_gl']) && $_POST['service_charge_gl'] === 'GL2') ? 'selected' : ''; ?>>GL2</option>
                <option value="GL3" <?php echo (isset($_POST['service_charge_gl']) && $_POST['service_charge_gl'] === 'GL3') ? 'selected' : ''; ?>>GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">আদায়ের ক্রমানুসারে মূলধন/আসল<span class="text-danger">*</span></label>
            <select class="form-select" name="capital_recovery_order">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1" <?php echo (isset($_POST['capital_recovery_order']) && $_POST['capital_recovery_order'] === 'GL1') ? 'selected' : ''; ?>>GL1</option>
                <option value="GL2" <?php echo (isset($_POST['capital_recovery_order']) && $_POST['capital_recovery_order'] === 'GL2') ? 'selected' : ''; ?>>GL2</option>
                <option value="GL3" <?php echo (isset($_POST['capital_recovery_order']) && $_POST['capital_recovery_order'] === 'GL3') ? 'selected' : ''; ?>>GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">আদায়ের ক্রমানুসারে সার্ভিস চার্জ<span class="text-danger">*</span></label>
            <select class="form-select" name="service_charge_recovery_order">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1" <?php echo (isset($_POST['service_charge_recovery_order']) && $_POST['service_charge_recovery_order'] === 'GL1') ? 'selected' : ''; ?>>GL1</option>
                <option value="GL2" <?php echo (isset($_POST['service_charge_recovery_order']) && $_POST['service_charge_recovery_order'] === 'GL2') ? 'selected' : ''; ?>>GL2</option>
                <option value="GL3" <?php echo (isset($_POST['service_charge_recovery_order']) && $_POST['service_charge_recovery_order'] === 'GL3') ? 'selected' : ''; ?>>GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">আদায়ের ক্রমানুসারে বিলম্বিত চার্জ <span class="text-danger">*</span></label>
            <select class="form-select" name="late_charge_recovery_order">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1" <?php echo (isset($_POST['late_charge_recovery_order']) && $_POST['late_charge_recovery_order'] === 'GL1') ? 'selected' : ''; ?>>GL1</option>
                <option value="GL2" <?php echo (isset($_POST['late_charge_recovery_order']) && $_POST['late_charge_recovery_order'] === 'GL2') ? 'selected' : ''; ?>>GL2</option>
                <option value="GL3" <?php echo (isset($_POST['late_charge_recovery_order']) && $_POST['late_charge_recovery_order'] === 'GL3') ? 'selected' : ''; ?>>GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">চেক বিতরণ আছে?</label>
            <select class="form-select" name="has_check_disbursement">
                <option value="">- নির্বাচন করুন -</option>
                <option value="Yes" <?php echo (isset($_POST['has_check_disbursement']) && $_POST['has_check_disbursement'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                <option value="No" <?php echo (isset($_POST['has_check_disbursement']) && $_POST['has_check_disbursement'] === 'No') ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        
        <div class="col-12 mt-4 text-end">
            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                Save Loan Info (ঋণের তথ্য সংরক্ষণ করুন)
            </button>
        </div>
    </div>
</form>
<?php endif; ?>

<?php if (!empty($loanInfoRows)): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>নং</th>
                    <th>প্রোডাক্টের কোড</th>
                    <th>প্রোডাক্টের নাম</th>
                    <th>শুরুর তারিখ</th>
                    <th>সঞ্চয় হার(%)</th>
                    <th>শেয়ার হার(%)</th>
                    <th>সর্বনিন্ম ঋণ</th>
                    <th>সর্বোচ্চ ঋণ</th>
                    <th>ঋণের মেয়াদ (মাস)</th>
                    <th>কর্মকান্ড</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loanInfoRows as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['savings_percentage']); ?></td>
                        <td><?php echo htmlspecialchars($row['share_percentage']); ?></td>
                        <td><?php echo htmlspecialchars($row['min_loan_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_loan_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['loan_term']); ?></td>
                        <td>
                            <a href="?edit_id=<?php echo intval($row['id']); ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                <input type="hidden" name="loan_info_delete" value="1">
                                <input type="hidden" name="loan_info_id" value="<?php echo intval($row['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>