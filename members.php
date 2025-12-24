<?php

include_once __DIR__ . '/config/config.php';

// Get members
$stmt = $pdo->prepare("SELECT * FROM members_info a, member_share b, user_login c WHERE a.id = b.member_id AND a.id = c.member_id AND c.status != 'R' ORDER BY a.id ASC;");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total shares
$stmtTotal = $pdo->prepare("SELECT SUM(no_share) AS total_share FROM member_share a, user_login b where a.member_id = b.member_id AND b.status != 'R'");
$stmtTotal->execute();
$total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

?>
<?php include_once __DIR__ . '/includes/open.php'; ?>
<!-- Hero Start -->
<div class="container-fluid pb-3 hero-header bg-light">
    <div class="row px-4">
      <div class="col-12 col-md-12 col-lg-12 col-xl-12">
        <div class="glass-card-header mb-1">
          <h5 class="text-center fw-bold" style="color:#045D5D; letter-spacing:1px; text-shadow:1px 2px 8px #fff8; font-size:1.5rem; font-family:'Poppins',sans-serif;">সদস্যদের তালিকা ( List of members )</h5>
          <!-- Display Total Share -->
          <p class="fw-bold text-center" style="color: #FF0000; font-size: 1rem">
            মোট বিক্রিত শেয়ার সংখ্যা: <?= htmlspecialchars($total['total_share']); ?>
          </p>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th scope="col">ক্রমিক নং</th>
                  <th scope="col">সদস্য কোড</th>
                  <th scope="col">নাম</th>
                  <th scope="col">শেয়ার সংখ্যা</th>
                  <th scope="col">পিতার নাম</th>
                  <th scope="col">মাতার নাম</th>
                </tr>
              </thead>
              <tbody>
                <?php $rownum = 1; ?>
                <?php foreach ($members as $member): ?>
                <tr>
                  <td class="text-center"><?= $rownum++; ?></td>
                  <td><?= htmlspecialchars($member['member_code']); ?></td>
                  <td><?= htmlspecialchars($member['name_en']); ?><br/><?= htmlspecialchars($member['name_bn']); ?></td>
                  <td class="text-center"><?= htmlspecialchars($member['no_share']); ?></td>
                  <td><?= htmlspecialchars($member['father_name']); ?></td>
                  <td><?= htmlspecialchars($member['mother_name']); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- Hero End -->
<?php include_once __DIR__ . '/includes/end.php'; ?>
