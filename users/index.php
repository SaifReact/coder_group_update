<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

function englishToBanglaNumber($number) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ','];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '.', ','];
    return str_replace($en, $bn, $number);
}

$ac_title = $_SESSION['setup']['ac_title'] ?? '';
$ac_no = $_SESSION['setup']['ac_no'] ?? 0;
$bank_name = $_SESSION['setup']['bank_name'] ?? '';
$bank_address = $_SESSION['setup']['bank_address'] ?? '';

$member_id = $_SESSION['member_id'] ?? 0;
$user_id   = $_SESSION['user_id'] ?? 0;
$status    = $_SESSION['status'] ?? '';

if (!$member_id || !$user_id || !$status) {
    echo "User_id, Member_id, status not found in session.";
}

$member = null;
$nominees = [];
$member_docs = [];

if ($member_id) {

    /* ----------------------------------------------------------------------
        1) Fetch Member Info
    ---------------------------------------------------------------------- */
    $stmt = $pdo->prepare("SELECT * FROM members_info WHERE id = ? LIMIT 1");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();

    /* ----------------------------------------------------------------------
        Nominees
    ---------------------------------------------------------------------- */
    $stmt2 = $pdo->prepare("SELECT * FROM member_nominee WHERE member_id = ?");
    $stmt2->execute([$member_id]);
    $nominees = $stmt2->fetchAll();

    /* ----------------------------------------------------------------------
        Member Documents
    ---------------------------------------------------------------------- */
    $stmt3 = $pdo->prepare("SELECT * FROM member_documents WHERE member_id = ?");
    $stmt3->execute([$member_id]);
    $member_docs = $stmt3->fetchAll();

    /* ----------------------------------------------------------------------
        1) SAMITY SHARE & EXTRA SHARE (From member_share table)
    ---------------------------------------------------------------------- */
    $stmt4 = $pdo->prepare("
        SELECT samity_share, extra_share, late_fee 
        FROM member_share
        WHERE member_id = ? LIMIT 1
    ");
    $stmt4->execute([$member_id]);
    $shareRow = $stmt4->fetch();

    $samity_share     = $shareRow['samity_share']     ?? 0;
    $extra_share      = $shareRow['extra_share']      ?? 0;
    $late_fee   = $shareRow['late_fee']      ?? 0;

    /* ----------------------------------------------------------------------
        2) PROJECT SHARE (SUM) (From member_project)
    ---------------------------------------------------------------------- */
    $stmt5 = $pdo->prepare("
        SELECT 
            COALESCE(SUM(project_share), 0) AS total_project_share,
            COALESCE(SUM(paid_amount), 0) AS total_paid_amount
        FROM member_project
        WHERE member_id = ?
          AND status = 'A'
          AND project_share > 0
          AND paid_amount > 0
    ");
    $stmt5->execute([$member_id]);
    $projectData = $stmt5->fetch();

    $project_share = $projectData['total_project_share'] ?? 0;
    $project_paid  = $projectData['total_paid_amount']  ?? 0;

    /* ----------------------------------------------------------------------
        4) TOTAL SHARE CALCULATION
        samity_share + project_share + extra_share
    ---------------------------------------------------------------------- */
    $total_share = $samity_share + $project_share + $extra_share;


    /* ----------------------------------------------------------------------
        Payments (Other payments)
    ---------------------------------------------------------------------- */
    $stmt6 = $pdo->prepare("
        SELECT SUM(CASE WHEN payment_method = 'admission' THEN amount ELSE 0 END) AS admission_amount, 
        SUM(CASE WHEN payment_method = 'Samity Share' THEN amount ELSE 0 END) AS samity_share_amount, 
        SUM(CASE WHEN payment_method = 'Project Share' THEN amount ELSE 0 END) AS project_share_amount, 
        SUM(CASE WHEN payment_method = 'Monthly' THEN amount ELSE 0 END) AS monthly_amount, 
        SUM(amount) AS total_amount FROM member_payments WHERE member_id = ? AND payment_method IN ('admission', 'Samity Share', 'Project Share', 'Monthly') AND status = 'A';
    ");
    $stmt6->execute([$member_id]);
    $paymentInfo = $stmt6->fetch();

    $admission_amount = $paymentInfo['admission_amount'] ?? 0;
    $samity_share_amount  = $paymentInfo['samity_share_amount']  ?? 0;
    $project_share_amount = $paymentInfo['project_share_amount'] ?? 0;
    $monthly_amount  = $paymentInfo['monthly_amount']  ?? 0;
    
    $total_amount = $samity_share_amount + $project_share_amount + $monthly_amount;
    
    /* ----------------------------------------------------------------------
        Fetch Member Bank Info
    ---------------------------------------------------------------------- */
    $stmtBank = $pdo->prepare("SELECT * FROM member_bank WHERE member_id = ? LIMIT 1");
    $stmtBank->execute([$member_id]);
    $bankInfo = $stmtBank->fetch();
}
?>

<?php 
include_once __DIR__ . '/../includes/open.php'; 
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

    <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
            <div>
                <h3 class="mb-3 text-primary fw-bold"> ড্যাশবোর্ড <span class="text-secondary">( Dashboard)</span></h3> 
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
                <h5 class="text-primary fw-bold" style="color:#007bff !important;">সমিতি শেয়ার</h5>
                <h6 class="text-primary" style="color:#007bff !important;">(Samity Share)</h6>
                <div class="display-6 fw-bold text-primary" style="color:#007bff !important;"> <?= englishToBanglaNumber($samity_share ?? 0); ?> <span style="font-size:1rem">(<?php echo htmlspecialchars($samity_share ?? 0); ?>) টি</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="text-success fw-bold" style="color:#28a745 !important;">প্রকল্প শেয়ার</h5>
                <h6 class="text-primary" style="color:#28a745 !important;">(Project Share)</h6>
                <div class="display-6 fw-bold text-success" style="color:#28a745 !important;"> <?= englishToBanglaNumber($project_share ?? 0); ?> <span style="font-size:1rem">(<?php echo htmlspecialchars($project_share ?? 0); ?>) টি</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="text-warning fw-bold" style="color:#ffc107 !important;">অবশিষ্ট শেয়ার</h5>
                <h6 class="text-primary" style="color:#ffc107 !important;">(Remaining Share)</h6>
                <div class="display-6 fw-bold text-warning" style="color:#ffc107 !important;"> <?= englishToBanglaNumber($extra_share ?? 0); ?> <span style="font-size:1rem">(<?php echo htmlspecialchars($extra_share ?? 0); ?>) টি</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#approvedModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#000 !important;">মোট শেয়ার</h5>
                <h6 class="text-primary" style="color:#000 !important;">(Total Share)</h6>
                <div class="display-6 fw-bold" style="color:#000 !important;"> <?= englishToBanglaNumber($total_share ?? 0); ?> <span style="font-size:1rem">(<?php echo htmlspecialchars($total_share ?? 0); ?>) টি</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#dc3545 !important;">সদস্য এন্ট্রি ফি (Admission Fee)</h5>
                <div class="display-6 fw-bold" style="color:#dc3545 !important;"> ৳ <?= englishToBanglaNumber($admission_amount ?? 0); ?> <span style="font-size:1rem">(<?php echo htmlspecialchars($admission_amount ?? 0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#FF1493 !important;">বিলম্ব ফি (Late Fine)</h5>
                <div class="display-6 fw-bold" style="color:#FF1493 !important;"> ৳ <?= englishToBanglaNumber($late_fee ?? 0); ?> <span style="font-size:1rem">(<?php echo htmlspecialchars($late_fee ?? 0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#6f42c1 !important;">সমিতি শেয়ার ফি</h5>
                <h6 class="text-primary" style="color:#6f42c1 !important;">(Samity Share Fee)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#6f42c1 !important;"> ৳ <?= englishToBanglaNumber($samity_share_amount ?? 0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars($samity_share_amount ?? 0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#0E4C92 !important;">প্রকল্প শেয়ার ফি</h5>
                <h6 class="text-primary" style="color:#0E4C92 !important;">(Project Share Fee)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#0E4C92 !important;"> ৳ <?= englishToBanglaNumber($project_share_amount ?? 0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars($project_share_amount ?? 0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#20c997 !important;">মাসিক ফি</h5>
                <h6 class="text-primary" style="color:#20c997 !important;">(Monthly Fee)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#20c997 !important;"> ৳ <?= englishToBanglaNumber($monthly_amount ?? 0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars($monthly_amount ?? 0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#rejectedModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#000000 !important;">মোট ফি জমা </h5>
                <h6 class="text-primary" style="color:#000000 !important;">(Total Deposit)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#000000 !important;"> ৳ <?= englishToBanglaNumber($total_amount ?? 0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars($total_amount ?? 0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#5097A4 !important;">ঋণ বিতরণ</h5>
                <h6 class="text-primary" style="color:#5097A4 !important;">(Loan Disburse)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#5097A4 !important;"> ৳ <?= englishToBanglaNumber(0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars(0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#40E0D0 !important;">মূলধনের পরিমাণ</h5>
                <h6 class="text-primary" style="color:#40E0D0 !important;">(Principle Amt)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#40E0D0 !important;"> ৳ <?= englishToBanglaNumber(0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars(0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#813101 !important;">সার্ভিস চার্জ</h5>
                <h6 class="text-primary" style="color:#813101 !important;">(Service Charge)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#813101 !important;"> ৳ <?= englishToBanglaNumber(0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars(0); ?>)</span> </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
              <div class="card-body">
                <h5 class="fw-bold" style="color:#000000 !important;">ঋণ পরিশোধ</h5>
                <h6 class="text-primary" style="color:#000000 !important;">(Loan Repay)</h6>
                <div class="fw-bold" style="font-size:1.5rem; color:#000000 !important;"> ৳ <?= englishToBanglaNumber(0); ?> <span style="font-size:.7rem">(<?php echo htmlspecialchars(0); ?>)</span> </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card mt-4 mb-4">
          <div class="card-header bg-primary text-white fw-bold">
              <div class="row">
                  <div class="col-md-6">সদস্যের সকল তথ্য (All Information)</div>
                  <div class="col-md-6 text-end">
                      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bankInfoModal" onclick="loadBankInfo()">
                        <?php echo ($bankInfo) ? 'ব্যাংকের তথ্য সম্পাদনা করুন (Edit Bank A/C Info.)' : 'ব্যাংকের তথ্য যুক্ত করুন (Add Bank A/C Info.)'; ?>
                      </button>
                  </div>
              </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <img src="../<?php echo htmlspecialchars($member['profile_image']); ?>" class="rounded-circle zoomable-img" style="width:80px;height:80px;" alt="Profile">
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
                <div class="card-header">নমিনী - Nominee</div>
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
                <div class="card-header">ডকুমেন্টস - Document(s)</div>
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

<!-- Bank Info Modal -->
<div class="modal fade" id="bankInfoModal" tabindex="-1" aria-labelledby="bankInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="bankInfoModalLabel">🏦 <span id="modalTitle">ব্যাংক হিসাব তথ্য যুক্ত করুন</span> (Add/Edit Bank Account Info)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../process/add_bank_info.php">
                <div class="modal-body">
                    <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member_id); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <input type="hidden" name="bank_id" id="bank_id" value="">
                    
                    <div class="mb-3">
                        <label for="ac_no" class="form-label">হিসাব নং (Account No) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ac_no" name="ac_no" required placeholder="Enter Account Number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="ac_title" class="form-label">হিসাবের শিরোনাম (Account Title) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ac_title" name="ac_title" required placeholder="Enter Account Title">
                    </div>
                    
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">ব্যাংকের নাম (Bank Name) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required placeholder="Enter Bank Name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="branch_name" class="form-label">শাখার নাম (Branch Name) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" required placeholder="Enter Branch Name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="routing_no" class="form-label">রাউটিং নম্বর (Routing Number)</label>
                        <input type="text" class="form-control" id="routing_no" name="routing_no" placeholder="Enter Routing Number (Optional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন (Close)</button>
                    <button type="submit" class="btn btn-success">সংরক্ষণ করুন (Save)</button>
                </div>
            </form>
        </div>
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

// Function to load bank info for edit
function loadBankInfo() {
    var bankInfo = <?php echo json_encode($bankInfo); ?>;
    
    if (bankInfo && bankInfo.id) {
        // Edit mode - populate fields
        document.getElementById('modalTitle').innerText = 'ব্যাংক হিসাব তথ্য সম্পাদনা করুন';
        document.getElementById('bank_id').value = bankInfo.id;
        document.getElementById('ac_no').value = bankInfo.ac_no || '';
        document.getElementById('ac_title').value = bankInfo.ac_title || '';
        document.getElementById('bank_name').value = bankInfo.bank_name || '';
        document.getElementById('branch_name').value = bankInfo.branch_name || '';
        document.getElementById('routing_no').value = bankInfo.routing_no || '';
    } else {
        // Add mode - clear fields
        document.getElementById('modalTitle').innerText = 'ব্যাংক হিসাব তথ্য যুক্ত করুন';
        document.getElementById('bank_id').value = '';
        document.getElementById('ac_no').value = '';
        document.getElementById('ac_title').value = '';
        document.getElementById('bank_name').value = '';
        document.getElementById('branch_name').value = '';
        document.getElementById('routing_no').value = '';
    }
}
</script>
