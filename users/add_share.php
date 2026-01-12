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
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"> <?php echo $success; ?> </div>
                <?php endif; ?>
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
</script>
<?php include_once __DIR__ . '/../includes/end.php'; ?>
