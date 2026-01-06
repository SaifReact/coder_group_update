<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = 'A';
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO expense_category (name, description, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $status]);
        $_SESSION['success_msg'] = 'Expense category added successfully!';
        header('Location: expense_category.php');
        exit;
    } else {
        $_SESSION['error_msg'] = 'Name is required!';
    }
}

$stmt = $pdo->prepare("SELECT * FROM expense_category ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Expense Category <span class="text-secondary">(ব্যয় ক্যাটাগরি)</span></h3>
                <hr class="mb-4" />
                <form method="post" enctype="multipart/form-data" action="../process/expense_category_process.php" class="mb-4">
                    <input type="hidden" name="action" value="insert">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">Save Category (ক্যাটাগরি সংরক্ষণ করুন)</button>
                        </div>
                    </div>
                </form>
                <hr class="my-4" />
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>

                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Status</th>
                                                                <th>Actions</th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <?php foreach ($categories as $cat): ?>
                                                                <tr>
                                                                        <td><?= $cat['id']; ?></td>
                                                                        <td><?= htmlspecialchars($cat['name']); ?></td>
                                                                        <td><?= htmlspecialchars($cat['description']); ?></td>
                                                                        <td><?= $cat['status']; ?></td>
                                                                        <td>
                                                                                <form action="../process/expense_category_process.php" method="post" style="display:inline-block;">
                                                                                        <input type="hidden" name="id" value="<?= $cat['id']; ?>">
                                                                                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This Category?');">
                                                                                                <i class="fa fa-trash"></i>
                                                                                        </button>
                                                                                </form>
                                                                                <button type="button" class="btn btn-info btn-sm" onclick='editExpenseCategory(
                                                                                        <?= (int)$cat["id"]; ?>,
                                                                                        <?= json_encode($cat["name"]); ?>,
                                                                                        <?= json_encode($cat["description"]); ?>,
                                                                                        <?= json_encode($cat["status"]); ?>
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

        <!-- Edit Expense Category Modal -->
        <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="../process/expense_category_process.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit_id">
                            <div class="row">
                                <div class="col-12 col-md-12 mb-3">
                                    <label for="edit_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="edit_name" required>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label for="edit_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_description" name="edit_description" rows="3"></textarea>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-control" id="edit_status" name="edit_status">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="action" value="update" class="btn btn-primary">Update Category (ক্যাটাগরি হালনাগাদ করুন)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</main>
</div>  
</div>

<script>
function editExpenseCategory(id, name, description, status) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_status').value = status;
        
        if (editors['#edit_description']) {
        editors['#edit_description'].setData(description);
    } else {
        document.getElementById('edit_description').value = description;
    }
        var modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
        modal.show();
}
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
<script>
const editors = {}; // store editors globally

['#description', '#edit_description'].forEach(selector => {
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

<?php include_once __DIR__ . '/../includes/toast.php'; ?>
<?php include_once __DIR__ . '/../includes/end.php'; ?>
