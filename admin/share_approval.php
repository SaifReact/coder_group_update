<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fetch all users
$stmt = $pdo->query("SELECT a.id, a.member_id, a.member_code, a.status, a.no_share, b.member_code, b.name_en, b.name_bn,
CASE 
    WHEN a.project_id > 1 THEN c.project_name_bn 
    ELSE 'সমিতি শেয়ার (CPSSL)'
END AS project_name_bn 
FROM share a 
JOIN members_info b ON b.id = a.member_id
LEFT JOIN project c ON a.project_id = c.id
ORDER BY a.id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Note: do not assume a single $member_id from the result set here.
// We'll determine the member_id from the submitted share id when processing POST.

// Handle status update
if ($method === 'POST' && isset($_POST['status'])) {

    $status = in_array($_POST['status'], ['A', 'I', 'R']) ? $_POST['status'] : 'I';

    // determine which share was submitted and resolve member info from it
    $submitted_share_id = (int)($_POST['user_id'] ?? 0);
    $member_id = 0;
    $member_code_from_share = null;
    if ($submitted_share_id > 0) {
        $stmtShareSingle = $pdo->prepare("SELECT * FROM share WHERE id = ? LIMIT 1");
        $stmtShareSingle->execute([$submitted_share_id]);
        $shareData = $stmtShareSingle->fetch(PDO::FETCH_ASSOC);
        if ($shareData) {
            $member_id = (int)$shareData['member_id'];
            $member_code_from_share = $shareData['member_code'] ?? null;
        }
    }

        // If approving, generate project_share rows and update member_share when project_id = 1
        if ($status === 'A') {
            // determine member_code (prefer value from share row if present)
            if (!empty($member_code_from_share)) {
                $member_code_val = $member_code_from_share;
            } else {
                $stmtMC = $pdo->prepare("SELECT member_code FROM members_info WHERE id = ? LIMIT 1");
                $stmtMC->execute([$member_id]);
                $member_code_val = $stmtMC->fetchColumn();
            }

            // find pending share entries for this member (status = 'I' or pending)
            $stmtShares = $pdo->prepare("SELECT * FROM share WHERE member_id = ? AND status = 'I'");
            $stmtShares->execute([$member_id]);
            while ($shareRow = $stmtShares->fetch(PDO::FETCH_ASSOC)) {
                $project_id = (int)($shareRow['project_id'] ?? 0);
                $addCount = (int)($shareRow['no_share'] ?? 0);
                if ($project_id === 1 && $addCount > 0) {
                    // ensure a member_project record exists for project_id = 1
                    $stmtMP = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? AND member_code = ? AND project_id = 1 LIMIT 1");
                    $stmtMP->execute([$member_id, $member_code_val]);
                    $mp = $stmtMP->fetch(PDO::FETCH_ASSOC);
                    if ($mp && !empty($mp['id'])) {
                        $member_project_id = (int)$mp['id'];
                    } else {
                        $stmtCreateMP = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, 1, 0, 0, NOW())");
                        $stmtCreateMP->execute([$member_id, $member_code_val]);
                        $member_project_id = (int)$pdo->lastInsertId();
                    }

                    // get last share sequence for project_id = 1
                    $stmtLast = $pdo->prepare("SELECT share_id FROM project_share WHERE member_id = ? ORDER BY id DESC LIMIT 1");
                    $stmtLast->execute([$member_id]);
                    $last = $stmtLast->fetch(PDO::FETCH_ASSOC);
                    $startingNumber = 1;
                    if ($last && !empty($last['share_id'])) {
                        $lastThree = substr($last['share_id'], -3);
                        if (is_numeric($lastThree)) {
                            $startingNumber = intval($lastThree) + 1;
                        }
                    }
                    

                    // insert project_share rows
                    $stmtInsert = $pdo->prepare("INSERT INTO project_share (member_project_id, member_id, member_code, project_id, share_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    for ($i = 0; $i < $addCount; $i++) {
                        $num = $startingNumber + $i;
                        $n = str_pad($num, 3, '0', STR_PAD_LEFT);
                        $share_id = "samity" . $member_id . $member_project_id . "1" . $n;
                        $stmtInsert->execute([$member_project_id, $member_id, $member_code_val, 1, $share_id]);
                    }

                    // update member_share totals
                    $stmtUpdateMS = $pdo->prepare("UPDATE member_share SET no_share = no_share + ?, samity_share = samity_share + ? WHERE member_id = ? AND member_code = ?");
                    $stmtUpdateMS->execute([$addCount, $addCount, $member_id, $member_code_val]);

                    // mark this share row as approved
                    $stmtMark = $pdo->prepare("UPDATE share SET status = 'A' WHERE id = ?");
                    $stmtMark->execute([$shareRow['id']]);
                } elseif ($project_id > 1 && $addCount > 0) {
                    // For other projects, just mark as approved
                    $stmtMark = $pdo->prepare("UPDATE share SET status = 'A' WHERE id = ?");
                    $stmtMark->execute([$shareRow['id']]);
                }   
            }
        }
    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ ডকুমেন্টস ও শেয়ার ফি জমা দেয়ায় আপনাকে ধন্যবাদ, আপনে আমাদের সক্রিয় সদস্য!";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ সমিতিতে আপনার কার্যক্রম সন্দেহাতীত হওয়ায় আপনাকে নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ সমিতির নীতিমালা ভঙ্গ করায় আপনার সদস্যপদ বাতিল করা হইলো !";
    }

    // Stay on the same page with success message
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
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
                    <h3 class="mb-3 text-primary fw-bold">Member Approval <span class="text-secondary">( সদস্য অনুমোদন )</span></h3> 
                    <hr class="mb-4" />
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>সদস্য নং </th>
                                        <th>সদস্য কোড </th>
                                        <th>সদস্যের নাম</th>
                                        <th>প্রকল্পের নাম</th>
                                        <th>শেয়ার সংখ্যা</th>
                                        <th colspan="2">অবস্থা</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['id']) ?></td>
                                        <td><?= htmlspecialchars($user['member_code']) ?></td>
                                        <td><?= htmlspecialchars($user['name_bn']) ?><br/>
                                            <?= htmlspecialchars($user['name_en']) ?></td>
                                        <td><?= htmlspecialchars($user['project_name_bn']) ?></td>
                                        <td><?= htmlspecialchars($user['no_share']) ?></td>
                                        <td>
                                            <form method="post" class="d-flex align-items-center">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <select name="status" class="form-select form-select-sm me-2">
                                                    <option value="A" <?= $user['status'] === 'A' ? 'selected' : '' ?>>✅ Approved</option>
                                                    <option value="I" <?= $user['status'] === 'I' ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                    <option value="R" <?= $user['status'] === 'R' ? 'selected' : '' ?>>❌ Rejected</option>

                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">Update (হালনাগাদ)</button>
                                            </form>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm view-member-btn" 
                                                data-user-id="<?= htmlspecialchars($user['id']) ?>" 
                                                title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- View Member Modal and other includes remain unchanged -->
            <?php include 'view_member.php'; ?>
        </div>
    </main>
  </div>
</div>
<!-- Hero End -->

<script>
// Handle view icon click
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-member-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var modalBody = document.getElementById('viewMemberModalBody');
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
            var modal = new bootstrap.Modal(document.getElementById('viewMemberModal'));
            modal.show();
            // Fetch details via AJAX
            fetch('member_details.php?id=' + encodeURIComponent(userId))
                .then(resp => resp.text())
                .then(html => { modalBody.innerHTML = html; })
                .catch(() => { modalBody.innerHTML = '<div class="alert alert-danger">Could not load details.</div>'; });
        });
    });
});
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>


