<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php'); exit;
}
include_once __DIR__ . '/../config/config.php';

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg   = $_SESSION['error_msg']   ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

$rows = $pdo->query("SELECT * FROM pcompany ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Product Company <span class="text-secondary">( পণ্য কোম্পানি )</span></h3>
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
                <form method="post" action="../process/pcompany_process.php">
                    <input type="hidden" name="action" value="insert">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Company Name (English)</label>
                            <input type="text" class="form-control" name="company_name" placeholder="e.g. ACI Limited" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">কোম্পানির নাম (বাংলা)</label>
                            <input type="text" class="form-control" name="company_name_bn" placeholder="যেমন: এসিআই লিমিটেড" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-plus"></i> যোগ করুন
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-4" />

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-success">
                            <tr>
                                <th width="6%">#</th>
                                <th>Company Name (EN)</th>
                                <th>কোম্পানির নাম (বাংলা)</th>
                                <th width="15%">তৈরির তারিখ</th>
                                <th width="12%" class="text-center">কর্মকান্ড</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($rows): ?>
                            <?php foreach ($rows as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($r['company_name']) ?></td>
                                <td><?= htmlspecialchars($r['company_name_bn']) ?></td>
                                <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-info btn-sm"
                                            onclick="editCompany(<?= $r['id'] ?>,<?= htmlspecialchars(json_encode($r['company_name']), ENT_QUOTES) ?>,<?= htmlspecialchars(json_encode($r['company_name_bn']), ENT_QUOTES) ?>)">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form method="post" action="../process/pcompany_process.php" style="display:inline-block;"
                                          onsubmit="return confirm('মুছে ফেলবেন?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted">কোনো কোম্পানি নেই।</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</main>
</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="../process/pcompany_process.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">কোম্পানি সম্পাদনা</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Company Name (English)</label>
                        <input type="text" class="form-control" name="company_name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">কোম্পানির নাম (বাংলা)</label>
                        <input type="text" class="form-control" name="company_name_bn" id="edit_name_bn" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary">হালনাগাদ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCompany(id, name, name_bn) {
    document.getElementById('edit_id').value      = id;
    document.getElementById('edit_name').value    = name;
    document.getElementById('edit_name_bn').value = name_bn;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
