<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

$stmt = $pdo->prepare("SELECT * FROM expenses ORDER BY id DESC");
$stmt->execute();
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtCat = $pdo->prepare("SELECT id, name FROM expense_category WHERE status = 'A' ORDER BY name ASC");
$stmtCat->execute();
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$stmtGlac = $pdo->prepare("SELECT id, glac_name FROM glac_mst WHERE status = 'A' AND LEVEL_CODE = 4 AND glac_type = 4 ORDER BY id ASC");
$stmtGlac->execute();
$glacs = $stmtGlac->fetchAll(PDO::FETCH_ASSOC);
?>
<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Expenses <span class="text-secondary">(ব্যয়সমূহ)</span></h3>
                <hr class="mb-4" />
                <form method="post" enctype="multipart/form-data" action="../process/expenses_process.php" class="mb-4">
                    <input type="hidden" name="action" value="insert">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="exp_date" class="form-label">Exp Date</label>
                            <input type="date" class="form-control" id="exp_date" name="exp_date" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="exp_cat" class="form-label">Expense Category</label>
                            <select class="form-control" id="exp_cat" name="exp_cat" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="gl_acc" class="form-label">Gl Account</label>
                            <select class="form-control" id="gl_acc" name="gl_acc" required>
                                <option value="">Select Account</option>
                                <?php foreach ($glacs as $glac): ?>
                                    <option value="<?= $glac['id']; ?>"><?= htmlspecialchars($glac['glac_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                          <label for="exp_slip" class="form-label">Expense Slip</label>
                          <input type="file" class="form-control" id="exp_slip" name="exp_slip" accept="image/*,application/pdf" onchange="previewExpSlip(event)">
                          <div id="exp_slip_preview" class="mt-2"></div>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">Save Expense (ব্যয় সংরক্ষণ করুন)</button>
                        </div>
                    </div>
                </form>
                <hr class="my-4" />
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Note</th>
                                <th>Slip</th>
                                <th>Status</th>
                                <th>Gl Account</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td><?= $exp['id']; ?></td>
                                    <td><?= htmlspecialchars($exp['exp_date']); ?></td>
                                    <td><?php 
                                        $cat = array_filter($categories, function($c) use ($exp) { return $c['id'] == $exp['exp_cat']; });
                                        echo $cat ? htmlspecialchars(array_values($cat)[0]['name']) : 'N/A';
                                    ?></td>
                                    <td><?= $exp['amount']; ?></td>
                                    <td><?= htmlspecialchars($exp['reference']); ?></td>
                                    <td><?= htmlspecialchars($exp['note']); ?></td>
                                    <td><?php if ($exp['exp_slip']): ?><a href="../expenses/<?= htmlspecialchars($exp['exp_slip']); ?>" target="_blank">View</a><?php endif; ?></td>
                                    <td><?= ($exp['status'] === 'A') ? 'Active' : 'Inactive'; ?></td>
                                    <td>
                                      <?php 
                                        $glac = array_filter($glacs, function($g) use ($exp) { return $g['id'] == $exp['glac_id']; });
                                        echo $glac ? htmlspecialchars(array_values($glac)[0]['glac_name']) : 'N/A';
                                    ?></td>
                                    <td>
                                        <form action="../process/expenses_process.php" method="post" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?= $exp['id']; ?>">
                                            <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This Expense?');">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-info btn-sm" onclick='editExpense(
                                            <?= (int)$exp["id"]; ?>,
                                            <?= json_encode($exp["exp_date"]); ?>,
                                            <?= json_encode($exp["exp_cat"]); ?>,
                                            <?= json_encode($exp["amount"]); ?>,
                                            <?= json_encode($exp["reference"]); ?>,
                                            <?= json_encode($exp["note"]); ?>,
                                            <?= json_encode($exp["status"]); ?>,
                                            <?= json_encode($exp["glac_id"]); ?>
                                        )'>
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="../process/expenses_process.php" method="post" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="edit_id">
              <div class="row">
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_exp_date" class="form-label">Exp Date</label>
                  <input type="date" class="form-control" id="edit_exp_date" name="edit_exp_date" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_exp_cat" class="form-label">Expense Category</label>
                  <select class="form-control" id="edit_exp_cat" name="edit_exp_cat" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_gl_acc" class="form-label">Gl Account</label>
                  <select class="form-control" id="edit_gl_acc" name="edit_gl_acc" required>
                    <option value="">Select Account</option>
                    <?php foreach ($glacs as $glac): ?>
                        <option value="<?= $glac['id']; ?>"><?= htmlspecialchars($glac['glac_name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_amount" class="form-label">Amount</label>
                  <input type="number" class="form-control" id="edit_amount" name="edit_amount" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_reference" class="form-label">Reference</label>
                  <input type="text" class="form-control" id="edit_reference" name="edit_reference">
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_note" class="form-label">Note</label>
                  <textarea class="form-control" id="edit_note" name="edit_note" rows="2"></textarea>
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_exp_slip" class="form-label">Expense Slip (Upload to replace)</label>
                  <input type="file" class="form-control" id="edit_exp_slip" name="edit_exp_slip" accept="image/*,application/pdf" onchange="previewEditExpSlip(event)">
                  <div id="edit_exp_slip_preview" class="mt-2"></div>
                  <div id="edit_exp_slip_existing" class="mt-2"></div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                  <label for="edit_status" class="form-label">Status</label>
                  <select class="form-control" id="edit_status" name="edit_status">
                    <option value="A">Active</option>
                    <option value="I">Inactive</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="action" value="update" class="btn btn-primary">Update Expense (ব্যয় হালনাগাদ করুন)</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</main>
</div>  
</div>

<script>
function previewExpSlip(event) {
  const file = event.target.files[0];
  const previewDiv = document.getElementById('exp_slip_preview');
  previewDiv.innerHTML = '';
  if (!file) return;
  if (file.type.startsWith('image/')) {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.style.maxWidth = '120px';
    img.style.maxHeight = '120px';
    img.onload = function() { URL.revokeObjectURL(img.src); };
    previewDiv.appendChild(img);
  } else if (file.type === 'application/pdf') {
    const pdf = document.createElement('embed');
    pdf.src = URL.createObjectURL(file);
    pdf.type = 'application/pdf';
    pdf.width = '120px';
    pdf.height = '120px';
    previewDiv.appendChild(pdf);
  } else {
    previewDiv.textContent = 'Preview not available';
  }
}

function previewEditExpSlip(event) {
  const file = event.target.files[0];
  const previewDiv = document.getElementById('edit_exp_slip_preview');
  previewDiv.innerHTML = '';
  if (!file) return;
  if (file.type.startsWith('image/')) {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.style.maxWidth = '120px';
    img.style.maxHeight = '120px';
    img.onload = function() { URL.revokeObjectURL(img.src); };
    previewDiv.appendChild(img);
  } else if (file.type === 'application/pdf') {
    const pdf = document.createElement('embed');
    pdf.src = URL.createObjectURL(file);
    pdf.type = 'application/pdf';
    pdf.width = '120px';
    pdf.height = '120px';
    previewDiv.appendChild(pdf);
  } else {
    previewDiv.textContent = 'Preview not available';
  }
}

// Show existing slip in edit modal
function editExpense(id, exp_date, exp_cat, amount, reference, note, status, gl_acc) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_exp_date').value = exp_date;
  document.getElementById('edit_exp_cat').value = exp_cat;
  document.getElementById('edit_amount').value = amount;
  document.getElementById('edit_reference').value = reference;
  document.getElementById('edit_note').value = note;
  document.getElementById('edit_status').value = status;
  document.getElementById('edit_gl_acc').value = gl_acc;
  document.getElementById('edit_exp_slip').value = '';
  document.getElementById('edit_exp_slip_preview').innerHTML = '';
  // Find the expense in JS from PHP array
  var expenses = <?php echo json_encode($expenses); ?>;
  var found = expenses.find(function(e) { return e.id == id; });
  var existingDiv = document.getElementById('edit_exp_slip_existing');
  existingDiv.innerHTML = '';
  if (found && found.exp_slip) {
    var ext = found.exp_slip.split('.').pop().toLowerCase();
    var url = '../expenses/' + found.exp_slip;
    if (["jpg","jpeg","png","gif","bmp","webp"].includes(ext)) {
      existingDiv.innerHTML = '<div>Current: <br><img src="'+url+'" style="max-width:120px;max-height:120px;" /></div>';
    } else if (ext === 'pdf') {
      existingDiv.innerHTML = '<div>Current: <br><embed src="'+url+'" type="application/pdf" width="120px" height="120px" /></div>';
    } else {
      existingDiv.innerHTML = '<div>Current: <a href="'+url+'" target="_blank">View</a></div>';
    }
  }
  var modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
  modal.show();
}
</script>

<?php include_once __DIR__ . '/../includes/toast.php'; ?>
<?php include_once __DIR__ . '/../includes/end.php'; ?>
