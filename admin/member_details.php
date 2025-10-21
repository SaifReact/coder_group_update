<?php
// member_details_ajax.php
include_once '../config/config.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid member ID.</div>';
    exit;
}
$user_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM user_login WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo '<div class="alert alert-warning">Member not found.</div>';
    exit;
}
// Optionally fetch more details from other tables if needed

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$user_id) {
    echo '<div class="alert alert-danger">Invalid user ID.</div>';
    exit;
}
// Get member_id from user_login
$stmt = $pdo->prepare("SELECT member_id FROM user_login WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$member_id = $stmt->fetchColumn();
if (!$member_id) {
    echo '<div class="alert alert-warning">No member info found.</div>';
    exit;
}
// Fetch member info
$stmt = $pdo->prepare("SELECT * FROM members_info WHERE id = ? LIMIT 1");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
// Fetch nominees
$stmt2 = $pdo->prepare("SELECT * FROM member_nominee WHERE member_id = ?");
$stmt2->execute([$member_id]);
$nominees = $stmt2->fetchAll(PDO::FETCH_ASSOC);
// Fetch documents
$stmt3 = $pdo->prepare("SELECT * FROM member_documents WHERE member_id = ?");
$stmt3->execute([$member_id]);
$docs = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
  <div class="row g-4">
    <div class="col-md-8">
      <div class="card mb-3">
        <div class="card-body position-relative">
          <img src="../<?php echo htmlspecialchars($member['profile_image'] ?? 'assets/default.png'); ?>" class="rounded-circle position-absolute end-0 top-0 m-3 zoomable-img" style="width:80px;height:80px;" alt="Profile">
          <h5 class="card-title">Member ID: <span><?php echo htmlspecialchars($member['id'] ?? ''); ?></span></h5>
          <p>Member Code: <?php echo htmlspecialchars($member['member_code'] ?? ''); ?></p>
          <p>Name (EN): <?php echo htmlspecialchars($member['name_en'] ?? ''); ?></p>
          <p>Name (BN): <?php echo htmlspecialchars($member['name_bn'] ?? ''); ?></p>
          <p>DOB: <?php echo htmlspecialchars($member['dob'] ?? ''); ?></p>
          <p>Religion: <?php echo htmlspecialchars($member['religion'] ?? ''); ?></p>
          <p>Father Name: <?php echo htmlspecialchars($member['father_name'] ?? ''); ?></p>
          <p>Mother Name: <?php echo htmlspecialchars($member['mother_name'] ?? ''); ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-header">Nominee(s)</div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead>
              <tr>
                <th>Name</th>
                <th>Percent</th>
                <th>Relation</th>
                <th>Image</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($nominees): ?>
              <?php foreach ($nominees as $nom): ?>
                <tr>
                  <td><?php echo htmlspecialchars($nom['name'] ?? $nom['nominee_name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($nom['percentage'] ?? $nom['percent'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($nom['relation'] ?? ''); ?></td>
                  <td>
                    <?php if (!empty($nom['nominee_image'])): ?>
                      <img src="../<?php echo htmlspecialchars($nom['nominee_image']); ?>" class="rounded zoomable-img" style="width:40px;height:40px;" alt="Nominee">
                    <?php else: ?>
                      <span class="text-muted">No Image</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-muted">No Nominee</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <div class="card-header">Documents</div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead>
              <tr>
                <th>ডকুমেন্টের নাম</th>
                <th>ডকুমেন্ট</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($docs): ?>
              <?php foreach ($docs as $doc): ?>
                <?php
                  $docTypeName = '';
                  switch ($doc['doc_type']) {
                    case '101': $docTypeName = 'জাতীয় পরিচয়পত্র / জন্ম সনদ'; break;
                    case '102': $docTypeName = 'স্বাক্ষর'; break;
                    case '103': $docTypeName = 'শিক্ষাগত যোগ্যতার সনদ'; break;
                    case '104': $docTypeName = 'অস্থায়ী নাগরিক সনদ'; break;
                    default: $docTypeName = htmlspecialchars($doc['doc_type']); break;
                  }
                ?>
                <tr>
                  <td><?php echo $docTypeName; ?></td>
                  <td>
                    <img src="../<?php echo htmlspecialchars($doc['doc_path']); ?>" class="doc-thumb zoomable-img" style="width:30px;height:30px;" alt="Doc">
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="2" class="text-muted">No Documents</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
