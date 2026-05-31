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
$editLoanDocument = null;
$formValues = [
    'product_id' => '',
    'document_type' => '',
    'is_required' => '',
];

if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($edit_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM loan_documents WHERE id = ? LIMIT 1");
            $stmt->execute([$edit_id]);
            $editLoanDocument = $stmt->fetch();
        } catch (PDOException $e) {
            // ignore invalid edit id
        }
    }
    if ($editLoanDocument) {
        $formValues = [
            'product_id' => $editLoanDocument['loan_info_id'] ?? '',
            'document_type' => $editLoanDocument['document_type'] ?? '',
            'is_required' => $editLoanDocument['is_required'] ?? '',
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_documents_delete'])) {
    $id = intval($_POST['loan_documents_id'] ?? 0);
    if ($id > 0) {
        try {
            $delete = $pdo->prepare("DELETE FROM loan_documents WHERE id = ?");
            $delete->execute([$id]);
            $successMessage = 'Loan document deleted successfully.';
            $editLoanDocument = null;
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_documents_update'])) {
    $id = intval($_POST['loan_documents_id'] ?? 0);
    $formValues['product_id'] = $_POST['product_id'] ?? '';
    $formValues['document_type'] = trim($_POST['document_type'] ?? '');
    $formValues['is_required'] = trim($_POST['is_required'] ?? '');

    if ($id <= 0) {
        $errors[] = 'Invalid document ID.';
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

            $update = $pdo->prepare("UPDATE loan_documents SET loan_info_id = ?, product_name = ?, document_type = ?, is_required = ? WHERE id = ?");
            $update->execute([
                $formValues['product_id'] !== '' ? intval($formValues['product_id']) : null,
                $product_name,
                $formValues['document_type'] !== '' ? $formValues['document_type'] : null,
                $formValues['is_required'] !== '' ? $formValues['is_required'] : null,
                $id,
            ]);

            $successMessage = 'Loan document updated successfully.';
            $editLoanDocument = null;
            $formValues = [
                'product_id' => '',
                'document_type' => '',
                'is_required' => '',
            ];
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_documents_insert'])) {
    $formValues['product_id'] = $_POST['product_id'] ?? '';
    $formValues['document_type'] = trim($_POST['document_type'] ?? '');
    $formValues['is_required'] = trim($_POST['is_required'] ?? '');

    if ($formValues['product_id'] === '') {
        $errors[] = 'Product is required.';
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

            $insert = $pdo->prepare("INSERT INTO loan_documents (loan_info_id, product_name, document_type, is_required, created_by) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([
                $formValues['product_id'] !== '' ? intval($formValues['product_id']) : null,
                $product_name,
                $formValues['document_type'] !== '' ? $formValues['document_type'] : null,
                $formValues['is_required'] !== '' ? $formValues['is_required'] : null,
                $_SESSION['user_id'] ?? null,
            ]);

            $successMessage = 'Loan document saved successfully.';
            $formValues = [
                'product_id' => '',
                'document_type' => '',
                'is_required' => '',
            ];
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

$loanDocuments = [];
try {
    $stmt = $pdo->query("SELECT id, product_name, document_type, is_required, created_at FROM loan_documents ORDER BY id DESC");
    $loanDocuments = $stmt->fetchAll();
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

<?php if ($editLoanDocument && isset($_GET['edit_id'])): ?>
    <form class="mb-4" method="post" action="?tab=loan-documents">
        <input type="hidden" name="loan_documents_update" value="1">
        <input type="hidden" name="loan_documents_id" value="<?php echo intval($editLoanDocument['id']); ?>">
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
                <label class="form-label">ডকুমেন্টের ধরন</label>
                <select class="form-select" name="document_type">
                    <option value="">- নির্বাচন করুন -</option>
                    <option value="NID" <?php echo ($formValues['document_type'] === 'N') ? 'selected' : ''; ?>>NID (জাতীয় পরিচয়পত্র)</option>
                    <option value="Photo" <?php echo ($formValues['document_type'] === 'P') ? 'selected' : ''; ?>>Photo (ছবি)</option>
                    <option value="Signature" <?php echo ($formValues['document_type'] === 'S') ? 'selected' : ''; ?>>Official Documents (অফিসিয়াল ডকুমেন্টস)</option>
                    <option value="Grantor" <?php echo ($formValues['document_type'] === 'G') ? 'selected' : ''; ?>>Grantor Info (অনুদানকারীর তথ্য)</option>
                    <option value="Other" <?php echo ($formValues['document_type'] === 'O') ? 'selected' : ''; ?>>Others (অন্যান্য)</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">বাধ্যতামূলক?</label>
                <select class="form-select" name="is_required">
                    <option value="">- নির্বাচন করুন -</option>
                    <option value="Yes" <?php echo ($formValues['is_required'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    <option value="No" <?php echo ($formValues['is_required'] === 'No') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm">
                    Update Documents (ডকুমেন্টস হালনাগাদ করুন)
                </button>
                <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?tab=loan-documents" class="btn btn-secondary btn-lg px-4 shadow-sm ms-2">Cancel</a>
            </div>
        </div>
    </form>
<?php else: ?>
    <form class="mb-4" method="post" action="?tab=loan-documents">
      <input type="hidden" name="loan_documents_insert" value="1">
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
          <label class="form-label">ডকুমেন্টের ধরন</label>
          <select class="form-select" name="document_type">
            <option value="">- নির্বাচন করুন -</option>
            <option value="NID" <?php echo ($formValues['document_type'] === 'N') ? 'selected' : ''; ?>>NID (জাতীয় পরিচয়পত্র)</option>
            <option value="Photo" <?php echo ($formValues['document_type'] === 'P') ? 'selected' : ''; ?>>Photo (ছবি)</option>
            <option value="Signature" <?php echo ($formValues['document_type'] === 'S') ? 'selected' : ''; ?>>Official Documents (অফিসিয়াল ডকুমেন্টস)</option>
            <option value="Grantor" <?php echo ($formValues['document_type'] === 'G') ? 'selected' : ''; ?>>Grantor Info (অনুদানকারীর তথ্য)</option>
            <option value="Other" <?php echo ($formValues['document_type'] === 'O') ? 'selected' : ''; ?>>Others (অন্যান্য)</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">বাধ্যতামূলক?</label>
          <select class="form-select" name="is_required">
            <option value="">- নির্বাচন করুন -</option>
            <option value="Yes" <?php echo ($formValues['is_required'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="No" <?php echo ($formValues['is_required'] === 'No') ? 'selected' : ''; ?>>No</option>
          </select>
        </div>
        <div class="col-12 mt-4 text-end">
          <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
            Save Documents (ডকুমেন্টস সংরক্ষণ করুন)
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
      <th>ডকুমেন্টের ধরন</th>
      <th>বাধ্যতামূলক</th>
      <th>তৈরী হয়েছে</th>
      <th>কর্মকান্ড</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($loanDocuments)): ?>
        <?php foreach ($loanDocuments as $doc): ?>
            <tr>
                <td><?php echo htmlspecialchars($doc['id']); ?></td>
                <td><?php echo htmlspecialchars($doc['product_name']); ?></td>
                <td>
                    <?php 
                        $docTypeMap = [
                            'N' => 'NID (জাতীয় পরিচয়পত্র)',
                            'P' => 'Photo (ছবি)',
                            'S' => 'Official Documents (অফিসিয়াল ডকুমেন্টস)',
                            'G' => 'Grantor Info (অনুদানকারীর তথ্য)',
                            'O' => 'Others (অন্যান্য)',
                        ];
                        $displayType = $docTypeMap[$doc['document_type']] ?? htmlspecialchars($doc['document_type']);
                        echo $displayType;
                    ?>
                </td>
                <td><?php echo htmlspecialchars($doc['is_required']); ?></td>
                <td><?php echo htmlspecialchars($doc['created_at']); ?></td>
                <td>
                    <a href="?tab=loan-documents&edit_id=<?php echo intval($doc['id']); ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                    <form method="post" action="?tab=loan-documents" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                        <input type="hidden" name="loan_documents_delete" value="1">
                        <input type="hidden" name="loan_documents_id" value="<?php echo intval($doc['id']); ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
          <td colspan="6" class="text-center">No documents found.</td>
        </tr>
    <?php endif; ?>
  </tbody>
</table>
