<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fetch members who have entries in member_share (member-wise)
$stmt = $pdo->query("SELECT m.id as member_id, m.member_code, m.name_en, m.name_bn, m.mobile,
    COALESCE(MAX(ms.late_assign), 'I') as late_assign,
    COALESCE(MAX(ms.late_cause), '') as late_cause
    FROM members_info m
    LEFT JOIN member_share ms ON ms.member_id = m.id
    GROUP BY m.id, m.member_code, m.name_en, m.name_bn, m.mobile
    ORDER BY m.id DESC");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle late_assign update (only 'A' or 'I')
if ($method === 'POST' && isset($_POST['member_id'], $_POST['late_assign'])) {
    $member_id = (int)$_POST['member_id'];
    $late_assign = ($_POST['late_assign'] === 'A') ? 'A' : 'I';
    $late_cause = trim($_POST['late_cause'] ?? '');

    // Update all member_share rows for this member to keep member-wise consistency
    $update = $pdo->prepare("UPDATE member_share SET late_assign = ?, late_cause = ? WHERE member_id = ?");
    $update->execute([$late_assign, $late_cause, $member_id]);

    if ($update->rowCount() > 0) {
        $_SESSION['success_msg'] = ($late_assign === 'A') ? '✅ সদস্যের জন্য বিলম্ব ফি একটিভ করা হয়েছে..!' : '⚠️ সদস্যের জন্য বিলম্ব ফি নিষ্ক্রিয় করা হয়েছে..!';
    } else {
        // If no rows updated, still set message (maybe member has no shares)
        $_SESSION['error_msg'] = 'ℹ️ সদস্যের জন্য কোনো শেয়ার রেকর্ড পাওয়া যায়নি অথবা কিছু পরিবর্তন হয়নি।';
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
                    <h3 class="mb-3 text-primary fw-bold">Late Assignment Activity <span class="text-secondary">( বিলম্ব ফি কার্যকলাপ )</span></h3> 
                    <hr class="mb-4" />
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">সদস্য নং</th>
                                        <th width="15%">সদস্য কোড</th>
                                        <th width="25%">সদস্যের নাম</th>
                                        <th width="50%">বিলম্ব ফি কার্যকলাপ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($members as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['member_id']) ?></td>
                                        <td><?= htmlspecialchars($m['member_code']) ?></td>
                                        <td><?= htmlspecialchars($m['name_bn']) ?><br/><?= htmlspecialchars($m['name_en']) ?>
                                        <br/><?= htmlspecialchars($m['mobile']) ?></td>
                                        <td>
                                            <form method="post" class="d-flex align-items-center">
                                                <input type="hidden" name="member_id" value="<?= $m['member_id'] ?>">
                                                <div class="me-2" style="min-width:140px;">
                                                    <select name="late_assign" class="form-select form-select-sm">
                                                        <option value="A" <?= $m['late_assign'] === 'A' ? 'selected' : '' ?>>✅ Approved</option>
                                                        <option value="I" <?= $m['late_assign'] === 'I' ? 'selected' : '' ?>>⏸️ Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="flex-grow-1 me-2">
                                                    <textarea name="late_cause" class="form-control form-control-sm" rows="2" placeholder="Late cause/details"><?= htmlspecialchars($m['late_cause']) ?></textarea>
                                                </div>
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
