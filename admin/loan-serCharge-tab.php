<?php
// Load loan products for select box
$products = [];
try {
    $stmt = $pdo->query("SELECT id, product_name FROM loan_info ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    // loan_info may not exist yet
}

$errors = [];
$successMessage = '';
$editLoanCharge = null;
$formValues = [
    'product_id' => '',
    'loan_term' => '',
    'service_charge_rate' => '',
    'late_service_charge_rate' => '',
    'expired_service_charge_rate' => '',
    'verification_charge' => '',
    'effective_date' => date('Y-m-d'),
];

if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($edit_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM loan_charge WHERE id = ? LIMIT 1");
            $stmt->execute([$edit_id]);
            $editLoanCharge = $stmt->fetch();
        } catch (PDOException $e) {
            // ignore invalid edit id
        }
    }
    if ($editLoanCharge) {
        $formValues = [
            'product_id' => $editLoanCharge['loan_info_id'] ?? '',
            'loan_term' => $editLoanCharge['loan_term'] ?? '',
            'service_charge_rate' => $editLoanCharge['service_charge_rate'] ?? '',
            'late_service_charge_rate' => $editLoanCharge['late_service_charge_rate'] ?? '',
            'expired_service_charge_rate' => $editLoanCharge['expired_service_charge_rate'] ?? '',
            'verification_charge' => $editLoanCharge['verification_charge'] ?? '',
            'effective_date' => $editLoanCharge['effective_date'] ?? date('Y-m-d'),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_charge_delete'])) {
    $id = intval($_POST['loan_charge_id'] ?? 0);
    if ($id > 0) {
        try {
            $delete = $pdo->prepare("DELETE FROM loan_charge WHERE id = ?");
            $delete->execute([$id]);
            $successMessage = 'Service charge deleted successfully.';
            $editLoanCharge = null;
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_charge_update'])) {
    $id = intval($_POST['loan_charge_id'] ?? 0);
    $formValues['product_id'] = $_POST['product_id'] ?? '';
    $formValues['loan_term'] = $_POST['loan_term'] ?? '';
    $formValues['service_charge_rate'] = $_POST['service_charge_rate'] ?? '';
    $formValues['late_service_charge_rate'] = $_POST['late_service_charge_rate'] ?? '';
    $formValues['expired_service_charge_rate'] = $_POST['expired_service_charge_rate'] ?? '';
    $formValues['verification_charge'] = $_POST['verification_charge'] ?? '';
    $formValues['effective_date'] = $_POST['effective_date'] ?? date('Y-m-d');

    if ($id <= 0) {
        $errors[] = 'Invalid service charge ID.';
    }
    if ($formValues['product_id'] === '') {
        $errors[] = 'Product is required.';
    }
    if ($formValues['loan_term'] === '') {
        $errors[] = 'Loan term is required.';
    }
    if ($formValues['service_charge_rate'] === '') {
        $errors[] = 'Service charge rate is required.';
    }
    if ($formValues['late_service_charge_rate'] === '') {
        $errors[] = 'Late service charge rate is required.';
    }
    if ($formValues['expired_service_charge_rate'] === '') {
        $errors[] = 'Expired service charge rate is required.';
    }
    if ($formValues['verification_charge'] === '') {
        $errors[] = 'Verification charge is required.';
    }

    if (empty($errors)) {
        try {
            $product_name = '';
            if ($formValues['product_id'] !== '') {
                $stmtP = $pdo->prepare("SELECT product_name FROM loan_info WHERE id = ? LIMIT 1");
                $stmtP->execute([intval($formValues['product_id'])]);
                $p = $stmtP->fetch();
                $product_name = $p['product_name'] ?? '';
            }

            $update = $pdo->prepare("UPDATE loan_charge SET loan_info_id = ?, product_name = ?, loan_term = ?, service_charge_rate = ?, late_service_charge_rate = ?, expired_service_charge_rate = ?, verification_charge = ?, effective_date = ? WHERE id = ?");
            $update->execute([
                intval($formValues['product_id']),
                $product_name,
                intval($formValues['loan_term']),
                floatval($formValues['service_charge_rate']),
                $formValues['late_service_charge_rate'] !== '' ? floatval($formValues['late_service_charge_rate']) : null,
                $formValues['expired_service_charge_rate'] !== '' ? floatval($formValues['expired_service_charge_rate']) : null,
                floatval($formValues['verification_charge']),
                $formValues['effective_date'] ?: null,
                $id,
            ]);

            $successMessage = 'Service charge updated successfully.';
            $editLoanCharge = null;
            $formValues = [
                'product_id' => '',
                'loan_term' => '',
                'service_charge_rate' => '',
                'late_service_charge_rate' => '',
                'expired_service_charge_rate' => '',
                'verification_charge' => '',
                'effective_date' => date('Y-m-d'),
            ];
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_charge_insert'])) {
    $formValues['product_id'] = $_POST['product_id'] ?? '';
    $formValues['loan_term'] = $_POST['loan_term'] ?? '';
    $formValues['service_charge_rate'] = $_POST['service_charge_rate'] ?? '';
    $formValues['late_service_charge_rate'] = $_POST['late_service_charge_rate'] ?? '';
    $formValues['expired_service_charge_rate'] = $_POST['expired_service_charge_rate'] ?? '';
    $formValues['verification_charge'] = $_POST['verification_charge'] ?? '';
    $formValues['effective_date'] = $_POST['effective_date'] ?? date('Y-m-d');

    if ($formValues['product_id'] === '') {
        $errors[] = 'Product is required.';
    }
    if ($formValues['loan_term'] === '') {
        $errors[] = 'Loan term is required.';
    }
    if ($formValues['service_charge_rate'] === '') {
        $errors[] = 'Service charge rate is required.';
    }
    if ($formValues['late_service_charge_rate'] === '') {
        $errors[] = 'Late service charge rate is required.';
    }
    if ($formValues['expired_service_charge_rate'] === '') {
        $errors[] = 'Expired service charge rate is required.';
    }
    if ($formValues['verification_charge'] === '') {
        $errors[] = 'Verification charge is required.';
    }

    if (empty($errors)) {
        try {
            $product_name = '';
            if ($formValues['product_id'] !== '') {
                $stmtP = $pdo->prepare("SELECT product_name FROM loan_info WHERE id = ? LIMIT 1");
                $stmtP->execute([intval($formValues['product_id'])]);
                $p = $stmtP->fetch();
                $product_name = $p['product_name'] ?? '';
            }

            $insert = $pdo->prepare("INSERT INTO loan_charge (loan_info_id, product_name, loan_term, service_charge_rate, late_service_charge_rate, expired_service_charge_rate, verification_charge, effective_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $formValues['product_id'] !== '' ? intval($formValues['product_id']) : null,
                $product_name,
                intval($formValues['loan_term']),
                floatval($formValues['service_charge_rate']),
                $formValues['late_service_charge_rate'] !== '' ? floatval($formValues['late_service_charge_rate']) : null,
                $formValues['expired_service_charge_rate'] !== '' ? floatval($formValues['expired_service_charge_rate']) : null,
                floatval($formValues['verification_charge']),
                $formValues['effective_date'] ?: null,
                $_SESSION['user_id'] ?? null,
            ]);

            $successMessage = 'Service charge saved successfully.';
            $formValues = [
                'product_id' => '',
                'loan_term' => '',
                'service_charge_rate' => '',
                'late_service_charge_rate' => '',
                'expired_service_charge_rate' => '',
                'verification_charge' => '',
                'effective_date' => date('Y-m-d'),
            ];
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

$loanChargeRows = [];
try {
    $stmt = $pdo->query("SELECT lc.id, lc.product_name, lc.loan_term, lc.service_charge_rate, lc.late_service_charge_rate, lc.expired_service_charge_rate, lc.verification_charge, lc.effective_date, lc.created_at FROM loan_charge lc ORDER BY lc.id DESC");
    $loanChargeRows = $stmt->fetchAll();
} catch (PDOException $e) {
    // ignore
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
    </div>
    <script>
        window.addEventListener('load', function () {
            var msg = document.getElementById('successMessage');
            if (!msg) return;
            setTimeout(function () {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
                setTimeout(function () {
                    if (msg.parentNode) {
                        msg.parentNode.removeChild(msg);
                    }
                }, 500);
            }, 4000);
        });
    </script>
<?php endif; ?>

<?php if ($editLoanCharge && isset($_GET['edit_id'])): ?>
    <form class="mb-4" method="post" action="?tab=loan-serCharge">
        <input type="hidden" name="loan_charge_update" value="1">
        <input type="hidden" name="loan_charge_id" value="<?php echo intval($editLoanCharge['id']); ?>">
        <div class="row align-items-end">
            <div class="col-md-4 mb-3">
                <label class="form-label">প্রোডাক্টের নাম <span class="text-danger">*</span></label>
                <select class="form-select" name="product_id" required>
                    <option value="">- নির্বাচন করুন -</option>
                    <?php foreach ($products as $prod): ?>
                        <option value="<?php echo htmlspecialchars($prod['id']); ?>" <?php echo ($formValues['product_id'] == $prod['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($prod['product_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">ঋণের মেয়াদকাল (মাস) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="loan_term" value="<?php echo htmlspecialchars($formValues['loan_term']); ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">সার্ভিস চার্জের হার(%) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="service_charge_rate" value="<?php echo htmlspecialchars($formValues['service_charge_rate']); ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">বিলম্বিত সার্ভিস চার্জের হার(%)</label>
                <input type="number" step="0.01" class="form-control" name="late_service_charge_rate" value="<?php echo htmlspecialchars($formValues['late_service_charge_rate']); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">মেয়াদোত্তির্ণ সার্ভিস চার্জের হার(%)</label>
                <input type="number" step="0.01" class="form-control" name="expired_service_charge_rate" value="<?php echo htmlspecialchars($formValues['expired_service_charge_rate']); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">ভেরিফিকেশন চার্জ (টাকার পরিমাণ)</label>
                <input type="number" step="0.01" class="form-control" name="verification_charge" value="<?php echo htmlspecialchars($formValues['verification_charge']); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">কার্যকর তারিখ</label>
                <input type="date" class="form-control" name="effective_date" value="<?php echo htmlspecialchars($formValues['effective_date']); ?>">
            </div>
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm">
                    Update Service Charge (সার্ভিস চার্জ হালনাগাদ করুন)
                </button>
                <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?tab=loan-serCharge" class="btn btn-secondary btn-lg px-4 shadow-sm ms-2">Cancel</a>
            </div>
        </div>
    </form>
<?php else: ?>
    <form class="mb-4" method="post" action="?tab=loan-serCharge">
        <input type="hidden" name="loan_charge_insert" value="1">
        <div class="row align-items-end">
            <div class="col-md-4 mb-3">
                <label class="form-label">প্রোডাক্টের নাম <span class="text-danger">*</span></label>
                <select class="form-select" name="product_id" required>
                    <option value="">- নির্বাচন করুন -</option>
                    <?php foreach ($products as $prod): ?>
                        <option value="<?php echo htmlspecialchars($prod['id']); ?>" <?php echo ($formValues['product_id'] == $prod['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($prod['product_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">ঋণের মেয়াদকাল (মাস) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="loan_term" value="<?php echo htmlspecialchars($formValues['loan_term']); ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">সার্ভিস চার্জের হার(%) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="service_charge_rate" value="<?php echo htmlspecialchars($formValues['service_charge_rate']); ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">বিলম্বিত সার্ভিস চার্জের হার(%)</label>
                <input type="number" step="0.01" class="form-control" name="late_service_charge_rate" value="<?php echo htmlspecialchars($formValues['late_service_charge_rate']); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">মেয়াদোত্তির্ণ সার্ভিস চার্জের হার(%)</label>
                <input type="number" step="0.01" class="form-control" name="expired_service_charge_rate" value="<?php echo htmlspecialchars($formValues['expired_service_charge_rate']); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">ভেরিফিকেশন চার্জ (টাকার পরিমাণ)</label>
                <input type="number" step="0.01" class="form-control" name="verification_charge" value="<?php echo htmlspecialchars($formValues['verification_charge']); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">কার্যকর তারিখ</label>
                <input type="date" class="form-control" name="effective_date" value="<?php echo htmlspecialchars($formValues['effective_date']); ?>">
            </div>
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                    Save Service Charge (সার্ভিস চার্জ সংরক্ষণ করুন)
                </button>
            </div>
        </div>
    </form>
<?php endif; ?>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>নং</th>
      <th>প্রোডাক্টের নাম</th>
      <th>ঋণের মেয়াদকাল (মাস)</th>
      <th>সার্ভিস চার্জ (%)</th>
      <th>বিলম্বিত সার্ভিস (%)</th>
      <th>মেয়াদোত্তির্ণ সার্ভিস (%)</th>
      <th>ভেরিফিকেশন চার্জ (টাকার পরিমাণ)</th>
      <th>কার্যকর তারিখ</th>
      <th>কর্মকান্ড</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($loanChargeRows)): ?>
        <?php foreach ($loanChargeRows as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['id']); ?></td>
                <td><?php echo htmlspecialchars($r['product_name']); ?></td>
                <td><?php echo htmlspecialchars($r['loan_term']); ?></td>
                <td><?php echo htmlspecialchars($r['service_charge_rate']); ?></td>
                <td><?php echo htmlspecialchars($r['late_service_charge_rate']); ?></td>
                <td><?php echo htmlspecialchars($r['expired_service_charge_rate']); ?></td>
                <td><?php echo htmlspecialchars($r['verification_charge']); ?></td>
                <td><?php echo htmlspecialchars($r['effective_date']); ?></td>
                <td>
                    <a href="?tab=loan-serCharge&edit_id=<?php echo intval($r['id']); ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                    <form method="post" action="?tab=loan-serCharge" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                        <input type="hidden" name="loan_charge_delete" value="1">
                        <input type="hidden" name="loan_charge_id" value="<?php echo intval($r['id']); ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="9" class="text-center">No records found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
