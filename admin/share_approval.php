<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fetch all shares
$stmt = $pdo->query("SELECT a.id, a.member_id, a.member_code, a.status, a.project_id, a.no_share, b.name_en, b.name_bn,
CASE
    WHEN a.project_id > 1 THEN c.project_name_bn
    ELSE 'সমিতি শেয়ার (CPSSL)'
END AS project_name_bn
FROM share a
JOIN members_info b ON b.id = a.member_id
LEFT JOIN project c ON a.project_id = c.id
ORDER BY a.id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($method === 'POST' && isset($_POST['status'], $_POST['user_id'])) {
    $share_id  = (int)$_POST['user_id'];
    $status    = in_array($_POST['status'], ['A', 'I', 'R']) ? $_POST['status'] : 'I';

    // Fetch the share row to get member info and no_share count
    $stmtShare = $pdo->prepare("SELECT member_id, member_code, no_share FROM share WHERE id = ? LIMIT 1");
    $stmtShare->execute([$share_id]);
    $shareRow = $stmtShare->fetch(PDO::FETCH_ASSOC);

    if ($shareRow) {
        $member_id   = (int)$shareRow['member_id'];
        $member_code = $shareRow['member_code'];
        $no_share    = (int)$shareRow['no_share'];

        // Update share status
        $stmtUpdate = $pdo->prepare("UPDATE share SET status = ? WHERE id = ?");
        $stmtUpdate->execute([$status, $share_id]);

        // On approval: add no_share to member_share.extra_share
        if ($status === 'A' && $no_share > 0) {
            $stmtMS = $pdo->prepare("UPDATE member_share SET extra_share = extra_share + ? WHERE member_id = ? AND member_code = ?");
            $stmtMS->execute([$no_share, $member_id, $member_code]);
        }
    }

    if ($status === 'A') {
        $_SESSION['success_msg'] = "✅ শেয়ারের অনুমোদন দেয়া হলো !";
    } elseif ($status === 'I') {
        $_SESSION['success_msg'] = "⚠️ সমিতিতে আপনার কার্যক্রম সন্দেহাতীত হওয়ায় শেয়ার নিষ্ক্রিয় করে রাখা হইলো !";
    } elseif ($status === 'R') {
        $_SESSION['success_msg'] = "❌ সমিতির নীতিমালা ভঙ্গ করায় শেয়ার বাতিল করা হইলো !";
    }

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
                    <h3 class="mb-3 text-primary fw-bold">Share Approval <span class="text-secondary">( শেয়ার অনুমোদন )</span></h3>
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
                                        <th colspan="1">কর্মকান্ড</th>
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

<?php include_once __DIR__ . '/../includes/end.php'; ?>
