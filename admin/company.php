<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$stmt = $pdo->query("SELECT * FROM company ORDER BY id ASC");
$companies = $stmt->fetchAll();
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Company <span class="text-secondary">( কোম্পানি )</span></h3> 
                      <hr class="mb-4" />

                        <!-- Add Form -->
                        <form action="../process/company_process.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_name_en" class="form-label">Company Name (English)</label>
                                    <input type="text" class="form-control" id="company_name_en" name="company_name_en" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="company_name_bn" class="form-label">Company Name (Bangla)</label>
                                    <input type="text" class="form-control" id="company_name_bn" name="company_name_bn" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="company_image" class="form-label">Company Logo</label>
                                    <input type="file" class="form-control" id="company_image" name="company_image" accept="image/*" required onchange="previewCompanyImage(event)">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="about_company" class="form-label">About Company</label>
                                    <textarea class="form-control" id="about_company" name="about_company" rows="5"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <img id="companyImagePreview" src="#" alt="Preview" style="display:none;max-height:80px;margin-top:8px;">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" name="action" value="insert" class="btn btn-primary btn-lg px-4 shadow-sm">
                                        Save Company (কোম্পানি সংরক্ষণ করুন)
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-4" />

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Company Name</th>
                                        <th>About Company</th>
                                        <th>Logo</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($companies as $company): ?>
                                    <tr>
                                        <td><?= $company['id']; ?></td>
                                        <td><?= htmlspecialchars($company['company_name_en']); ?><br/>
                                        <?= htmlspecialchars($company['company_name_bn']); ?></td>
                                        <td><?= strip_tags($company['about_company'], '<p><ul><li><b><i><br>'); ?></td>
                                        <td>
                                            <img src="../company/<?= htmlspecialchars($company['company_image']); ?>" style="height:40px;cursor:pointer;" onclick="showCompanyModal('../company/<?= htmlspecialchars($company['company_image']); ?>')">
                                        </td>
                                        <td>
                                            <!-- Delete -->
                                            <form action="../process/company_process.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?= $company['id']; ?>">
                                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this company?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Edit -->
                                            <button type="button" class="btn btn-info btn-sm" 
                                              onclick="editCompany(
                                                <?= $company['id']; ?>, 
                                                '<?= htmlspecialchars($company['company_name_en'], ENT_QUOTES); ?>', 
                                                '<?= htmlspecialchars($company['company_name_bn'], ENT_QUOTES); ?>', 
                                                '<?= htmlspecialchars($company['about_company'], ENT_QUOTES); ?>', 
                                                '../company/<?= htmlspecialchars($company['company_image']); ?>'
                                              )">
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

                <!-- Edit Modal -->
                <div class="modal fade" id="editCompanyModal" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="../process/company_process.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Company</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" id="edit_id">
                          <div class="row">
                            <div class="mb-3 col-md-6">
                              <label for="edit_company_name_en" class="form-label">Company Name (English)</label>
                              <input type="text" class="form-control" id="edit_company_name_en" name="edit_company_name_en" required>
                            </div>
                            <div class="mb-3 col-md-6">
                              <label for="edit_company_name_bn" class="form-label">Company Name (Bangla)</label>
                              <input type="text" class="form-control" id="edit_company_name_bn" name="edit_company_name_bn" required>
                            </div>
                            <div class="mb-3 col-md-6">
                              <label for="edit_company_image" class="form-label">Company Logo (optional)</label>
                              <input type="file" class="form-control" id="edit_company_image" name="edit_company_image" accept="image/*" onchange="previewEditCompanyImage(event)">
                              <img id="editCompanyImagePreview" src="#" alt="Preview" style="display:none;max-height:80px;margin-top:8px;">
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                              <label for="edit_about_company" class="form-label">About Company</label>
                              <textarea class="form-control" id="edit_about_company" name="edit_about_company" rows="5"></textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                              <label>Current Logo</label><br>
                              <img id="editCompanyCurrentImage" src="#" alt="Current Logo" style="max-height:80px;">
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="action" value="update" class="btn btn-primary">Update Company (কোম্পানি হালনাগাদ করুন)</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            </div>
        </main>
  </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>

<script>
const editors = {}; // store editors globally
['#about_company', '#edit_about_company'].forEach(selector => {
    const el = document.querySelector(selector);
    if (el) {
        ClassicEditor.create(el, {
            toolbar: [
                'bold', 'italic', 'underline', 'link',
                'bulletedList', 'numberedList',
                '|', 'fontSize', 'undo', 'redo'
            ],
            fontSize: {
                options: [9, 11, 13, 'default', 17, 19, 21]
            }
        }).then(editor => {
            editors[selector] = editor; // save reference
        }).catch(error => {
            console.error(error);
        });
    }
});
</script>

<script>
function previewCompanyImage(event) {
  var img = document.getElementById('companyImagePreview');
  if(event.target.files && event.target.files[0]) {
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
}
function showCompanyModal(src) {
  document.getElementById('companyModalImg').src = src;
  var modal = new bootstrap.Modal(document.getElementById('companyImageModal'));
  modal.show();
}
function previewEditCompanyImage(event) {
  var img = document.getElementById('editCompanyImagePreview');
  if(event.target.files && event.target.files[0]) {
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
}
function editCompany(id, nameEn, nameBn, aboutCompany, imgSrc) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_company_name_en').value = nameEn;
  document.getElementById('edit_company_name_bn').value = nameBn;
  document.getElementById('edit_about_company').value = aboutCompany;
  document.getElementById('editCompanyCurrentImage').src = imgSrc;
  document.getElementById('editCompanyImagePreview').style.display = 'none';

  if (editors['#edit_about_company']) {
        editors['#edit_about_company'].setData(aboutCompany);
    }

  var modal = new bootstrap.Modal(document.getElementById('editCompanyModal'));
  modal.show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../includes/toast.php'; ?>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
