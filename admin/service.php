<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Service <span class="text-secondary">( সেবা )</span></h3> 
                      <hr class="mb-4" />

                        <form method="post" enctype="multipart/form-data" action="../process/service_process.php">
                            <input type="hidden" name="action" value="insert">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="service_name_bn" class="form-label">Service Name</label>
                                    <input type="text" class="form-control" id="service_name_bn" name="service_name_bn" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="service_name_en" class="form-label">Goal</label>
                                    <input type="text" class="form-control" id="service_name_en" name="service_name_en" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="about_service" class="form-label">Objectives</label>
                                    <textarea class="form-control" id="about_service" name="about_service" rows="5"></textarea>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <input type="text" class="form-control" id="icon" name="icon" required>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                        Save Service (সেবা সংরক্ষণ করুন)
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
                                        <th>Service Name</th>
                                        <th>Goal</th>
                                        <th>Objective</th>
                                        <th>Icon</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?= $service['id']; ?></td>
                                        <td><?= htmlspecialchars($service['service_name_en']); ?> </td>
                                        <td><?= htmlspecialchars($service['service_name_bn']); ?></td>
                                        <td><?= strip_tags($service['about_service'], '<p><ul><li><b><i><br>'); ?></td>
                                        <td><i class="fa <?= htmlspecialchars($service['icon']); ?>"></i></td>
                                        <td>
                                            <form action="../process/service_process.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?= $service['id']; ?>">
                                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This Service?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                           <button type="button" class="btn btn-info btn-sm"
                                                onclick='editService(
                                                    <?= (int)$service["id"]; ?>,
                                                    <?= json_encode($service["service_name_en"]); ?>,
                                                    <?= json_encode($service["service_name_bn"]); ?>,
                                                    <?= json_encode($service["about_service"]); ?>,
                                                    <?= json_encode($service["icon"]); ?>
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
                <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="../process/service_process.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="text" name="id" id="edit_id">
                          <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_service_name_bn" class="form-label">Service Name</label>
                                    <input type="text" class="form-control" id="edit_service_name_bn" name="edit_service_name_bn" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_service_name_en" class="form-label">Goal</label>
                                    <input type="text" class="form-control" id="edit_service_name_en" name="edit_service_name_en" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_about_service" class="form-label">Objectives</label>
                                    <textarea class="form-control" id="edit_about_service" name="edit_about_service" rows="5"></textarea>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_icon" class="form-label">Icon <i id="edit_icon_preview" class="fa"></i></label>
                                    <input type="text" class="form-control" id="edit_icon" name="edit_icon" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="action" value="update" class="btn btn-primary">Update Service (সেবা হালনাগাদ করুন)</button>
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

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
<script>
const editors = {}; // store editors globally

['#about_service', '#edit_about_service'].forEach(selector => {
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
function editService(id, service_name_en, service_name_bn, about_service, icon) {   
      
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_service_name_en').value = service_name_en;
    document.getElementById('edit_service_name_bn').value = service_name_bn;
    document.getElementById('edit_icon').value = icon;

    // ✅ update CKEditor instead of textarea value
    if (editors['#edit_about_service']) {
        editors['#edit_about_service'].setData(about_service);
    }

    const preview = document.getElementById('edit_icon_preview');
    if (preview) {
        preview.className = "fa " + icon;
    }

    var modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
    modal.show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../includes/toast.php'; ?>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
