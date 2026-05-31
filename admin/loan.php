<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../includes/head.php';
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';

$activeTab = 'loan-info';
$allowedTabs = ['loan-info', 'loan-serCharge', 'loan-documents'];
if (isset($_GET['tab']) && in_array($_GET['tab'], $allowedTabs, true)) {
    $activeTab = $_GET['tab'];
}
?>
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Loan Product Entry</h3>
                <hr class="mb-4" />
                <ul class="nav nav-tabs mb-3" id="loanTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $activeTab === 'loan-info' ? 'active' : ''; ?>" id="loan-info-tab" data-bs-toggle="tab" data-bs-target="#loan-info" type="button" role="tab">প্রোডাক্ট তথ্য</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $activeTab === 'loan-serCharge' ? 'active' : ''; ?>" id="loan-serCharge-tab" data-bs-toggle="tab" data-bs-target="#loan-serCharge" type="button" role="tab">প্রোডাক্টের চার্জ</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $activeTab === 'loan-documents' ? 'active' : ''; ?>" id="loan-documents-tab" data-bs-toggle="tab" data-bs-target="#loan-documents" type="button" role="tab">প্রয়োজনীয় ডকুমেন্টস</button>
                    </li>
                </ul>
                <div class="tab-content" id="loanTabsContent">
                    <!-- Loan Info Tab -->
                    <div class="tab-pane fade <?php echo $activeTab === 'loan-info' ? 'show active' : ''; ?>" id="loan-info" role="tabpanel">
                        <?php include 'loan-info-tab.php'; ?>
                    </div>
                    <!-- Loan Service Charge Tab -->
                    <div class="tab-pane fade <?php echo $activeTab === 'loan-serCharge' ? 'show active' : ''; ?>" id="loan-serCharge" role="tabpanel">
                        <?php include 'loan-serCharge-tab.php'; ?>
                    </div>
                    <div class="tab-pane fade <?php echo $activeTab === 'loan-documents' ? 'show active' : ''; ?>" id="loan-documents" role="tabpanel">
                        <?php include 'loan-documents-tab.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</div>
<?php include_once __DIR__ . '/../includes/end.php'; ?>


