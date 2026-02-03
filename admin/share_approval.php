<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fetch all users
$stmt = $pdo->query("SELECT a.id, a.member_id, a.member_code, a.status, a.project_id, a.no_share, b.member_code, b.name_en, b.name_bn,
CASE 
    WHEN a.project_id > 1 THEN c.project_name_bn 
    ELSE 'সমিতি শেয়ার (CPSSL)'
END AS project_name_bn 
FROM share a 
JOIN members_info b ON b.id = a.member_id
LEFT JOIN project c ON a.project_id = c.id
ORDER BY a.id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load utils fees for samity and project shares
$stmtUtils = $pdo->query("SELECT fee_type, fee FROM utils WHERE fee_type IN ('samity_share','project_share') AND status = 'A' ORDER BY id ASC");
$utilsRows = $stmtUtils->fetchAll(PDO::FETCH_ASSOC);
$samity_share_value_utils = 0.0;
$project_share_value_utils = 0.0;
foreach ($utilsRows as $u) {
    if (isset($u['fee_type']) && isset($u['fee'])) {
        if ($u['fee_type'] === 'samity_share') {
            $samity_share_value_utils = (float)$u['fee'];
        } elseif ($u['fee_type'] === 'project_share') {
            $project_share_value_utils = (float)$u['fee'];
        }
    }
}

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

    // If the requested status is not approval, just update that single share row and skip approval processing.
    if ($submitted_share_id > 0 && $status !== 'A') {
        $stmtSimpleMark = $pdo->prepare("UPDATE share SET status = ? WHERE id = ?");
        $stmtSimpleMark->execute([$status, $submitted_share_id]);
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
            $stmtShares = $pdo->prepare("SELECT * FROM share WHERE member_id = ? AND status != 'A'");
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
                    $stmtLast = $pdo->prepare("SELECT share_id FROM project_share WHERE member_id = ? AND project_id = ? ORDER BY id DESC LIMIT 1");
                    $stmtLast->execute([$member_id, $project_id]);
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

                    $sundrySamityAmt = $addCount * $samity_share_value_utils;

                    // update member_share totals
                    $stmtUpdateMS = $pdo->prepare("UPDATE member_share SET no_share = no_share + ?, samity_share = samity_share + ?, sundry_samity_share = sundry_samity_share + ? WHERE member_id = ? AND member_code = ?");
                    $stmtUpdateMS->execute([$addCount, $addCount, $sundrySamityAmt, $member_id, $member_code_val]);
                    // mark this share row as approved
                    $stmtMark = $pdo->prepare("UPDATE share SET status = 'A' WHERE id = ?");
                    $stmtMark->execute([$shareRow['id']]);
                } elseif ($project_id > 1 && $addCount > 0) {
                    // Find or create member_project for this project, then find last share sequence
                    $stmtMP = $pdo->prepare("SELECT id FROM member_project WHERE member_id = ? AND project_id = ? LIMIT 1");
                    $stmtMP->execute([$member_id, $project_id]);
                    $mp = $stmtMP->fetch(PDO::FETCH_ASSOC);

                    if ($mp && !empty($mp['id'])) {
                        $member_project_id = (int)$mp['id'];
                    } else {
                        $stmtCreateMP = $pdo->prepare("INSERT INTO member_project (member_id, member_code, project_id, project_share, share_amount, created_at) VALUES (?, ?, ?, 0, 0, NOW())");
                        $stmtCreateMP->execute([$member_id, $member_code_val, $project_id]);
                        $member_project_id = (int)$pdo->lastInsertId();
                    }

                    // get last share sequence for this project
                    $stmtLast = $pdo->prepare("SELECT share_id FROM project_share WHERE member_id = ? AND project_id = ? ORDER BY id DESC LIMIT 1");
                    $stmtLast->execute([$member_id, $project_id]);
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
                        $share_id = "share" . $member_id . $member_project_id . $project_id . $n;
                        $stmtInsert->execute([$member_project_id, $member_id, $member_code_val, $project_id, $share_id]);
                    }

                    $shareAmt = $addCount * $project_share_value_utils;

                    // update member_project share count
                    $stmtUpdateMP = $pdo->prepare("UPDATE member_project SET share_amount = share_amount + ?, project_share = project_share + ?, sundry_amount = sundry_amount + ? WHERE id = ? AND member_id = ?");
                    $stmtUpdateMP->execute([$shareAmt, $addCount, $shareAmt, $member_project_id, $member_id]);    


                    // update member_share totals
                    $stmtUpdateMS = $pdo->prepare("UPDATE member_share SET no_share = no_share + ?, extra_share = extra_share + ? WHERE member_id = ? AND member_code = ?");
                    $stmtUpdateMS->execute([$addCount, $addCount, $member_id, $member_code_val]);

                    // mark this share row as approved
                    $stmtMark = $pdo->prepare("UPDATE share SET status = 'A' WHERE id = ?");
                    $stmtMark->execute([$shareRow['id']]);
                }   
            }
        }
    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ শেয়ারের অনুমোদন দেয়া হলো !";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ সমিতিতে আপনার কার্যক্রম সন্দেহাতীত হওয়ায় শেয়ার নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ সমিতির নীতিমালা ভঙ্গ করায় শেয়ার বাতিল করা হইলো !";
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
                                        <th>নং </th>
                                        <th>সদস্য কোড </th>
                                        <th>সদস্যের নাম</th>
                                        <th>প্রকল্পের নাম</th>
                                        <th>শেয়ার সংখ্যা</th>
                                        <th colspan="1">অবস্থা</th>
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
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </main>
  </div>
</div>
<!-- Hero End -->

<?php include_once __DIR__ . '/../includes/end.php'; ?>


