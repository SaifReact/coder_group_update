<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$stmt = $pdo->query("SELECT * FROM project ORDER BY id ASC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once __DIR__ . '/../includes/open.php'; ?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
      <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
            <div class="container">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Project <span class="text-secondary">( প্রকল্প )</span></h3> 
                      <hr class="mb-4" />

                        <form method="post" enctype="multipart/form-data" action="../process/project_process.php">
                            <input type="hidden" name="action" value="insert">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="project_name_bn" class="form-label">Project Name (Bangla)</label>
                                    <input type="text" class="form-control" id="project_name_bn" name="project_name_bn" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="project_name_en" class="form-label">Project Name (English)</label>
                                    <input type="text" class="form-control" id="project_name_en" name="project_name_en" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="project_value" class="form-label">Project Value</label>
                                    <input type="text" class="form-control" id="project_value" name="project_value" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="project_share" class="form-label">Project Share</label>
                                    <input type="text" class="form-control" id="project_share" name="project_share" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="per_share_value" class="form-label">Per Share Value</label>
                                    <input type="text" class="form-control" id="per_share_value" name="per_share_value" required>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label for="about_project" class="form-label">About Service</label>
                                    <textarea class="form-control" id="about_project" name="about_project" rows="10"></textarea>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                        Save Project (প্রকল্প সংরক্ষণ করুন)
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
                                        <th>Project Name</th>
                                        <th>Project Value</th>
                                        <th>Project Share</th>
                                        <th>Per Share Value</th>
                                        <th>About Project</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><?= $project['id']; ?></td>
                                        <td><?= htmlspecialchars($project['project_name_en']); ?> <br>
                                            <?= htmlspecialchars($project['project_name_bn']); ?></td>
                                        <td><?= htmlspecialchars($project['project_value']); ?></td>
                                        <td><?= htmlspecialchars($project['project_share']); ?></td>
                                        <td><?= htmlspecialchars($project['per_share_value']); ?></td>
                                        <td><?= strip_tags($project['about_project'], '<p><ul><li><b><i><br>'); ?></td>
                                        <td>
                                            <form action="../process/project_process.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?= $project['id']; ?>">
                                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This Service?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                           <button type="button" class="btn btn-info btn-sm"
                                                onclick='editProject(
                                                    <?= (int)$project["id"]; ?>,
                                                    <?= json_encode($project["project_name_en"]); ?>,
                                                    <?= json_encode($project["project_name_bn"]); ?>,
                                                    <?= json_encode($project["project_value"]); ?>,
                                                    <?= json_encode($project["project_share"]); ?>,
                                                    <?= json_encode($project["per_share_value"]); ?>,
                                                    <?= json_encode($project["about_project"]); ?>
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
                <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="../process/project_process.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editServiceModalLabel">Edit Project</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" id="edit_id">
                          <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_project_name_bn" class="form-label">Project Name (Bangla)</label>
                                    <input type="text" class="form-control" id="edit_project_name_bn" name="edit_project_name_bn" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_project_name_en" class="form-label">Project Name (English)</label>
                                    <input type="text" class="form-control" id="edit_project_name_en" name="edit_project_name_en" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_project_value" class="form-label">Project Value</label>
                                    <input type="text" class="form-control" id="edit_project_value" name="edit_project_value" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_project_share" class="form-label">Project Share</label>
                                    <input type="text" class="form-control" id="edit_project_share" name="edit_project_share" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="edit_per_share_value" class="form-label">Per Share Value</label>
                                    <input type="text" class="form-control" id="edit_per_share_value" name="edit_per_share_value" required>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label for="edit_about_project" class="form-label">About Project</label>
                                    <textarea class="form-control" id="edit_about_project" name="edit_about_project" rows="10"></textarea>
                                </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="action" value="update" class="btn btn-primary">Update Project (প্রকল্প হালনাগাদ করুন)</button>
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

['#about_project', '#edit_about_project'].forEach(selector => {
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
function editProject(id, project_name_en, project_name_bn, project_value, project_share, per_share_value, about_project) {
      
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_project_name_en').value = project_name_en;
    document.getElementById('edit_project_name_bn').value = project_name_bn;
    document.getElementById('edit_project_value').value = project_value;
    document.getElementById('edit_project_share').value = project_share;
    document.getElementById('edit_per_share_value').value = per_share_value;
    // ✅ update CKEditor instead of textarea value
    if (editors['#edit_about_project']) {
        editors['#edit_about_project'].setData(about_project);
    }

    var modal = new bootstrap.Modal(document.getElementById('editProjectModal'));
    modal.show();
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
