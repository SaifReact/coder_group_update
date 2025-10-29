<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$ac_title = $_SESSION['setup']['ac_title'] ?? '';
$ac_no = $_SESSION['setup']['ac_no'] ?? '';
$bank_name = $_SESSION['setup']['bank_name'] ?? '';
$bank_address = $_SESSION['setup']['bank_address'] ?? '';

 $member_id = isset($_SESSION['member_id'])? $_SESSION['member_id'] : '';
 $user_id = isset($_SESSION['user_id'])? $_SESSION['user_id'] : '';
 $status = isset($_SESSION['status']) ? $_SESSION['status'] : '';

 if ($member_id === '' || $user_id === '' || $status === '') {
   echo 'User_id, Member_id, status not found in session.';
 } 
                $member = null;
                $nominees = [];
                $member_docs = [];
                $member_share = 0;
                if ($member_id) {
                    // Fetch member info
                    $stmt = $pdo->prepare("SELECT * FROM members_info WHERE id = ? LIMIT 1");
                    $stmt->execute([$member_id]);
                    $member = $stmt->fetch();
                    // Fetch nominee(s)
                    $stmt2 = $pdo->prepare("SELECT * FROM member_nominee WHERE member_id = ?");
                    $stmt2->execute([$member_id]);
                    $nominees = $stmt2->fetchAll();
                    // Fetch member documents
                    $stmt3 = $pdo->prepare("SELECT * FROM member_documents WHERE member_id = ?");
                    $stmt3->execute([$member_id]);
                    $member_docs = $stmt3->fetchAll();

                    $stmt4 = $pdo->prepare("SELECT * FROM member_share WHERE member_id = ?");
                    $stmt4->execute([$member_id]);
                    $result = $stmt4->fetch();

                    if ($result) {
                        $admission_fee = $result['admission_fee'];
                        $no_share = $result['no_share'];
                        $idcard_fee = $result['idcard_fee'];
                        $passbook_fee = $result['passbook_fee'];
                        $softuses_fee = $result['softuses_fee'];

                        // Update admission_fee calculation
                        $admission_fee_total = 
                            ($admission_fee ?? 0) - 
                            (($idcard_fee ?? 0) + ($passbook_fee ?? 0) + ($softuses_fee ?? 0));

                    } else {
                        $admission_fee = 0;
                        $no_share = 0;
                        $idcard_fee = 0;
                        $passbook_fee = 0;
                        $softuses_fee = 0;
                    }

                    $stmt5 = $pdo->prepare("SELECT COUNT(*) as payment_count, SUM(amount) as total_amount 
                                            FROM member_payments 
                                            WHERE payment_method != 'admission' AND member_id = ?");
                    $stmt5->execute([$member_id]);
                    $result1 = $stmt5->fetch();

                    $payment_count = $result1['payment_count'] ?? 0; // Total number of payments
                    $total_amount = $result1['total_amount'] ?? 0;   // Sum of all payment amounts
                    
                }
?>

<?php include_once __DIR__ . '/../includes/open.php'; ?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
      <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 col-xl-9 px-md-4">
            <div>
                <h3 class="mb-3 text-primary fw-bold">Dashboard <span class="text-secondary">( ড্যাশবোর্ড )</span></h3> 
                <hr class="mb-4" />

                <div class ="row g-4 mb-4">
                  <div class="col-md-12 text-center">                  
                    <?php if ($status === 'P'): ?>
                    <div class="alert alert-danger mt-2" style="font-size:1rem;">
                      <b>সদস্যপদ অনুমোদনের জন্য ডকুমেন্টস ও ভর্তি ফি প্রদান করুন।</b>
                        <br/>
                        যৌথ হিসাবের তথ্য (Joint account information): <br/>
                       <?= htmlspecialchars($ac_title); ?> <br/>
                        হিসাব নং- <?= htmlspecialchars($ac_no); ?> <br/>
                        <?= htmlspecialchars($bank_name); ?>, <?= htmlspecialchars($bank_address); ?>
                    </div>
                    <?php endif; ?>
                  </div>  
                </div>
                
                <div class="row g-4 mb-4">
                  <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
                     <div class="card-body">
                        <h5 class="text-success fw-bold">No of Share</h5>
                        <div class="display-6 fw-bold text-success"><?php echo htmlspecialchars($no_share ?? 0); ?></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
                      <div class="card-body">
                        <h5 class="text-warning fw-bold">Admission Fee</h5>
                        <div class="display-6 fw-bold text-warning"><?php echo htmlspecialchars($admission_fee ?? 0); ?></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#rejectedModal">
                      <div class="card-body">
                        <h5 class="text-danger fw-bold">Total Deposit</h5>
                        <div class="display-6 fw-bold text-danger"><?php echo htmlspecialchars($total_amount ?? 0); ?></div>
                      </div>
                    </div>
                  </div>  
                </div>
  <div class="row">
    <!-- Member Info -->
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-body position-relative">
          <!-- Member Info -->
          <img src="../<?php echo htmlspecialchars($member['profile_image']); ?>" 
               class="rounded-circle position-absolute end-0 top-0 m-3 zoomable-img" 
               style="width:80px;height:80px;" alt="Profile">

          <h5 class="card-title">Member ID: <span><?php echo htmlspecialchars($member['id']); ?></span></h5>
          <p>Member Code: <?php echo htmlspecialchars($member['member_code']); ?></p>
          <p>Name: <?php echo htmlspecialchars($member['name_en']); ?> - <?php echo htmlspecialchars($member['name_bn']); ?></p>
          <p>DOB: <?php echo htmlspecialchars($member['dob']); ?></p>
          <p>Father Name: <?php echo htmlspecialchars($member['father_name']); ?></p>
          <p>Mother Name: <?php echo htmlspecialchars($member['mother_name']); ?></p>
          <p>Religion: <?php echo htmlspecialchars($member['religion']); ?></p>
          
        </div>
      </div>
    </div>
    <!-- Right Column -->
    <div class="col-md-4">
      <!-- Nominee Card -->
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
                  <td><?php echo htmlspecialchars($nom['name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($nom['percentage'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($nom['relation'] ?? ''); ?></td>
                  <td>
                    <?php if (!empty($nom['nominee_image'])): ?>
                      <img src="../<?php echo htmlspecialchars($nom['nominee_image']); ?>" 
                           class="rounded zoomable-img" style="width:40px;height:40px;" alt="Nominee">
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
      <!-- Documents Card -->
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
            <?php if ($member_docs): ?>
              <?php foreach ($member_docs as $doc): ?>
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
                    <img src="../<?php echo htmlspecialchars($doc['doc_path']); ?>" 
                         class="doc-thumb zoomable-img" style="width:30px;height:30px;" alt="Doc">
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
                
                <!-- Add your main dashboard content here -->
            </div>
        </main>
  </div>
  
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editMemberModalBody">
                <!-- Form will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Admission Modal -->
<div class="modal fade" id="admissionModal" tabindex="-1" aria-labelledby="admissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="admissionModalLabel">Admission Fee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Admission Fee</th>
                            <th>ID Card Fee</th>
                            <th>Passbook Fee</th>
                            <th>Software Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            
                            <td><?php echo htmlspecialchars($admission_fee_total); ?></td>
                            <td><?php echo htmlspecialchars($idcard_fee); ?></td>
                            <td><?php echo htmlspecialchars($passbook_fee); ?></td>
                            <td><?php echo htmlspecialchars($softuses_fee); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Total Deposit Modal -->
<div class="modal fade" id="totalDepositModal" tabindex="-1" aria-labelledby="totalDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="totalDepositModalLabel">Total Deposit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Payment Year</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->prepare("SELECT trans_no, payment_method, payment_year, SUM(amount) as total_amount 
                                               FROM member_payments 
                                               WHERE payment_method != 'admission' AND member_id = ?
                                               GROUP BY trans_no, payment_method, payment_year");
                        $stmt->execute([$member_id]);
                        $payments = $stmt->fetchAll();

                        if ($payments):
                            foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['payment_method'] . ' (' . $payment['payment_year'] . ')'); ?></td>
                                    <td><?php echo htmlspecialchars($payment['trans_no']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['total_amount']); ?></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="3" class="text-muted text-center">No Deposit Records Found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Hero End -->

<?php include_once __DIR__ . '/../includes/end.php'; ?>

<style>
.zoomable-img, .doc-thumb {
    cursor: url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/zoom-in.svg'), zoom-in !important;
}
</style>
<!-- Modal for image zoom -->
<style>
#imgZoomModal .modal-dialog {
    max-width: unset;
    width: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
    height: 100vh;
}
#imgZoomModal .modal-content {
    background: transparent;
    box-shadow: none;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
#imgZoomModal .modal-body {
    background: transparent;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
#imgZoomModal #zoomedImg {
    display: block;
    max-width: 100vw;
    max-height: 90vh;
    margin: auto;
    border-radius: 8px;
    box-shadow: 0 2px 16px #000a;
    background: transparent;
}
</style>
<div class="modal fade" id="imgZoomModal" tabindex="-1" aria-labelledby="imgZoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 p-1" style="background:transparent;">
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="zoomedImg" src="" alt="Zoomed Document">
            </div>
        </div>
    </div>
</div>
<!-- Add jQuery and Magnify.js -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnify/2.3.4/css/magnify.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnify/2.3.4/js/jquery.magnify.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image zoom modal (fallback)
    document.querySelectorAll('.doc-thumb, .zoomable-img').forEach(function(img) {
        img.addEventListener('click', function() {
            var modal = new bootstrap.Modal(document.getElementById('imgZoomModal'));
            document.getElementById('zoomedImg').src = this.src;
            modal.show();
        });
    });

    // Magnify.js zoom
    $(function() {
        $('.zoomable-img, .doc-thumb').magnify({
            speed: 200,
            magnifiedWidth: 300,
            magnifiedHeight: 300
        });
    });

    // Edit member modal
    var editBtn = document.getElementById('editMemberBtn');
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var modalBody = document.getElementById('editMemberModalBody');
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
            var modal = new bootstrap.Modal(document.getElementById('editMemberModal'));
            modal.show();
            fetch('../includes/edit_member_form.php?id=' + encodeURIComponent(userId))
                .then(resp => resp.text())
                .then(html => { modalBody.innerHTML = html; })
                .catch(() => { modalBody.innerHTML = '<div class="alert alert-danger">Could not load form.</div>'; });
        });
    }
});
</script>
