<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$member_code = $_SESSION['member_code'];

$stmt = $pdo->prepare("SELECT * FROM utils WHERE fee_type = 'samity_share'");
$stmt->execute();
$utils = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get samity_share fee from utils
$samity_share_fee = 0;
foreach ($utils as $u) {
    if ($u['fee_type'] === 'samity_share') {
        $samity_share_fee = $u['fee'];
        break;
    }
}

// Fetch project shares for this member with project details
$stmt = $pdo->prepare("SELECT ps.*, p.project_name_bn, p.project_name_en, p.per_share_value 
                       FROM project_share ps 
                       LEFT JOIN project p ON ps.project_id = p.id 
                       WHERE ps.member_id = ? 
                       ORDER BY ps.project_id, ps.share_id ASC");
$stmt->execute([$member_id]);
$projectShares = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group shares by project
$groupedShares = [];
foreach ($projectShares as $share) {
    $projectId = $share['project_id'];
    if (!isset($groupedShares[$projectId])) {
        $groupedShares[$projectId] = [
            'project_name_bn' => $share['project_name_bn'],
            'project_name_en' => $share['project_name_en'],
            'per_share_value' => $share['per_share_value'],
            'shares' => []
        ];
    }
    $groupedShares[$projectId]['shares'][] = $share;
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
            <div class="row mb-3">
                      <div class="col-12 col-md-6">
                        <h3 class="mb-3 text-primary fw-bold">Project Shares <span class="text-secondary">(প্রকল্প শেয়ার)</span></h3>
                      </div>
                      <div class="col-12 col-md-6 text-md-end">
                        <a href="add_share.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> নতুন শেয়ার যোগ করুন (Add New Share)
                        </a>
                      </div>
                    </div>
            <hr class="mb-4" />
            
            <?php if (empty($projectShares)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> আপনার কোন সমিতি ও প্রকল্প শেয়ার নেই। (You don't have any Samity and Project shares yet.)
              </div>
            <?php else: ?>
              <?php foreach ($groupedShares as $projectId => $projectData): ?>
                <div class="mb-4">
                  <h5 class="text-success fw-bold mb-3">
                    <i class="fas fa-project-diagram"></i> 
                    <?php echo htmlspecialchars($projectData['project_name_bn']); ?> 
                    - <?php echo htmlspecialchars($projectData['project_name_en']); ?>
                  </h5>
                  <p class="text-muted">প্রতি শেয়ার মূল্য: ৳<?php echo number_format(($projectData['per_share_value'] > 0 ? $projectData['per_share_value'] : $samity_share_fee), 2); ?></p>
                  <p class="text-info fw-bold">মোট শেয়ার: <?php echo count($projectData['shares']); ?> টি</p>
                  
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead class="table-primary">
                        <tr>
                          <th>#</th>
                          <th>Share ID (শেয়ার আইডি)</th>
                          <th>Created Date (তৈরির তারিখ)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($projectData['shares'] as $share): 
                        ?>
                          <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><strong><?php echo htmlspecialchars($share['share_id']); ?></strong></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($share['created_at'])); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot class="table-light">
                        <tr>
                          <td colspan="2" class="text-end fw-bold">মোট মূল্য (Total Value):</td>
                          <td class="fw-bold text-success">
                            <?php
                            $single_share_value = ($projectData['per_share_value'] > 0 ? $projectData['per_share_value'] : $samity_share_fee);
                            echo '৳' . number_format(count($projectData['shares']) * $single_share_value, 2);
                            ?>
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
      </div>
    </main>
  </div>
</div>
<!-- Hero End -->

<?php include_once __DIR__ . '/../includes/end.php'; ?>
