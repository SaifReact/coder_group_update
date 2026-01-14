<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fetch all users
$stmt = $pdo->query("SELECT a.id, a.member_id, a.status, a.no_share, b.member_code, b.name_en, b.name_bn,
CASE 
    WHEN a.project_id > 0 THEN c.project_name_bn 
    ELSE 'সমিতি শেয়ার (CPSSL)'
END AS project_name_bn 
FROM share a 
JOIN members_info b ON b.id = a.member_id
LEFT JOIN project c ON a.project_id = c.id
ORDER BY a.id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($method === 'POST' && isset($_POST['user_id'], $_POST['status'])) {
    $user_id = (int)$_POST['user_id'];
    $status = in_array($_POST['status'], ['P', 'A', 'I', 'R']) ? $_POST['status'] : 'I';
    
    // Get member_id from user_login table
    // find member_project table getting member_id
    
    $stmt = $pdo->prepare("SELECT member_id FROM user_login WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $member_id = $user_data['member_id'];
    
    // Check if status is 'A' (Approved), verify all 4 documents exist
    if ($status === 'A') {
        $stmt = $pdo->prepare("SELECT COUNT(*) as doc_count FROM member_documents WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $doc_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $doc_count = (int)$doc_data['doc_count'];
        
        if ($doc_count < 3) {
            $_SESSION['error_msg'] = '❌ সদস্যকে অনুমোদন করা যাবে না! সকল ডকুমেন্ট জমা দেওয়া হয়নি। (মোট ' . $doc_count . '/3 টি ছবি পাওয়া গেছে)';
            // Stay on the same page
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE user_login SET status = ? WHERE id = ?");
    $stmt->execute([$status, $user_id]);

    // Set dynamic success message based on status
    if ($status === 'P') {
        $_SESSION['success_msg'] = "✅ ডকুমেন্টস ও মেম্বারশিপ ফি জমা দেয়ার জন্য আপনাকে প্রক্রিয়াধীন রাখা হইলো!";
    } elseif ($status === 'A') {
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
                                                    <option value="P" <?= $user['status'] === 'P' ? 'selected' : '' ?>>⏳ Processing</option>
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


