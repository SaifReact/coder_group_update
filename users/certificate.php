<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$logo     = $_SESSION['setup']['logo'] ?? '';
$siteName = $_SESSION['setup']['site_name_bn'] ?? '';
$slogan_bn = $_SESSION['setup']['slogan_bn'] ?? '';
$slogan_en = $_SESSION['setup']['slogan_en'] ?? ''; 
$slogan   = $slogan_bn . ($slogan_en ? ' ( ' . $slogan_en . ' )' : '');
$reg_no   = $_SESSION['setup']['registration_no'] ?? '';
$address  = $_SESSION['setup']['address'] ?? '';
$phone1   = $_SESSION['setup']['phone1'] ?? '';
$phone2   = $_SESSION['setup']['phone2'] ?? '';
$phone    = $phone1 . ($phone2 ? ', ' . $phone2 : '');
$email    = $_SESSION['setup']['email'] ?? '';

 $member_id = isset($_SESSION['member_id'])? $_SESSION['member_id'] : 0;
 $user_id = isset($_SESSION['user_id'])? $_SESSION['user_id'] : 0;
 $status = isset($_SESSION['status']) ? $_SESSION['status'] : '';

/* --------- English to Bangla Number Converter --------- */
function englishToBanglaNumber($number) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ','];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '.', ','];
    return str_replace($en, $bn, $number);
}

function getBanglaDate($date) {
    $months_bn = [
        '01' => 'জানুয়ারি', '02' => 'ফেব্রুয়ারি', '03' => 'মার্চ', 
        '04' => 'এপ্রিল', '05' => 'মে', '06' => 'জুন',
        '07' => 'জুলাই', '08' => 'আগস্ট', '09' => 'সেপ্টেম্বর',
        '10' => 'অক্টোবর', '11' => 'নভেম্বর', '12' => 'ডিসেম্বর'
    ];
    
    $parts = explode('-', $date);
    if (count($parts) == 3) {
        $day = englishToBanglaNumber($parts[2]);
        $month = $months_bn[$parts[1]];
        $year = englishToBanglaNumber($parts[0]);
        return $day . ' ' . $month . ', ' . $year;
    }
    return '';
}

// Get President signature
$stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE banner_name_en = ? LIMIT 1");
$stmt->execute(['president']);
$president_sign = $stmt->fetch(PDO::FETCH_ASSOC);
$president_signature = $president_sign ? $president_sign['banner_image'] : '';

// Get Secretary signature
$stmt = $pdo->prepare("SELECT banner_image FROM banner WHERE banner_name_en = ? LIMIT 1");
$stmt->execute(['secretary']);
$secretary_sign = $stmt->fetch(PDO::FETCH_ASSOC);
$secretary_signature = $secretary_sign ? $secretary_sign['banner_image'] : '';

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

                    $stmt7 = $pdo->prepare("SELECT * FROM member_office WHERE member_id = ? LIMIT 1");
                    $stmt7->execute([$member_id]);
                    $member_office = $stmt7->fetch();

                    $stmt8 = $pdo->prepare("SELECT * FROM utils WHERE fee_type = ? LIMIT 1");
                    $stmt8->execute(['samity_share']);
                    $utils = $stmt8->fetch();

                    $stmt4 = $pdo->prepare("SELECT * FROM member_share WHERE member_id = ?");
                    $stmt4->execute([$member_id]);
                    $result = $stmt4->fetch();

                    $stmt6 = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ?");
                    $stmt6->execute([$member_id]);
                    $member_project = $stmt6->fetch();

          if ($result && $member_project) {
            // Fix: Use PHP DateTime for formatting
            $join_date = '';
            if (!empty($result['created_at'])) {
              try {
                $dt = new DateTime($result['created_at']);
                $join_date = $dt->format('Y-m-d');
              } catch (Exception $e) {
                $join_date = $result['created_at'];
              }
            }
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
  <div class="row"> 
    <?php include_once __DIR__ . '/../includes/side_bar.php'; ?> 
    <main class="col-12 col-md-9 col-lg-9 col-xl-9 px-md-4">
      <div>
        <h3 class="mb-3 text-primary fw-bold">Certificate <span class="text-secondary">( সনদপত্র )</span>
        </h3>
        <hr class="mb-4" />
        <div class="card mt-4 mb-4">
          <div class="card-header bg-primary text-white fw-bold">ক্রয়কৃত শেয়ার সমিতি ও প্রকল্প অনুযায়ী সনদপত্র</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered align-middle mb-0">
                <thead>
                  <tr>
                    <th>প্রকল্পের নাম (Project Name)</th>
                    <th>শেয়ার পরিমাণ (Share Amount)</th>
                    <th>সনদপত্র (Certificate)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>সমিতির শেয়ার</td>
                    <td> <?php echo htmlspecialchars($samity_share ?? 0); ?> </td>
                    <td>
                      <?php if ($samity_share > 0): ?>
                      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#certificateModal">
                        সনদপত্র দেখুন (View Certificate)
                      </button>
<!-- Certificate Modal -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="certificateModalLabel">সমিতি শেয়ার সনদপত্র</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="certificateModalBody">
        <div class="certificate-container-samity" id="certificate-content-samity">
                  <div class="certificate-border">
                    <!-- Serial Number -->
                    <div class="serial-number"> সিরিয়াল নং:- <?= englishToBanglaNumber($member['id']) ?> </div>
                    <!-- Header with Logo on Left, Details in Center -->
                    <div class="certificate-header"> <?php if (!empty($logo)): ?> <div class="certificate-logo">
                        <img src="
													<?php echo BASE_URL; ?>assets/img/
													<?= htmlspecialchars($logo) ?>" alt="Logo">
                      </div> <?php endif; ?> <div class="certificate-header-text">
                        <h1 class="certificate-title"> <?= htmlspecialchars($siteName) ?> </h1>
                        <p class="certificate-reg">রেজিঃ নং- <?= englishToBanglaNumber(htmlspecialchars($reg_no)) ?> </p> <?php if (!empty($slogan)): ?> <p class="certificate-slogan"> <?= htmlspecialchars($slogan) ?> </p> <?php endif; ?>
                      </div>
                    </div>
                    <!-- Certificate Title -->
                    <h2 class="share-certificate-title"> সমিতি শেয়ার সনদপত্র</h2>
                    <!-- Certificate Body -->
                    <div class="certificate-body">
                      <div class="certificate-content">
                        <p>এই মর্মে প্রত্যয়ন করা যাইতেছে যে, <span class="fill-blank"> <?= htmlspecialchars($member['name_bn']) ?> </span> সদস্য কোড- <span class="fill-blank"> <?= htmlspecialchars($member['member_code']) ?> </span> পিতাঃ <span class="fill-blank"> <?= htmlspecialchars($member['father_name']) ?> </span> মাতাঃ <span class="fill-blank"> <?= htmlspecialchars($member['mother_name']) ?> </span> ঠিকানাঃ <span class="fill-blank"> <?= htmlspecialchars($member_office['present_address']) ?> </span>। </p>
                        <p> <?= htmlspecialchars($siteName) ?> এর প্রত্যেকটি = <span class="fill-blank"> <?= htmlspecialchars(englishToBanglaNumber($utils['fee'])) ?>/- </span> টাকা মূল্যের <span class="fill-blank"> <?= englishToBanglaNumber($samity_share) ?> </span> টি শেয়ারের পূর্ণমূল্য = <span class="fill-blank"> <?= englishToBanglaNumber($share_amt) ?>/- </span> টাকা পরিশোধিত উক্ত সমবায় সমিতির শেয়ারের নিবন্ধকৃত মালিক। </p>
                        <p>অদ্য <?= getBanglaDate($join_date) ?> তারিখে অত্র সমিতির সদস্যপদের দিনে এই সার্টিফিকেট প্রদান করা হইল। </p>
                      </div>
                      <!-- Signature Section -->
                      <div class="signature-section">
                        <div class="signature-box">
                            <?php if (!empty($president_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($president_signature) ?>" alt="President Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">সভাপতি</div>
                        </div>
                        <div class="signature-box">
                            <?php if (!empty($secretary_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($secretary_signature) ?>" alt="Secretary Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">সম্পাদক</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
      </div>
    </div>
  </div>
</div>
                      <?php else: ?> <span class="text-muted">No Certificate</span>
                      <?php endif; ?>
                    </td>
                  </tr> 
                  <?php
                        $stmt = $pdo->prepare("SELECT ms.*, mp.*, p.project_name_bn, p.project_name_en FROM member_share ms JOIN member_project mp ON ms.member_id = mp.member_id JOIN project p ON mp.project_id = p.id WHERE mp.member_id = ?");
                        $stmt->execute([$member_id]);
                        $projects = $stmt->fetchAll();
                        if ($projects):
                          foreach ($projects as $proj): ?> 
                        <tr>
                          <td> <?php echo htmlspecialchars($proj['project_name_bn']); ?> </td>
                          <td> <?php echo htmlspecialchars($proj['project_share']); ?> </td>
                          <td>
                            <?php if ($proj['project_share'] > 0): ?>
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#projectCertificateModal<?php echo $proj['project_id']; ?>">
                              সনদপত্র দেখুন (View Certificate)
                            </button>

<!-- Project Certificate Modal -->
<div class="modal fade" id="projectCertificateModal<?php echo $proj['project_id']; ?>" tabindex="-1" aria-labelledby="projectCertificateModalLabel<?php echo $proj['project_id']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"> 
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="projectCertificateModalLabel<?php echo $proj['project_id']; ?>">প্রকল্প শেয়ার সনদপত্র</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="projectCertificateModalBody<?php echo $proj['project_id']; ?>">
        <div class="certificate-container-project" id="project-certificate-content-<?php echo $proj
['project_id']; ?>">
                  <div class="certificate-border">
                    <!-- Serial Number -->
                    <div class="serial-number"> সিরিয়াল নং:- <?= englishToBanglaNumber($member['id']) ?> </div>
                    <!-- Header with Logo on Left, Details in Center -->  
                    <div class="certificate-header"> <?php if (!empty($logo)): ?> <div class="certificate-logo">
                        <img src="
                          <?php echo BASE_URL; ?>assets/img/<?= htmlspecialchars($logo) ?>" alt="Logo">
                      </div> <?php endif; ?> <div class="certificate-header-text">

                        <h1 class="certificate-title"> <?= htmlspecialchars($siteName) ?> </h1>
                        <p class="certificate-reg">রেজিঃ নং- <?= englishToBanglaNumber(htmlspecialchars($reg_no)) ?> </p> <?php if (!empty($slogan)): ?> <p class="certificate-slogan"> <?= htmlspecialchars($slogan) ?> </p> <?php endif; ?>
                      </div>
                    </div>
                    <!-- Certificate Title -->
                    <h2 class="share-certificate-title"> প্রকল্প শেয়ার সনদপত্র</h2>
                    <!-- Certificate Body -->
                    <div class="certificate-body">
                      <div class="certificate-content">
                        <p>এই মর্মে প্রত্যয়ন করা যাইতেছে যে, <span class="fill-blank"> <?= htmlspecialchars($member['name_bn']) ?> </span> সদস্য কোড- <span class="fill-blank"> <?= htmlspecialchars($member['member_code']) ?> </span> পিতাঃ <span class="fill-blank"> <?= htmlspecialchars($member['father_name']) ?> </span> মাতাঃ <span class="fill-blank
"> <?= htmlspecialchars($member['mother_name']) ?> </span> ঠিকানাঃ <span class="fill-blank"> <?= htmlspecialchars($member_office['present_address']) ?> </span>। </p>
                        <p> <?= htmlspecialchars($siteName) ?> এর প্রকল্প <span class="fill-blank"> <?= htmlspecialchars($proj['project_name_bn']) ?> </span> এর প্রত্যেকটি = <span class="fill-blank"> <?= htmlspecialchars(englishToBanglaNumber($utils['fee'])) ?>/- </span> টাকা মূল্যের <span class="fill-blank"> <?= englishToBanglaNumber($proj['project_share']) ?> </span> টি শেয়ারের পূর্ণমূল্য = <span class="fill-blank"> <?= englishToBanglaNumber($proj['share_amount']) ?>/- </span> টাকা পরিশোধিত উক্ত সমবায় সমিতির প্রকল্প শেয়ারের নিবন্ধকৃত মালিক। </p>
                        <p>অদ্য <?= getBanglaDate($join_date) ?> তারিখে অত্র সমিতির সদস্যপদের দিনে এই সার্টিফিকেট প্রদান করা হইল। </p>
                      </div>
                      <!-- Signature Section -->

                      <div class="signature-section">
                        <div class="signature-box">
                            <?php if (!empty($president_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($president_signature) ?>" alt="President Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">সভাপতি</div>
                              
                        </div>
                        <div class="signature-box"> 
                            <?php if (!empty($secretary_signature)): ?>
                            <img src="<?php echo BASE_URL; ?>banner/<?= htmlspecialchars($secretary_signature) ?>" alt="Secretary Signature" class="signature-image">
                            <?php else: ?>
                            <div class="signature-line"></div>
                            <?php endif; ?>
                            <div class="signature-line"></div>
                          <div class="signature-label">সম্পাদক</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
      </div>
    </div>
  </div>  
</div>
                            <?php else: ?> <span class="text-muted">No Certificate</span>
                            <?php endif; ?>
                          </td>
                        
                        </tr> <?php endforeach;
                        else: ?> <tr>
                    <td colspan="6" class="text-muted text-center">No project shares found.</td>
                  </tr> <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  </div>
  </main>
</div>
</div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
