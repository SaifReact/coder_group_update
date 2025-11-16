<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$ac_title = $_SESSION['setup']['ac_title'] ?? '';
$ac_no = $_SESSION['setup']['ac_no'] ?? 0;
$bank_name = $_SESSION['setup']['bank_name'] ?? '';
$bank_address = $_SESSION['setup']['bank_address'] ?? '';

 $member_id = isset($_SESSION['member_id'])? $_SESSION['member_id'] : 0;
 $user_id = isset($_SESSION['user_id'])? $_SESSION['user_id'] : 0;
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

                    $stmt6 = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ?");
                    $stmt6->execute([$member_id]);
                    $member_project = $stmt6->fetch();

          if ($result && $member_project) {
            $samity_share = $result['samity_share'] ?? 0;
            $share_amt = $samity_share * ($utils['fee'] ?? 0);
            $project_share = $member_project['project_share'] ?? 0;
            $extra_share = $result['extra_share'] ?? 0;
            $no_share = $samity_share + $project_share + $extra_share;
            $admission_fee = $result['admission_fee'] ?? 0;
            $samity_share_amt = $result['samity_share_amt'] ?? 0;
            $share_amount = $member_project['paid_amount'] ?? 0;
            $total_share_amount = $samity_share_amt + $share_amount;
            $monthly_amount = $result['for_install'] ?? 0;
            $total_deposit = $total_share_amount + $monthly_amount;

            // Update admission_fee calculation
            $admission_fee_total = 
              ($admission_fee ?? 0) - 
              (($idcard_fee ?? 0) + ($passbook_fee ?? 0) + ($softuses_fee ?? 0));

                    } 

                    $stmt5 = $pdo->prepare("SELECT COUNT(*) as payment_count, SUM(amount) as total_amount 
                                            FROM member_payments 
                                            WHERE payment_method != 'admission' AND member_id = ?");
                    $stmt5->execute([$member_id]);
                    $result1 = $stmt5->fetch();

                    $payment_count = $result1['payment_count'] ?? 0; // Total number of payments
                    $total_amount = $result1['total_amount'] ?? 0;   // Sum of all payment amounts
                    
                    
                }
                include_once __DIR__ . '/../includes/open.php';
?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row"> <?php include_once __DIR__ . '/../includes/side_bar.php'; ?> <main class="col-12 col-md-9 col-lg-9 col-xl-9 px-md-4">
      <div>
        <h3 class="mb-3 text-primary fw-bold">Dashboard <span class="text-secondary">( ড্যাশবোর্ড )</span>
        </h3>
        <hr class="mb-4" />
        <div class="row g-4 mb-4">
          <div class="col-md-12 text-center"> <?php if ($status === 'P'): ?> <div class="alert alert-danger mt-2" style="font-size:1rem;">
              <b>সদস্যপদ অনুমোদনের জন্য ডকুমেন্টস ও ভর্তি ফি প্রদান করুন।</b>
              <br /> হিসাবের তথ্য ( Account information): <br /> <?= htmlspecialchars($ac_title); ?> <br /> ইসলামিক হিসাব নং- <?= htmlspecialchars($ac_no); ?> <br /> <?= htmlspecialchars($bank_name); ?>, <?= htmlspecialchars($bank_address); ?>
            </div> <?php endif; ?> </div>
        </div>
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="text-primary fw-bold" style="color:#007bff !important;">Samity Share</h5>
                <div class="display-6 fw-bold text-primary" style="color:#007bff !important;"> <?php echo htmlspecialchars($samity_share ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="text-success fw-bold" style="color:#28a745 !important;">Project Share</h5>
                <div class="display-6 fw-bold text-success" style="color:#28a745 !important;"> <?php echo htmlspecialchars($project_share ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="text-warning fw-bold" style="color:#ffc107 !important;">Remaining Share</h5>
                <div class="display-6 fw-bold text-warning" style="color:#ffc107 !important;"> <?php echo htmlspecialchars($extra_share ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#000 !important;">Total Share</h5>
                <div class="display-6 fw-bold" style="color:#000 !important;"> <?php echo htmlspecialchars($no_share ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#dc3545 !important;">Admission Fee</h5>
                <div class="display-6 fw-bold" style="color:#dc3545 !important;"> <?php echo htmlspecialchars($admission_fee ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#6f42c1 !important;">Share Amount</h5>
                <div class="display-6 fw-bold" style="color:#6f42c1 !important;"> <?php echo htmlspecialchars($total_share_amount ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#20c997 !important;">Monthly Amount</h5>
                <div class="display-6 fw-bold" style="color:#20c997 !important;"> <?php echo htmlspecialchars($monthly_amount ?? 0); ?> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#rejectedModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#000 !important;">Total Deposit</h5>
                <div class="display-6 fw-bold" style="color:#000 !important;"> <?php echo htmlspecialchars($total_deposit ?? 0); ?> </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card mt-4 mb-4">
          <div class="card-header bg-primary text-white fw-bold">সদস্যের সকল তথ্য</div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <img src="../
									<?php echo htmlspecialchars($member['profile_image']); ?>" class="rounded-circle zoomable-img" style="width:80px;height:80px;" alt="Profile">
                  <div class="row">
                    <p>নাম (Name): <?php echo htmlspecialchars($member['name_en']); ?> - <?php echo htmlspecialchars($member['name_bn']); ?> </p>
                    <div class="col-md-6">
                      <p>সদস্য নং (Member No): <?php echo htmlspecialchars($member['id']); ?></p>
                      <p>জন্ম তারিখ (DOB): <?php echo htmlspecialchars($member['dob']); ?> </p>
                      <p>পিতার নাম (Father Name): <?php echo htmlspecialchars($member['father_name']); ?> </p>
                    </div>
                    <div class="col-md-6">
                      <p>সদস্য কোড (Member Code): <?php echo htmlspecialchars($member['member_code']); ?> </p>
                      <p>ধর্ম (Religion): <?php echo htmlspecialchars($member['religion']); ?> </p>
                      <p>মাতার নাম (Mother Name): <?php echo htmlspecialchars($member['mother_name']); ?> </p>
                    </div>
                  </div>
              </div>
              <div class="col-md-4">
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
                    <tbody> <?php if ($nominees): ?> <?php foreach ($nominees as $nom): ?> <tr>
                        <td> <?php echo htmlspecialchars($nom['name'] ?? ''); ?> </td>
                        <td> <?php echo htmlspecialchars($nom['percentage'] ?? ''); ?> </td>
                        <td> <?php echo htmlspecialchars($nom['relation'] ?? ''); ?> </td>
                        <td> <?php if (!empty($nom['nominee_image'])): ?> <img src="../
															<?php echo htmlspecialchars($nom['nominee_image']); ?>" class="rounded zoomable-img" style="width:40px;height:40px;" alt="Nominee"> <?php else: ?> <span class="text-muted">No Image</span> <?php endif; ?> </td>
                      </tr> <?php endforeach; ?> <?php else: ?> <tr>
                        <td colspan="4" class="text-muted">No Nominee</td>
                      </tr> <?php endif; ?> </tbody>
                  </table>
                </div>
                <div class="card-header">Nominee(s)</div>
                <div class="table-responsive">
                  <table class="table table-bordered align-middle mb-0">
                    <thead>
                      <tr>
                        <th>ডকুমেন্টের নাম</th>
                        <th>ডকুমেন্ট</th>
                      </tr>
                    </thead>
                    <tbody> <?php if ($member_docs): ?> <?php foreach ($member_docs as $doc): ?> <?php
                  $docTypeName = '';
                  switch ($doc['doc_type']) {
                    case '101': $docTypeName = 'জাতীয় পরিচয়পত্র / জন্ম সনদ'; break;
                    case '102': $docTypeName = 'স্বাক্ষর'; break;
                    case '103': $docTypeName = 'শিক্ষাগত যোগ্যতার সনদ'; break;
                    case '104': $docTypeName = 'অস্থায়ী নাগরিক সনদ'; break;
                    default: $docTypeName = htmlspecialchars($doc['doc_type']); break;
                  }
                ?> <tr>
                        <td> <?php echo $docTypeName; ?> </td>
                        <td>
                          <img src="../
																<?php echo htmlspecialchars($doc['doc_path']); ?>" class="doc-thumb zoomable-img" style="width:30px;height:30px;" alt="Doc">
                        </td>
                      </tr> <?php endforeach; ?> <?php else: ?> <tr>
                        <td colspan="2" class="text-muted">No Documents</td>
                      </tr> <?php endif; ?> </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  </main>
</div>
</div>

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
