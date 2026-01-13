<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$member_code = $_SESSION['member_code'];

// Fetch all projects
$stmt_projects = $pdo->query("SELECT id, project_name_bn, project_name_en FROM project ORDER BY id DESC");
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

$stmt_committee_role = $pdo->prepare("SELECT * FROM committee_member WHERE member_id = ? AND role = ? LIMIT 1");
$stmt_committee_role->execute([$member_id, 'Entrepreneur']);
$committee_role = $stmt_committee_role->fetchAll(PDO::FETCH_ASSOC);

// Fetch samity share
$stmt_share = $pdo->prepare("SELECT samity_share FROM member_share WHERE member_id = ?");
$stmt_share->execute([$member_id]);
$samity_share = $stmt_share->fetchColumn();
if ($samity_share === false) $samity_share = 0;

// Fetch project shares
$stmt_member_projects = $pdo->prepare("SELECT mp.project_id, mp.project_share, p.project_name_bn FROM member_project mp JOIN project p ON mp.project_id = p.id WHERE mp.member_id = ?");
$stmt_member_projects->execute([$member_id]);
$member_projects = $stmt_member_projects->fetchAll(PDO::FETCH_ASSOC);

$shares = [];
$stmt_share = $pdo->prepare("
    SELECT a.*, 
           CASE 
               WHEN a.project_id > 0 THEN b.project_name_bn 
               ELSE 'সমিতি শেয়ার (CPSSL)'
           END AS project_name_bn
    FROM share a
    LEFT JOIN project b ON a.project_id = b.id
    WHERE a.member_id = ?
");
$stmt_share->execute([$member_id]);
if ($stmt_share) {
    $shares = $stmt_share->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submission (CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'] ?? '';
    $share_type = $_POST['share_type'] ?? '';
    $share_amount = intval($_POST['share_amount'] ?? 0);
    $errors = [];

    if (!$share_type) {
        $errors[] = 'শেয়ার টাইপ নির্বাচন করুন (Select share type)';
    }
    if ($share_amount <= 0) {
        $errors[] = 'শেয়ার সংখ্যা সঠিকভাবে দিন (Enter valid share amount)';
    }
    if ($share_type === 'project' && !$project_id) {
        $errors[] = 'প্রকল্প নির্বাচন করুন (Select project)';
    }

    if (empty($errors)) {
        if ($share_type === 'samity') {
            // Update samity_share
            $stmt = $pdo->prepare("UPDATE member_share SET samity_share = samity_share + ? WHERE member_id = ?");
            $stmt->execute([$share_amount, $member_id]);
        } elseif ($share_type === 'project') {
            // Update or insert project share
            $stmt = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? AND project_id = ?");
            $stmt->execute([$member_id, $project_id]);
            $exists = $stmt->fetchColumn();
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE member_project SET project_share = project_share + ? WHERE member_id = ? AND project_id = ?");
                $stmt->execute([$share_amount, $member_id, $project_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share) VALUES (?, ?, ?, ?)");
                $stmt->execute([$member_id, $member_code, $project_id, $share_amount]);
            }
        }
        $success = 'শেয়ার সফলভাবে যোগ হয়েছে (Share added successfully)';
        // Refresh data
        header('Location: add_share.php?success=1');
        exit;
    }
}

if (isset($_GET['success'])) {
    $success = 'শেয়ার সফলভাবে যোগ হয়েছে (Share added successfully)';
}
?>
<?php 
include_once __DIR__ . '/../includes/open.php'; 
include_once __DIR__ . '/../includes/side_bar.php'; 
?>
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Add Share <span class="text-secondary">( শেয়ার যোগ করুন )</span></h3>
                <hr class="mb-4" />
                <form method="post" action="../process/share_process.php" autocomplete="off">
                        <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($_SESSION['member_id']); ?>">
                        <input type="hidden" name="member_code" value="<?php echo htmlspecialchars($_SESSION['member_code']); ?>">
                        <input type="hidden" name="findProject" id="findProject" value="<?php echo isset($project_id) ? htmlspecialchars($project_id) : '0'; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">শেয়ার টাইপ নির্বাচন করুন (Select Share Type)</label>
                            <select class="form-select" name="share_type" id="share_type" required>
                                <option value="">নির্বাচন করুন (Select)</option>
                                <?php if (!empty($committee_role) && isset($committee_role[0]['role']) && $committee_role[0]['role'] === 'Entrepreneur'): ?>
                                    <option value="samity">সমিতি শেয়ার (CPSSL)</option>
                                <?php endif; ?>
                                <option value="project">প্রকল্প শেয়ার (Project Share)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="projectSelectBox" style="display:none;">
                            <label class="form-label">প্রকল্প নির্বাচন করুন (Select Project)</label>
                            <select class="form-select" name="project_id" id="project_id">
                                <option value="">প্রকল্প নির্বাচন করুন (Select Project)</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?php echo htmlspecialchars($project['id']); ?>">
                                        <?php echo htmlspecialchars($project['project_name_bn']) . ' - ' . htmlspecialchars($project['project_name_en']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">সমিতি শেয়ার (CPSSL Share)</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($samity_share); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">প্রকল্প শেয়ার (Project Shares)</label>
                            <ul class="list-group">
                                <?php if (empty($member_projects)): ?>
                                    <li class="list-group-item">কোনো প্রকল্প শেয়ার নেই (No project shares)</li>
                                <?php else: ?>
                                    <?php foreach ($member_projects as $mp): ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($mp['project_name_bn']); ?>: <?php echo htmlspecialchars($mp['project_share']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">নতুন শেয়ার সংখ্যা (New Share Amount)</label>
                            <input type="number" class="form-control" name="share_amount" min="1" value="" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3" id="projectInfoBox" style="display:none;"></div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">Add Share (শেয়ার যোগ করুন)</button>
                        </div>
                    </div>
                </form>
                <hr class="my-4" />
                <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Project Name</th>
                                        <th>No Share</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($shares as $share): ?>
                                    <tr>
                                        <td><?= $share['id']; ?></td>
                                        <td><?= htmlspecialchars($share['type']); ?> </td>
                                        <td><?= htmlspecialchars($share['project_name_bn']); ?></td>
                                        <td><?= htmlspecialchars($share['no_share']); ?></td>
                                        <td>
                                            <?php
                                                if ($share['status'] == 'I') {
                                                    echo 'অপেক্ষমান';
                                                } elseif ($share['status'] == 'A') {
                                                    echo 'অনুমোদিত';
                                                } elseif ($share['status'] == 'R') {
                                                    echo 'বাতিল';
                                                } else {
                                                    echo htmlspecialchars($share['status']);
                                                }
                                            ?>
                                        </td>
                                        <td>
                                        <?php if ($share['status'] != 'A'): ?>
                                            <form action="../process/share_process.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?= $share['id']; ?>">
                                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This Service?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                           <button type="button" class="btn btn-info btn-sm"
                                                onclick='editService(
                                                    <?= (int)$share["id"]; ?>,
                                                    <?= json_encode($share["type"]); ?>,
                                                    <?= json_encode($share["project_id"]); ?>,
                                                    <?= json_encode($share["no_share"]); ?>,
                                                    <?= json_encode($share["status"]); ?>
                                                )'>
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var projectSelect = document.getElementById('project_id');
    var findProjectInput = document.getElementById('findProject');
    var infoBox = document.getElementById('projectInfoBox');
    projectSelect.addEventListener('change', function() {
        var pid = this.value;
        if (findProjectInput) {
            findProjectInput.value = pid ? pid : '0';
        }
        if (!infoBox) return;
        if (!pid) {
            infoBox.style.display = 'none';
            infoBox.innerHTML = '';
            return;
        }
        fetch('project_info.php?project_id=' + encodeURIComponent(pid))
            .then(r => r.json())
            .then(function(data) {
                if (data.error) {
                    infoBox.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                    infoBox.style.display = '';
                    return;
                }
                var html = '<table class="table table-bordered table-sm">';
                html += '<tr><th>Project Name (BN)</th><td>' + (data.project_name_bn || '') + '</td></tr>';
                html += '<tr><th>Project Name (EN)</th><td>' + (data.project_name_en || '') + '</td></tr>';
                html += '<tr><th>About Project</th><td>' + (data.about_project || '') + '</td></tr>';
                html += '<tr><th>Project Value (TK)</th><td>' + (data.project_value || '') + '</td></tr>';
                html += '<tr><th>No of Project Share</th><td>' + (data.project_share || '') + '</td></tr>';
                html += '<tr><th>Per Share Value (TK)</th><td>' + (data.per_share_value || '') + '</td></tr>';
                html += '</table>';
                infoBox.innerHTML = html;
                infoBox.style.display = '';
            })
            .catch(function() {
                infoBox.innerHTML = '<div class="alert alert-danger">Could not load project info.</div>';
                infoBox.style.display = '';
            });
    });

    // Edit modal logic
    window.editService = function(id, type, project_id, no_share, status) {
        document.getElementById('editShareId').value = id;
        document.getElementById('editShareType').value = type;
        document.getElementById('editShareProjectId').value = project_id;
        document.getElementById('editShareNoShare').value = no_share;
        document.getElementById('editShareStatus').value = status;
        var modal = new bootstrap.Modal(document.getElementById('editShareModal'));
        modal.show();
    }
});
</script>
            </div>
        </div>
    </div>
</main>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
        var shareType = document.getElementById('share_type');
        var projectBox = document.getElementById('projectSelectBox');
        shareType.addEventListener('change', function() {
                if (this.value === 'project') {
                        projectBox.style.display = '';
                } else {
                        projectBox.style.display = 'none';
                }
        });
});

// Modal HTML
document.body.insertAdjacentHTML('beforeend', `
<div class="modal fade" id="editShareModal" tabindex="-1" aria-labelledby="editShareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="../process/share_process.php" autocomplete="off">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="editShareModalLabel">শেয়ার সম্পাদনা করুন (Edit Share)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editShareId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">শেয়ার টাইপ নির্বাচন করুন (Select Share Type)</label>
                            <select class="form-select" name="type" id="editShareType" required>
                                <option value="">নির্বাচন করুন (Select)</option>
                                <option value="samity">সমিতি শেয়ার (CPSSL)</option>
                                <option value="project">প্রকল্প শেয়ার (Project Share)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="editProjectSelectBox">
                            <label class="form-label">প্রকল্প নির্বাচন করুন (Select Project)</label>
                            <select class="form-select" name="project_id" id="editShareProjectId">
                                <option value="">প্রকল্প নির্বাচন করুন (Select Project)</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?php echo htmlspecialchars($project['id']); ?>">
                                        <?php echo htmlspecialchars($project['project_name_bn']) . ' - ' . htmlspecialchars($project['project_name_en']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">শেয়ার সংখ্যা</label>
                            <input type="number" class="form-control" name="no_share" id="editShareNoShare" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">স্ট্যাটাস</label>
                            <select class="form-select" name="status" id="editShareStatus">
                                <option value="I">অপেক্ষমান</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="update" class="btn btn-info">হালনাগাদ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
`);

// Show/hide project select in modal
document.addEventListener('DOMContentLoaded', function() {
    var editType = document.getElementById('editShareType');
    var editProjectBox = document.getElementById('editProjectSelectBox');
    if (editType && editProjectBox) {
        editType.addEventListener('change', function() {
            if (this.value === 'project') {
                editProjectBox.style.display = '';
            } else {
                editProjectBox.style.display = 'none';
            }
        });
    }
});
</script>
<?php include_once __DIR__ . '/../includes/end.php'; ?>
