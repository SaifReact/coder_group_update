<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg']   ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

$stmt = $pdo->query("SELECT * FROM project ORDER BY id ASC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Project <span class="text-secondary">( প্রকল্প )</span></h3>
                <hr class="mb-4" />

                <?php if ($success_msg): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($success_msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error_msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Insert Form -->
                <form method="post" enctype="multipart/form-data" action="../process/project_process.php">
                    <input type="hidden" name="action" value="insert">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Project Name ( প্রকল্প নাম - বাংলা )</label>
                            <input type="text" class="form-control" name="project_name_bn" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Project Name ( প্রকল্প নাম - ইংরেজি )</label>
                            <input type="text" class="form-control" name="project_name_en" required>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Project Value ( প্রকল্প মূল্য )</label>
                            <input type="text" class="form-control" name="project_value" required>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Project Share ( প্রকল্প শেয়ার )</label>
                            <input type="text" class="form-control" name="project_share" required>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Per Share Value ( প্রতি শেয়ার মূল্য )</label>
                            <input type="text" class="form-control" name="per_share_value" required>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-12 col-md-12 mb-3">
                            <label class="form-label">Project Image ( প্রকল্পের ছবি )</label>
                            <input type="file" class="form-control" name="project_image"
                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                   onchange="previewImage(this, 'insertPreview')">
                            <div class="form-text">JPG, PNG, GIF, WEBP — সর্বোচ্চ ৫MB</div>
                            <div class="mt-2">
                                <img id="insertPreview" src="#" alt="Preview"
                                     style="display:none; max-height:140px; border-radius:8px; border:1px solid #dee2e6;">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <label class="form-label">About Project ( প্রকল্প সম্পর্কে )</label>
                            <textarea class="form-control" id="about_project" name="about_project" rows="10"></textarea>
                        </div>
                        <div class="col-12 mt-2 text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                Save Project (প্রকল্প সংরক্ষণ করুন)
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-4" />

                <!-- Project Table -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">নং</th>
                                <th width="10%">ছবি</th>
                                <th width="20%">প্রকল্প নাম</th>
                                <th width="10%">মূল্য</th>
                                <th width="10%">শেয়ার</th>
                                <th width="10%">প্রতি শেয়ার</th>
                                <th width="26%">প্রকল্প সম্পর্কে</th>
                                <th width="10%" class="text-center">কর্মকান্ড</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?= $project['id'] ?></td>
                                <td class="text-center">
                                    <?php if (!empty($project['project_image'])): ?>
                                        <img src="../<?= htmlspecialchars($project['project_image']) ?>"
                                             alt="project"
                                             style="height:60px; width:80px; object-fit:cover; border-radius:6px; border:1px solid #dee2e6;">
                                    <?php else: ?>
                                        <span class="text-muted small">ছবি নেই</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($project['project_name_bn']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($project['project_name_en']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($project['project_value']) ?></td>
                                <td><?= htmlspecialchars($project['project_share']) ?></td>
                                <td><?= htmlspecialchars($project['per_share_value']) ?></td>
                                <td><?= strip_tags($project['about_project'], '<p><ul><li><b><i><br>') ?></td>
                                <td class="text-center">
                                    <form action="../process/project_process.php" method="post" style="display:inline-block;">
                                        <input type="hidden" name="id"     value="<?= $project['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this project?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-info btn-sm"
                                            onclick='editProject(
                                                <?= (int)$project["id"] ?>,
                                                <?= json_encode($project["project_name_en"]) ?>,
                                                <?= json_encode($project["project_name_bn"]) ?>,
                                                <?= json_encode($project["project_value"]) ?>,
                                                <?= json_encode($project["project_share"]) ?>,
                                                <?= json_encode($project["per_share_value"]) ?>,
                                                <?= json_encode($project["about_project"]) ?>,
                                                <?= json_encode($project["project_image"] ?? "") ?>
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

        <!-- Edit Modal -->
        <div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="../process/project_process.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Project ( প্রকল্প সম্পাদনা )</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit_id">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Project Name ( বাংলা )</label>
                                    <input type="text" class="form-control" id="edit_project_name_bn" name="edit_project_name_bn" required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Project Name ( English )</label>
                                    <input type="text" class="form-control" id="edit_project_name_en" name="edit_project_name_en" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label class="form-label">Project Value</label>
                                    <input type="text" class="form-control" id="edit_project_value" name="edit_project_value" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label class="form-label">Project Share</label>
                                    <input type="text" class="form-control" id="edit_project_share" name="edit_project_share" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label class="form-label">Per Share Value</label>
                                    <input type="text" class="form-control" id="edit_per_share_value" name="edit_per_share_value" required>
                                </div>

                                <!-- Image Upload -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Project Image ( প্রকল্পের ছবি )</label>
                                    <div class="mb-2" id="editCurrentImgWrap">
                                        <p class="form-text mb-1">বর্তমান ছবি:</p>
                                        <img id="editCurrentImg" src="#" alt="current"
                                             style="max-height:120px; border-radius:8px; border:1px solid #dee2e6; display:none;">
                                        <span id="editNoImg" class="text-muted small">ছবি নেই</span>
                                    </div>
                                    <input type="file" class="form-control" name="project_image"
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           onchange="previewImage(this, 'editNewPreview')">
                                    <div class="form-text">নতুন ছবি বেছে নিলে পুরনো ছবি মুছে যাবে। খালি রাখলে পুরনো ছবি থাকবে।</div>
                                    <div class="mt-2">
                                        <img id="editNewPreview" src="#" alt="New Preview"
                                             style="display:none; max-height:120px; border-radius:8px; border:1px solid #dee2e6;">
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">About Project ( প্রকল্প সম্পর্কে )</label>
                                    <textarea class="form-control" id="edit_about_project" name="edit_about_project" rows="8"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                            <button type="submit" class="btn btn-primary">Update Project (হালনাগাদ করুন)</button>
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
const editors = {};

['#about_project', '#edit_about_project'].forEach(function(selector) {
    var el = document.querySelector(selector);
    if (el) {
        ClassicEditor.create(el, {
            toolbar: ['bold','italic','underline','link','bulletedList','numberedList','|','fontSize','undo','redo'],
            fontSize: { options: [9,11,13,'default',17,19,21] }
        }).then(function(editor) {
            editors[selector] = editor;
        }).catch(console.error);
    }
});

function previewImage(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function editProject(id, name_en, name_bn, value, share, per_share, about, image) {
    document.getElementById('edit_id').value              = id;
    document.getElementById('edit_project_name_en').value = name_en;
    document.getElementById('edit_project_name_bn').value = name_bn;
    document.getElementById('edit_project_value').value   = value;
    document.getElementById('edit_project_share').value   = share;
    document.getElementById('edit_per_share_value').value = per_share;

    if (editors['#edit_about_project']) {
        editors['#edit_about_project'].setData(about);
    }

    // Show current image
    var curImg  = document.getElementById('editCurrentImg');
    var noImg   = document.getElementById('editNoImg');
    var newPrev = document.getElementById('editNewPreview');
    newPrev.style.display = 'none';
    newPrev.src = '#';

    if (image) {
        curImg.src = '../' + image;
        curImg.style.display = 'block';
        noImg.style.display  = 'none';
    } else {
        curImg.style.display = 'none';
        noImg.style.display  = 'inline';
    }

    new bootstrap.Modal(document.getElementById('editProjectModal')).show();
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
