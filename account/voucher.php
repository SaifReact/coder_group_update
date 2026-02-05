<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Account') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../includes/head.php';
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php';
?>
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">Voucher Entry</h3>
                <hr class="mb-4" />
                <ul class="nav nav-tabs mb-3" id="voucherTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">পেমেন্ট ভাউচার</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="receipt-tab" data-bs-toggle="tab" data-bs-target="#receipt" type="button" role="tab">রিসিপ্ট ভাউচার</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="journal-tab" data-bs-toggle="tab" data-bs-target="#journal" type="button" role="tab">জার্নাল ভাউচার</button>
                    </li>
                </ul>
                <div class="tab-content" id="voucherTabsContent">
                    <!-- Payment Voucher Tab -->
                    <div class="tab-pane fade show active" id="payment" role="tabpanel">
                        <?php include 'voucher_payment_tab.php'; ?>
                    </div>
                    <!-- Receipt Voucher Tab -->
                    <div class="tab-pane fade" id="receipt" role="tabpanel">
                        <?php include 'voucher_receipt_tab.php'; ?>
                    </div>
                    <!-- Journal Voucher Tab -->
                    <div class="tab-pane fade" id="journal" role="tabpanel">
                        <?php include 'voucher_journal_tab.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</div>
<?php include_once __DIR__ . '/../includes/end.php'; ?>
