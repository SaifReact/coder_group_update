<?php
session_start();

include_once __DIR__ . '/../config/config.php';
$stmt = $pdo->query("SELECT id, banner_name_bn, banner_name_en, banner_image, banner_category FROM banner ORDER BY id ASC");
$banners = $stmt->fetchAll();

include_once __DIR__ . '/../includes/open.php';
?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
      <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
            <div class="container">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Images <span class="text-secondary">( ছবিসমূহ )</span></h3> 
                      <hr class="mb-4" />

                        <form action="../process/banner_process.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <!-- New Select Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="banner_category" class="form-label">Category Image</label>
                                    <select class="form-select" id="banner_category" name="banner_category" required>
                                        <option value="" disabled selected>Select Category ( নির্বাচন করুন )</option>
                                        <option value="ban">Banner ( ব্যানার )</option>
                                        <option value="oth">Other ( অন্যান্য )</option>
                                    </select>
                                </div>
                                <!-- Existing Banner Name Fields -->
                                <div class="col-md-6 mb-3">
                                    <label for="banner_name_bn" class="form-label"> Name (Bangla)</label>
                                    <input type="text" class="form-control" id="banner_name_bn" name="banner_name_bn" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="banner_name_en" class="form-label">Name (English)</label>
                                    <input type="text" class="form-control" id="banner_name_en" name="banner_name_en" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="banner_image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*" required onchange="previewBannerImage(event)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <img id="bannerImagePreview" src="#" alt="Preview" style="display:none;max-height:80px;margin-top:8px;">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" name="action" value="insert" class="btn btn-primary btn-lg px-4 shadow-sm">
                                        Save Image ( ছবি সংরক্ষণ করুন )
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr class="my-4" />
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name (Bangla)</th>
                                        <th>Name (English)</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($banners as $banner): ?>
                                    <tr>
                                        <td><?= $banner['id']; ?></td>
                                        <td><?= htmlspecialchars($banner['banner_name_bn']); ?></td>
                                        <td><?= htmlspecialchars($banner['banner_name_en']); ?></td>
                                        <td>
                                            <img src="../banner/<?= htmlspecialchars($banner['banner_image']); ?>" style="height:40px;cursor:pointer;" onclick="showBannerModal('../banner/<?= htmlspecialchars($banner['banner_image']); ?>')">
                                        </td>
                                        <td>
                                            <form action="../process/banner_process.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?= $banner['id']; ?>">
                                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this banner?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-info btn-sm" 
                                            onclick="editBanner(
                                              <?= $banner['id']; ?>, 
                                              '<?= htmlspecialchars($banner['banner_name_bn'], ENT_QUOTES); ?>', 
                                              '<?= htmlspecialchars($banner['banner_name_en'], ENT_QUOTES); ?>', 
                                              '../banner/<?= htmlspecialchars($banner['banner_image']); ?>',
                                              '<?= htmlspecialchars($banner['banner_category'], ENT_QUOTES); ?>'
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
                <!-- Banner Image Modal -->
                <div class="modal fade" id="bannerImageModal" tabindex="-1" aria-labelledby="bannerImageModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:98vw;width:98vw;">
                    <div class="modal-content">
                      <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-center">
                        <img id="bannerModalImg" src="#" alt="Banner" style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 2px 16px #000a;">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Edit Modal -->
                <div class="modal fade" id="editBannerModal" tabindex="-1" aria-labelledby="editBannerModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="../process/banner_process.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editBannerModalLabel">Edit Banner</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" id="edit_id">
                          <div class="row">
                            <!-- New Select Input -->
                            <div class="mb-3 col-md-6">
                              <label for="edit_banner_category" class="form-label">Banner Category</label>
                              <select class="form-select" id="edit_banner_category" name="banner_category" required>
                                <option value="" disabled selected>Select Category ( নির্বাচন করুন )</option>
                                <option value="ban">Banner ( ব্যানার )</option>
                                <option value="oth">Other ( অন্যান্য )</option>
                              </select>
                            </div>
                            <div class="mb-3 col-md-6">
                              <label for="edit_banner_name_bn" class="form-label">Name (Bangla)</label>
                              <input type="text" class="form-control" id="edit_banner_name_bn" name="banner_name_bn" required>
                            </div>
                            <div class="mb-3 col-md-6">
                              <label for="edit_banner_name_en" class="form-label">Name (English)</label>
                              <input type="text" class="form-control" id="edit_banner_name_en" name="banner_name_en" required>
                            </div>
                            <div class="mb-3 col-md-6">
                              <label for="edit_banner_image" class="form-label">Image (optional)</label>
                              <input type="file" class="form-control" id="edit_banner_image" name="banner_image" accept="image/*" onchange="previewEditBannerImage(event)">
                              <img id="editBannerImagePreview" src="#" alt="Preview" style="display:none;max-height:80px;margin-top:8px;">
                            </div>
                            <div class="mb-3 col-md-6">
                              <label>Current Image</label>
                              <img id="editBannerCurrentImage" src="#" alt="Current Banner" style="max-height:80px;">
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="action" value="update" class="btn btn-primary">Update Image ( ছবি হালনাগাদ করুন )</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            </div>
        </main>
  </div>
  
</div>
<!-- Hero End -->

<script>
function previewBannerImage(event) {
  var img = document.getElementById('bannerImagePreview');
  if(event.target.files && event.target.files[0]) {
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
}
function showBannerModal(src) {
  document.getElementById('bannerModalImg').src = src;
  var modal = new bootstrap.Modal(document.getElementById('bannerImageModal'));
  modal.show();
}
function previewEditBannerImage(event) {
  var img = document.getElementById('editBannerImagePreview');
  if(event.target.files && event.target.files[0]) {
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
}
function editBanner(id, nameBn, nameEn, imgSrc, category) {
  // Set the hidden input for the ID
  document.getElementById('edit_id').value = id;

  // Set the Banner Name fields
  document.getElementById('edit_banner_name_bn').value = nameBn;
  document.getElementById('edit_banner_name_en').value = nameEn;

  // Set the Current Image
  document.getElementById('editBannerCurrentImage').src = imgSrc;

  // Set the Category in the Select Input
  const categorySelect = document.getElementById('edit_banner_category');
  categorySelect.value = category;

  // Hide the preview for the new image
  document.getElementById('editBannerImagePreview').style.display = 'none';

  // Show the Edit Modal
  var modal = new bootstrap.Modal(document.getElementById('editBannerModal'));
  modal.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../includes/toast.php'; ?>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
