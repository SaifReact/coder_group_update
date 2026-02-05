<?php
// Prepare GL lists for Credit and Debit dropdowns
$creditGLs = [];
$debitGLs = [];
try {
    if (!isset($pdo)) {
        include __DIR__ . '/../config/config.php';
    }

    $stmt = $pdo->prepare("SELECT id, glac_name FROM glac_mst WHERE parent_child = 'C' AND gl_nature = 'C' ORDER BY glac_name ASC");
    $stmt->execute();
    $creditGLs = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT id, glac_name FROM glac_mst WHERE parent_child = 'C' AND gl_nature = 'D' ORDER BY glac_name ASC");
    $stmt->execute();
    $debitGLs = $stmt->fetchAll();
} catch (Exception $e) {
    // Fail silently — leave lists empty
}

?>
<form id="paymentVoucherForm">
    <div id="voucherRows">
        <div class="row g-2 mb-3 voucher-row">
            <div class="col-md-3">
                <select class="form-select" name="credit_gl[]">
                    <option value="">ক্রেডিট জি.এল</option>
                    <?php if (!empty($creditGLs)) : ?>
                        <?php foreach ($creditGLs as $g) : ?>
                            <option value="<?= htmlspecialchars($g['id']) ?>"><?= htmlspecialchars($g['glac_name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="debit_gl[]">
                    <option value="">ডেবিট জি.এল</option>
                    <?php if (!empty($debitGLs)) : ?>
                        <?php foreach ($debitGLs as $g) : ?>
                            <option value="<?= htmlspecialchars($g['id']) ?>"><?= htmlspecialchars($g['glac_name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="amount[]" placeholder="টাকার পরিমাণ">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="narration[]" placeholder="হিসাবের বিবরণ">
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-outline-primary add-row" onclick="addVoucherRow(this)">+</button>
                <button type="button" class="btn btn-outline-danger ms-1 remove-row" style="display:none;" onclick="removeVoucherRow(this)">×</button>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <span>মোট ক্রেডিট টাকা : <span id="total_credit">0.00</span></span>
        </div>
        <div class="col-md-6">
            <span>মোট ডেবিট টাকা : <span id="total_debit">0.00</span></span>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">ব্যালেন্স</label>
        <div id="balance"></div>
    </div>
    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm" disabled>ভাউচার জমা দিন</button>
    </div>
</form>
<script>
function addVoucherRow(btn) {
    var row = btn.closest('.voucher-row');
    var newRow = row.cloneNode(true);
    // Clear input values
    newRow.querySelectorAll('input').forEach(function(input) { input.value = ''; });
    newRow.querySelectorAll('select').forEach(function(select) { select.selectedIndex = 0; });
    // Show remove button for all but first row
    newRow.querySelector('.remove-row').style.display = '';
    document.getElementById('voucherRows').appendChild(newRow);
    // Show remove button for this row too (if not first)
    row.querySelector('.remove-row').style.display = '';
}
function removeVoucherRow(btn) {
    var row = btn.closest('.voucher-row');
    var container = document.getElementById('voucherRows');
    if (container.children.length > 1) {
        row.remove();
    }
}
</script>
