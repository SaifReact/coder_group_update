<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';
$stmt = $pdo->query("SELECT id, banner_name_bn, banner_name_en, banner_image, banner_category FROM banner ORDER BY id ASC");
$banners = $stmt->fetchAll();

?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Banner & Documents <span class="text-secondary">( ব্যানার এবং ডকুমেন্টস )</span></h3> 
                      <hr class="mb-4" />

                        <form action="../process/imgdocs_process.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <!-- New Select Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="banner_category" class="form-label">Category Image</label>
                                    <select class="form-select" id="banner_category" name="banner_category" required>
                                        <option value="" disabled selected>Select Category ( নির্বাচন করুন )</option>
                                        <option value="ban">Banner ( ব্যানার )</option>
                                        <option value="oth">Other ( অন্যান্য )</option>
                                        <option value="sig">Sign ( স্বাক্ষর )</option>
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
                                    <label for="banner_image" class="form-label">Image/PDF</label>
                                    <input type="file" class="form-control" id="banner_image" name="banner_image" accept=".jpg,.jpeg,.png,.pdf" required onchange="previewBannerFile(event)">
                                </div>
                                <div class="col-md-6 mb-3">
                                <div id="bannerFilePreview" style="display:none;max-height:80px;margin-top:8px;"></div>
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
                                        <?php
                                            $file = $banner['banner_image'];
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $fileUrl = '../banner/' . htmlspecialchars($file);
                                        ?>
                                        <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                                            <img src="<?= $fileUrl ?>" style="height:40px;cursor:pointer;" onclick="showBannerModal('<?= $fileUrl ?>', 'image')">
                                        <?php elseif ($ext === 'pdf'): ?>
                                            <span style="cursor:pointer;" onclick="showBannerModal('<?= $fileUrl ?>', 'pdf')">
                                            <i class="fa fa-file-pdf text-danger" style="font-size:2rem;"></i>
                                            <span class="small ms-1">PDF</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No Preview</span>
                                        <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="../process/imgdocs_process.php" method="post" style="display:inline-block;">
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
                </div>
                <!-- Banner Image Modal -->
                <div class="modal fade" id="bannerImageModal" tabindex="-1" aria-labelledby="bannerImageModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:98vw;width:98vw;">
                    <div class="modal-content">
                      <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-center">
                        <div id="bannerModalBody"></div>
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
function showBannerModal(src, type) {
  var modal = new bootstrap.Modal(document.getElementById('bannerImageModal'));
  var modalBody = document.getElementById('bannerModalBody');
  if (type === 'image') {
    modalBody.innerHTML = '<img src="' + src + '" alt="Banner" style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 2px 16px #000a;cursor:zoom-in;" onclick="this.style.maxWidth=\'none\';this.style.maxHeight=\'none\';">';
  } else if (type === 'pdf') {
    modalBody.innerHTML = '<embed src="' + src + '" type="application/pdf" style="width:70vw;height:70vh;border-radius:8px;box-shadow:0 2px 16px #000a;">';
  } else {
    modalBody.innerHTML = '<div class="text-muted">No preview available</div>';
  }
  modal.show();
}
function previewEditBannerImage(event) {
  var previewDiv = document.getElementById('editBannerImagePreview');
  if(event.target.files && event.target.files[0]) {
    var file = event.target.files[0];
    var ext = file.name.split('.').pop().toLowerCase();
    var url = URL.createObjectURL(file);
    if(['jpg','jpeg','png'].includes(ext)) {
      previewDiv.style.display = 'block';
      previewDiv.src = url;
      previewDiv.alt = 'Preview';
      previewDiv.style.maxHeight = '80px';
      previewDiv.style.marginTop = '8px';
      previewDiv.style.width = '';
      previewDiv.style.borderRadius = '8px';
      previewDiv.style.objectFit = 'contain';
      // Remove any previous PDF/embed
      if(document.getElementById('editBannerImagePreviewPdf')) {
        document.getElementById('editBannerImagePreviewPdf').remove();
      }
      if(document.getElementById('editBannerImagePreviewOther')) {
        document.getElementById('editBannerImagePreviewOther').remove();
      }
    } else if(ext === 'pdf') {
      previewDiv.style.display = 'none';
      // Remove image preview
      previewDiv.src = '#';
      // Remove any previous PDF/embed
      if(document.getElementById('editBannerImagePreviewPdf')) {
        document.getElementById('editBannerImagePreviewPdf').remove();
      }
      if(document.getElementById('editBannerImagePreviewOther')) {
        document.getElementById('editBannerImagePreviewOther').remove();
      }
      var embed = document.createElement('embed');
      embed.id = 'editBannerImagePreviewPdf';
      embed.src = url;
      embed.type = 'application/pdf';
      embed.style.width = '100%';
      embed.style.height = '80px';
      embed.style.borderRadius = '8px';
      previewDiv.parentNode.appendChild(embed);
    } else {
      previewDiv.style.display = 'none';
      previewDiv.src = '#';
      // Remove any previous PDF/embed
      if(document.getElementById('editBannerImagePreviewPdf')) {
        document.getElementById('editBannerImagePreviewPdf').remove();
      }
      if(document.getElementById('editBannerImagePreviewOther')) {
        document.getElementById('editBannerImagePreviewOther').remove();
      }
      var otherSpan = document.createElement('span');
      otherSpan.id = 'editBannerImagePreviewOther';
      otherSpan.className = 'text-muted';
      otherSpan.innerText = 'No Preview';
      previewDiv.parentNode.appendChild(otherSpan);
    }
  } else {
    previewDiv.style.display = 'none';
    previewDiv.src = '#';
    if(document.getElementById('editBannerImagePreviewPdf')) {
      document.getElementById('editBannerImagePreviewPdf').remove();
    }
    if(document.getElementById('editBannerImagePreviewOther')) {
      document.getElementById('editBannerImagePreviewOther').remove();
    }
  }
}
                        function previewBannerFile(event) {
                          var input = event.target;
                          var preview = document.getElementById('bannerFilePreview');
                          preview.innerHTML = '';
                          preview.style.display = 'none';
                          if (input.files && input.files[0]) {
                            var file = input.files[0];
                            var ext = file.name.split('.').pop().toLowerCase();
                            var reader = new FileReader();
                            if (['jpg','jpeg','png'].includes(ext)) {
                              reader.onload = function(e) {
                                preview.innerHTML = '<img src="' + e.target.result + '" style="max-height:80px;max-width:100%;border-radius:8px;" />';
                                preview.style.display = 'block';
                              }
                              reader.readAsDataURL(file);
                            } else if (ext === 'pdf') {
                              reader.onload = function(e) {
                                preview.innerHTML = '<embed src="' + e.target.result + '" type="application/pdf" style="width:100%;height:200border-radius:8px;" />';
                                preview.style.display = 'block';
                              }
                              reader.readAsDataURL(file);
                            }
                          }
                        }

function editBanner(id, nameBn, nameEn, imgSrc, category) {
  // Set the hidden input for the ID
  document.getElementById('edit_id').value = id;

  // Set the Banner Name fields
  document.getElementById('edit_banner_name_bn').value = nameBn;
  document.getElementById('edit_banner_name_en').value = nameEn;


  // Show the correct preview for current file (image, PDF, or other)
  var ext = imgSrc.split('.').pop().toLowerCase();
  var currentImageDiv = document.getElementById('editBannerCurrentImage');
  if(['jpg','jpeg','png'].includes(ext)) {
    currentImageDiv.src = imgSrc;
    currentImageDiv.style.display = 'block';
    currentImageDiv.alt = 'Current Banner';
    currentImageDiv.removeAttribute('onclick');
  } else if(ext === 'pdf') {
    currentImageDiv.style.display = 'none';
    // Show PDF icon and preview
    if(!document.getElementById('editBannerCurrentPdf')) {
      var pdfIcon = document.createElement('span');
      pdfIcon.id = 'editBannerCurrentPdf';
      pdfIcon.innerHTML = '<span style="cursor:pointer;" onclick="window.open(\'' + imgSrc + '\', \'_blank\')"><i class="fa fa-file-pdf text-danger" style="font-size:2rem;"></i> <span class="small ms-1">PDF</span></span>';
      currentImageDiv.parentNode.appendChild(pdfIcon);
    } else {
      var pdfIcon = document.getElementById('editBannerCurrentPdf');
      pdfIcon.innerHTML = '<span style="cursor:pointer;" onclick="window.open(\'' + imgSrc + '\', \'_blank\')"><i class="fa fa-file-pdf text-danger" style="font-size:2rem;"></i> <span class="small ms-1">PDF</span></span>';
      pdfIcon.style.display = 'inline-block';
    }
  } else {
    currentImageDiv.style.display = 'none';
    // Show 'No Preview' for other file types
    if(!document.getElementById('editBannerCurrentOther')) {
      var otherSpan = document.createElement('span');
      otherSpan.id = 'editBannerCurrentOther';
      otherSpan.className = 'text-muted';
      otherSpan.innerText = 'No Preview';
      currentImageDiv.parentNode.appendChild(otherSpan);
    } else {
      var otherSpan = document.getElementById('editBannerCurrentOther');
      otherSpan.innerText = 'No Preview';
      otherSpan.style.display = 'inline-block';
    }
    if(document.getElementById('editBannerCurrentPdf')) document.getElementById('editBannerCurrentPdf').style.display = 'none';
  }

  // Hide any other preview if not needed
  if(ext !== 'pdf' && document.getElementById('editBannerCurrentPdf')) document.getElementById('editBannerCurrentPdf').style.display = 'none';
  if(['jpg','jpeg','png'].includes(ext) && document.getElementById('editBannerCurrentOther')) document.getElementById('editBannerCurrentOther').style.display = 'none';

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

<?php include_once __DIR__ . '/../includes/end.php'; ?>
