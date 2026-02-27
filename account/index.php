<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';

// Helper function to convert English numbers to Bangla
function englishToBanglaNumber($number) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', ','];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '.', ','];
    return str_replace($en, $bn, $number);
}

// Fetch member counts
$processed = $pdo->query("SELECT COUNT(*) FROM user_login WHERE status='P' and role = 'user'")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM user_login WHERE status='A' and role = 'user'")->fetchColumn();
$inactive = $pdo->query("SELECT COUNT(*) FROM user_login WHERE status='I' and role = 'user'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM user_login WHERE status='R' and role = 'user'")->fetchColumn();
$paymentDoneData = $pdo->query("SELECT COUNT(DISTINCT member_id) as total_payment_done FROM member_payments WHERE status = 'A'")->fetch(PDO::FETCH_ASSOC);

// Fetch financial data from member_share table
$shareStmt = $pdo->query("
    SELECT 
        COALESCE(SUM(idcard_fee), 0) as total_idcard_fee,
        COALESCE(SUM(passbook_fee), 0) as total_passbook_fee,
        COALESCE(SUM(softuses_fee), 0) as total_softuses_fee,
        COALESCE(SUM(sms_fee), 0) as total_sms_fee,
        COALESCE(SUM(office_rent), 0) as total_office_rent,
        COALESCE(SUM(office_staff), 0) as total_office_staff,
        COALESCE(SUM(other_fee), 0) as total_other_fee,
        COALESCE(SUM(late_fee), 0) as total_late_fee
    FROM member_share
");
$shareData = $shareStmt->fetch(PDO::FETCH_ASSOC);

// Fetch payment data from member_payments table
$paymentStmt = $pdo->query("
    SELECT 
        COALESCE(SUM(CASE WHEN payment_method = 'Samity Share' THEN amount ELSE 0 END), 0) as total_samity_share,
        COALESCE(SUM(CASE WHEN payment_method = 'Project Share' THEN amount ELSE 0 END), 0) as total_project_share,
        COALESCE(SUM(CASE WHEN payment_method NOT IN ('Samity Share', 'Project Share', 'admission') THEN amount ELSE 0 END), 0) as total_monthly
    FROM member_payments WHERE status = 'A'
");
$paymentData = $paymentStmt->fetch(PDO::FETCH_ASSOC);

// Calculate totals
$total_member_admission_fees = $shareData['total_idcard_fee'] + $shareData['total_passbook_fee'] + 
                           $shareData['total_softuses_fee'] + $shareData['total_sms_fee'] + 
                           $shareData['total_office_rent'] + $shareData['total_office_staff'] + 
                           $shareData['total_other_fee'];

$total_all_deposits = $paymentData['total_samity_share'] + $paymentData['total_project_share'] + 
                      $paymentData['total_monthly'] + $total_member_admission_fees + $shareData['total_late_fee'];
?>

<?php 
include_once __DIR__ . '/../includes/open.php'; 
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

    <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
            <div>
                <h3 class="mb-3 text-primary fw-bold">একাউন্টস ড্যাশবোর্ড <span class="text-secondary">(Accounts Dashboard)</span></h3> 
                <hr class="mb-4" />

                <!-- Member Status Cards -->
                <div class="row g-4 mb-4">
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center" style="<?php echo $approved > 0 ? 'cursor:pointer;' : ''; ?> background: linear-gradient(135deg, #28a745 0%, #20c997 100%);" <?php echo $approved > 0 ? 'data-bs-toggle="modal" data-bs-target="#approvedModal"' : ''; ?>>
                      <div class="card-body text-white">
                        <h6 class="mb-2">✅ অনুমোদিত সদস্য</h6>
                        <p class="mb-1 small">Approved Members</p>
                        <div class="display-5 fw-bold"><?php echo englishToBanglaNumber($approved); ?></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center" style="<?php echo $processed > 0 ? 'cursor:pointer;' : ''; ?> background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);" <?php echo $processed > 0 ? 'data-bs-toggle="modal" data-bs-target="#processModal"' : ''; ?>>
                      <div class="card-body text-white">
                        <h6 class="mb-2">⏳ প্রক্রিয়াধীন সদস্য</h6>
                        <p class="mb-1 small">Processing Members</p>
                        <div class="display-5 fw-bold"><?php echo englishToBanglaNumber($processed); ?></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center" style="<?php echo $inactive > 0 ? 'cursor:pointer;' : ''; ?> background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);" <?php echo $inactive > 0 ? 'data-bs-toggle="modal" data-bs-target="#inactiveModal"' : ''; ?>>
                      <div class="card-body text-white">
                        <h6 class="mb-2">⏸️ নিষ্ক্রিয় সদস্য</h6>
                        <p class="mb-1 small">Inactive Members</p>
                        <div class="display-5 fw-bold"><?php echo englishToBanglaNumber($inactive); ?></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center" style="<?php echo $rejected > 0 ? 'cursor:pointer;' : ''; ?> background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);" <?php echo $rejected > 0 ? 'data-bs-toggle="modal" data-bs-target="#rejectedModal"' : ''; ?>>
                      <div class="card-body text-white">
                        <h6 class="mb-2">❌ প্রত্যাখ্যাত সদস্য</h6>
                        <p class="mb-1 small">Rejected Members</p>
                        <div class="display-5 fw-bold"><?php echo englishToBanglaNumber($rejected); ?></div>
                      </div>
                    </div>
                  </div>
                   <div class="col-md-12">
                    <div class="card shadow-sm border-0 text-center" style="<?php echo $paymentDoneData['total_payment_done'] > 0 ? 'cursor:pointer;' : ''; ?> background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" <?php echo $paymentDoneData['total_payment_done'] > 0 ? 'data-bs-toggle="modal" data-bs-target="#paymentDoneModal"' : ''; ?>>
                      <div class="card-body text-white">
                        <h6 class="mb-2">💳 সদস্য পেমেন্ট</h6>
                        <p class="mb-1 small">Members Payment</p>
                        <div class="display-5 fw-bold"><?php echo englishToBanglaNumber($paymentDoneData['total_payment_done']); ?></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Financial Summary Cards -->
                <h5 class="mb-3 text-primary fw-bold">আর্থিক সারসংক্ষেপ <span class="text-secondary">(Financial Summary)</span></h5>
                <div class="row g-4 mb-4">
                  <!-- Admission Fee -->
                  
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">🪪 আইডি কার্ড ফি</h6>
                        <p class="mb-1 small text-muted">ID Card Fee</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_idcard_fee'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Passbook Fee -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">📖 পাসবুক ফি</h6>
                        <p class="mb-1 small text-muted">Passbook Fee</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_passbook_fee'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Software Fee -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">💻 সফটওয়্যার ফি</h6>
                        <p class="mb-1 small text-muted">Software Fee</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_softuses_fee'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- SMS Fee -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">📱 এসএমএস ফি</h6>
                        <p class="mb-1 small text-muted">SMS Fee</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_sms_fee'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Office Rent -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">🏢 অফিস ভাড়া</h6>
                        <p class="mb-1 small text-muted">Office Rent</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_office_rent'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Office Staff -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">👥 অফিস কর্মচারী</h6>
                        <p class="mb-1 small text-muted">Office Staff</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_office_staff'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Other Fee -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-secondary mb-2">📋 অন্যান্য ফি</h6>
                        <p class="mb-1 small text-muted">Other Fee</p>
                        <div class="h5 fw-bold text-secondary mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_other_fee'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Late Fee -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-warning mb-2">⏰ বিলম্ব ফি</h6>
                        <p class="mb-1 small text-muted">Late Fee</p>
                        <div class="h5 fw-bold text-warning mb-0">৳ <?php echo englishToBanglaNumber(number_format($shareData['total_late_fee'], 2)); ?></div>
                      </div>
                    </div>
                  </div>
                
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-primary mb-2">💳 ভর্তি ফি</h6>
                        <p class="mb-1 small text-muted">Admission Fee</p>
                        <div class="h5 fw-bold text-success mb-0">৳ <?php echo englishToBanglaNumber(number_format($total_member_admission_fees, 2)); ?></div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Samity Share -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-info mb-2">🏦 সমিতি শেয়ার</h6>
                        <p class="mb-1 small text-muted">Samity Share</p>
                        <div class="h5 fw-bold text-info mb-0">৳ <?php echo englishToBanglaNumber(number_format($paymentData['total_samity_share'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Project Share -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-primary mb-2">📊 প্রকল্প শেয়ার</h6>
                        <p class="mb-1 small text-muted">Project Share</p>
                        <div class="h5 fw-bold text-primary mb-0">৳ <?php echo englishToBanglaNumber(number_format($paymentData['total_project_share'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Monthly Deposit -->
                  <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                      <div class="card-body">
                        <h6 class="text-success mb-2">📅 মাসিক জমা</h6>
                        <p class="mb-1 small text-muted">Monthly Deposit</p>
                        <div class="h5 fw-bold text-success mb-0">৳ <?php echo englishToBanglaNumber(number_format($paymentData['total_monthly'], 2)); ?></div>
                      </div>
                    </div>
                  </div>

                  </div>

                  <!-- ID Card Fee -->
                  

                <!-- Total Deposit Card -->
                <div class="row g-4 mb-4">
                  <div class="col-12">
                    <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                      <div class="card-body text-white text-center py-4">
                        <h4 class="mb-2">💰 মোট জমা পরিমাণ</h4>
                        <p class="mb-3">Total Deposit Amount</p>
                        <div class="display-4 fw-bold">৳ <?php echo englishToBanglaNumber(number_format($total_all_deposits, 2)); ?></div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Approved Members Modal -->
                <div class="modal fade" id="approvedModal" tabindex="-1" aria-labelledby="approvedModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="approvedModalLabel">✅ অনুমোদিত সদস্য তালিকা (Approved Members List)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover align-middle">
                            <thead class="table-success">
                              <tr>
                                <th>Member Code</th>
                                <th>Name (Bangla)</th>
                                <th>Name (English)</th>
                                <th>Mobile</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $stmt = $pdo->query("SELECT a.member_code, a.name_bn, a.name_en, a.mobile FROM members_info a, user_login b WHERE a.id = b.member_id and b.status='A' and b.role = 'user' ORDER BY b.id DESC");
                              while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>'.htmlspecialchars($row['member_code']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_bn']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_en']).'</td>';
                                echo '<td>'.htmlspecialchars($row['mobile']).'</td>';
                                echo '</tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="processModalLabel">⏳ প্রক্রিয়াধীন সদস্য তালিকা (Processing Members List)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover align-middle">
                            <thead class="table-primary">
                              <tr>
                                <th>Member Code</th>
                                <th>Name (Bangla)</th>
                                <th>Name (English)</th>
                                <th>Mobile</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $stmt = $pdo->query("SELECT a.member_code, a.name_bn, a.name_en, a.mobile FROM members_info a, user_login b WHERE a.id = b.member_id and b.status='P' and b.role = 'user' ORDER BY b.id DESC");
                              while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>'.htmlspecialchars($row['member_code']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_bn']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_en']).'</td>';
                                echo '<td>'.htmlspecialchars($row['mobile']).'</td>';
                                echo '</tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Pending Members Modal -->
                <div class="modal fade" id="inactiveModal" tabindex="-1" aria-labelledby="inactiveModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="inactiveModalLabel">⏸️ নিষ্ক্রিয় সদস্য তালিকা (Inactive Members List)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover align-middle">
                            <thead class="table-warning">
                              <tr>
                                <th>Member Code</th>
                                <th>Name (Bangla)</th>
                                <th>Name (English)</th>
                                <th>Mobile</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $stmt = $pdo->query("SELECT a.member_code, a.name_bn, a.name_en, a.mobile FROM members_info a, user_login b WHERE a.id = b.member_id and b.status='I' and b.role = 'user' ORDER BY b.id DESC");
                              while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>'.htmlspecialchars($row['member_code']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_bn']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_en']).'</td>';
                                echo '<td>'.htmlspecialchars($row['mobile']).'</td>';
                                echo '</tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Rejected Members Modal -->
                <div class="modal fade" id="rejectedModal" tabindex="-1" aria-labelledby="rejectedModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectedModalLabel">❌ প্রত্যাখ্যাত সদস্য তালিকা (Rejected Members List)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover align-middle">
                            <thead class="table-danger">
                              <tr>
                                <th>Member Code</th>
                                <th>Name (Bangla)</th>
                                <th>Name (English)</th>
                                <th>Mobile</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $stmt = $pdo->query("SELECT a.member_code, a.name_bn, a.name_en, a.mobile FROM members_info a, user_login b WHERE a.id = b.member_id and b.status='R' and b.role = 'user' ORDER BY b.id DESC");
                              while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>'.htmlspecialchars($row['member_code']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_bn']).'</td>';
                                echo '<td>'.htmlspecialchars($row['name_en']).'</td>';
                                echo '<td>'.htmlspecialchars($row['mobile']).'</td>';
                                echo '</tr>';
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Payment Done Modal -->
                <div class="modal fade" id="paymentDoneModal" tabindex="-1" aria-labelledby="paymentDoneModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="modal-title" id="paymentDoneModalLabel">💳 পেমেন্ট সম্পন্ন সদস্যদের তালিকা (Payment Done Members List)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="d-flex justify-content-end mb-2">
                          <button class="btn btn-danger btn-sm" onclick="downloadPDF()">Download PDF</button>
                        </div>
                        <div class="table-responsive" id="payment-done-table">
                          <table class="table table-bordered table-hover align-middle">
                            <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                              <tr>
                                <th>No#</th>
                                <th>Member Info</th>
                                <th>No Samity Share</th>
                                <th>No Project Share</th>
                                <th>Admission Fee</th>
                                <th>Samity Share Amt</th>
                                <th>Project Share Amt</th>
                                <th>Monthly Fee</th>
                                <th>সর্বমোট</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $stmt = $pdo->query("WITH share_data AS (
                                    SELECT 
                                        ms.member_id,
                                        SUM(ms.samity_share) AS samity_share,
                                        SUM(ms.no_share - ms.samity_share) AS projects
                                    FROM member_share ms
                                    GROUP BY ms.member_id
                                ),
                                
                                payment_data AS (
                                    SELECT 
                                        mp.member_id,
                                        SUM(CASE WHEN mp.payment_method = 'admission' THEN mp.amount ELSE 0 END) AS admission,
                                        SUM(CASE WHEN mp.payment_method = 'Samity Share' THEN mp.amount ELSE 0 END) AS Samity_Share,
                                        SUM(CASE WHEN mp.payment_method = 'Project Share' THEN mp.amount ELSE 0 END) AS project_share,
                                        SUM(CASE WHEN mp.payment_method NOT IN ('Samity Share','Project Share','admission') THEN mp.amount ELSE 0 END) AS monthly
                                    FROM member_payments mp
                                    WHERE mp.status = 'A'
                                    GROUP BY mp.member_id
                                )
                                
                                SELECT 
                                    ROW_NUMBER() OVER (ORDER BY a.id) AS serial_no,
                                    a.id,
                                    a.member_code,
                                    a.name_bn,
                                    COALESCE(s.samity_share,0) AS samity_share,
                                    COALESCE(s.projects,0) AS projects,
                                    COALESCE(p.admission,0) AS admission,
                                    COALESCE(p.Samity_Share,0) AS Samity_Share,
                                    COALESCE(p.project_share,0) AS project_share,
                                    COALESCE(p.monthly,0) AS monthly
                                
                                FROM members_info a
                                LEFT JOIN share_data s ON a.id = s.member_id
                                LEFT JOIN payment_data p ON a.id = p.member_id
                                
                                WHERE EXISTS (
                                    SELECT 1 
                                    FROM user_login c 
                                    WHERE c.member_id = a.id 
                                    AND c.status IN ('A','P')
                                )
                                
                                UNION ALL
                                
                                SELECT 
                                    NULL,
                                    NULL,
                                    'সর্বমোট',
                                    '',
                                    SUM(COALESCE(s.samity_share,0)),
                                    SUM(COALESCE(s.projects,0)),
                                    SUM(COALESCE(p.admission,0)),
                                    SUM(COALESCE(p.Samity_Share,0)),
                                    SUM(COALESCE(p.project_share,0)),
                                    SUM(COALESCE(p.monthly,0))
                                
                                FROM members_info a
                                LEFT JOIN share_data s ON a.id = s.member_id
                                LEFT JOIN payment_data p ON a.id = p.member_id
                                
                                WHERE EXISTS (
                                    SELECT 1 
                                    FROM user_login c 
                                    WHERE c.member_id = a.id 
                                    AND c.status IN ('A','P')
                                )");
                              while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                // ...existing code...
                                // Column-wise totals initialization
                                static $total_admission = 0;
                                static $total_samity_share = 0;
                                static $total_project_share = 0;
                                static $total_monthly = 0;

                                // Add to totals if not 'সর্বমোট' row
                                if ($row['serial_no'] !== null) {
                                  $total_admission += floatval($row['admission']);
                                  $total_samity_share += floatval($row['Samity_Share']);
                                  $total_project_share += floatval($row['project_share']);
                                  $total_monthly += floatval($row['monthly']);
                                }

                                echo '<td>'.englishToBanglaNumber(htmlspecialchars($row['serial_no'])).'</td>';
                                echo '<td>'
                                  . englishToBanglaNumber(htmlspecialchars($row['id'])) . '<br>' 
                                  . htmlspecialchars($row['member_code']) . '<br>' 
                                  . htmlspecialchars($row['name_bn']) . 
                                '</td>';
                                echo '<td>'.englishToBanglaNumber(htmlspecialchars($row['samity_share'])).'</td>';
                                echo '<td>'.englishToBanglaNumber(htmlspecialchars($row['projects'])).'</td>';
                                echo '<td>'.englishToBanglaNumber(number_format(htmlspecialchars($row['admission']), 2)).'</td>';
                                echo '<td>'.englishToBanglaNumber(number_format(htmlspecialchars($row['Samity_Share']), 2)).'</td>';
                                echo '<td>'.englishToBanglaNumber(number_format(htmlspecialchars($row['project_share']), 2)).'</td>';
                                echo '<td>'.englishToBanglaNumber(number_format(htmlspecialchars($row['monthly']), 2)).'</td>';
                                // Calculate row total for relevant columns
                                $row_total = floatval($row['admission']) + floatval($row['Samity_Share']) + floatval($row['project_share']) + floatval($row['monthly']);
                                echo '<td>'.englishToBanglaNumber(number_format($row_total, 2)).'</td>';
                                echo '</tr>';

                                
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Add your main dashboard content here -->
            </div>
        </main>
  </div>
  
</div>
<!-- Hero End -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    const element = document.getElementById('payment-done-table');
    
    // Create a wrapper for proper sizing
    const wrapper = document.createElement('div');
    wrapper.style.width = '290mm'; // A4 width
    wrapper.style.padding = '15mm';
    wrapper.style.boxSizing = 'border-box';
    wrapper.style.backgroundColor = 'white';
    wrapper.innerHTML = element.innerHTML;
    
    // Temporarily add to body
    document.body.appendChild(wrapper);
    
    const opt = {
        margin: 0,
        filename: `payment-done-table.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            letterRendering: true,
            allowTaint: true,
            scrollY: -window.scrollY,
            scrollX: 0
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'landscape'
        }
    };
    
    html2pdf().set(opt).from(wrapper).save().then(() => {
        // Remove temporary wrapper
        document.body.removeChild(wrapper);
    });
}
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
