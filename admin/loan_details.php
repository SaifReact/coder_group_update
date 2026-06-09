<?php
include_once __DIR__ . '/../config/config.php';

if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id'])) {
    echo '<div class="alert alert-danger">Invalid loan ID.</div>';
    exit;
}

$loan_id = (int)$_GET['loan_id'];

// Fetch loan details
$stmt = $pdo->prepare("SELECT * FROM loan_application a LEFT JOIN loan_info c ON a.product_code = c.product_code WHERE a.id = ? LIMIT 1");
$stmt->execute([$loan_id]);
$loan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$loan) {
    echo '<div class="alert alert-warning">Loan record not found.</div>';
    exit;
}

// Fetch member details
$member_id = $loan['member_id'];
$stmt = $pdo->prepare("SELECT * FROM members_info WHERE id = ? LIMIT 1");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    echo '<div class="alert alert-warning">Member info not found.</div>';
    exit;
}

// Fetch member share info
$stmt = $pdo->prepare("SELECT * FROM member_share WHERE member_id = ? LIMIT 1");
$stmt->execute([$member_id]);
$share = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch project share info
$stmt = $pdo->prepare("SELECT COALESCE(SUM(paid_amount), 0) AS project_amount FROM member_project WHERE member_id = ?  AND project_id > 1 AND status = 'A'");
$stmt->execute([$member_id,]);
$pm = $stmt->fetch();
$project_amount = $pm['project_amount'] ?? 0.0;

//Fetch Monthly Deposit info
$stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS monthly_deposit FROM member_payments WHERE member_id = ? AND status = 'A' AND payment_method = 'Monthly'");
$stmt->execute([$member_id]);
$md = $stmt->fetch();
$monthly_deposit = $md['monthly_deposit'] ?? 0.0;

// Fetch nominees
$stmt = $pdo->prepare("SELECT * FROM member_nominee WHERE member_id = ?");
$stmt->execute([$member_id]);
$nominees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row g-4">
    <!-- Member Info Column -->
    <div class="col-md-7">
      <div class="card mb-3">
        <div class="card-header"><strong>Member Information</strong></div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-12 d-flex justify-content-center align-items-center mb-2">
              <img src="../<?php echo htmlspecialchars($member['profile_image'] ?? 'assets/default.png'); ?>" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;" alt="Profile">
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Member ID & Code:</h6>
              <span><?php echo htmlspecialchars($member['id'] ?? ''); ?> - <?php echo htmlspecialchars($member['member_code'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Name (EN/BN):</h6>
              <span><?php echo htmlspecialchars($member['name_en'] ?? ''); ?> / <?php echo htmlspecialchars($member['name_bn'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">DOB:</h6>
              <span><?php echo htmlspecialchars($member['dob'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Religion:</h6>
              <span><?php echo htmlspecialchars($member['religion'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Father Name:</h6>
              <span><?php echo htmlspecialchars($member['father_name'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Mother Name:</h6>
              <span><?php echo htmlspecialchars($member['mother_name'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Mobile:</h6>
              <span><?php echo htmlspecialchars($member['mobile'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">NID:</h6>
              <span><?php echo htmlspecialchars($member['nid'] ?? ''); ?></span>
            </div>
          </div>
        </div>

        <div class="card-header"><strong>Deposit Information</strong></div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-6">
              <h6 class="text-secondary">Samity Shares:</h6>
              <span><?php echo htmlspecialchars($share['samity_share'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Samity Share Amount:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$share['samity_share_amt'], 2)); ?></span>
            </div>
            
            <div class="col-md-6">
              <h6 class="text-secondary">Project Share Amount:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$project_amount, 2)); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Monthly Deposit:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$monthly_deposit, 2)); ?></span>
            </div>
          </div>
        </div>

        <div class="card-header"><strong>Grantor Information</strong></div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-6">
              <h6 class="text-secondary">Grantor Id:</h6>
              <span><?php echo htmlspecialchars($loan['grantor_id'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
              <h6 class="text-secondary">Grantor Describes:</h6>
              <span><?php echo htmlspecialchars($loan['grantor_describes'] ?? ''); ?></span>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Loan Info Column -->
    <div class="col-md-5">
      <div class="card mb-3">
        <div class="card-header"><strong>Loan Details</strong></div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-12">
              <h6 class="text-secondary">Product Name:</h6>
              <span><?php echo htmlspecialchars($loan['product_name'] ?? ''); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Product Code:</h6>
              <span><?php echo htmlspecialchars($loan['product_code'] ?? ''); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Loan Amount:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$loan['loan_amount'], 2)); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Duration (Months):</h6>
              <span><?php echo htmlspecialchars($loan['duration'] ?? ''); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Service Charge:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$loan['service_charge'], 2)); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Verification Charge:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$loan['verification_charge'], 2)); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Late Charge:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$loan['late_charge'], 2)); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Expired Charge:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$loan['expired_charge'], 2)); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Disbursement Amount:</h6>
              <span>৳ <?php echo htmlspecialchars(number_format((float)$loan['disbursement_amount'], 2)); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Purpose:</h6>
              <span><?php echo htmlspecialchars($loan['loan_purpose'] ?? ''); ?></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Status:</h6>
              <span><strong><?php echo htmlspecialchars($loan['status'] ?? ''); ?></strong></span>
            </div>
            <div class="col-md-12">
              <h6 class="text-secondary">Applied On:</h6>
              <span><?php echo htmlspecialchars($loan['created_at'] ?? ''); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Nominees Section -->
    <?php if (!empty($nominees)): ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header"><strong>Nominee(s)</strong></div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Relation</th>
                <th>NID</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($nominees as $nominee): ?>
              <tr>
                <td><?php echo htmlspecialchars($nominee['name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($nominee['relation'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($nominee['nid'] ?? ''); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
