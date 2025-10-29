<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
$role = $_SESSION['role'] ?? '';
?>

<nav class="col-12 col-md-2 col-lg-2 bg-light sidebar shadow-sm rounded-3 mb-4 mb-md-0 p-0" style="min-height:500px; font-family: 'Poppins', Arial, sans-serif;">
    <div class="position-sticky pt-5">
        <ul class="nav flex-column">
            <?php if ($role === 'Admin'): ?>
                <li class="nav-item mb-3">
                    <a class="nav-link active text-dark fw-bold" href="../admin/index.php" style="font-size: .8rem;">
                        <i class="fa fa-tachometer-alt me-2 text-primary"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/setup.php" style="font-size: .8rem;">
                        <i class="fa fa-cogs me-2 text-secondary"></i> Setup
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/approval.php" style="font-size: .8rem;">
                        <i class="fa fa-user-check me-2 text-success"></i> Approval
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/payment_approved.php" style="font-size: .8rem;">
                        <i class="fa fa-credit-card me-2 text-info"></i> Payment
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/company.php" style="font-size: .8rem;">
                        <i class="fa fa-building me-2 text-warning"></i> Company
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/images.php" style="font-size: .8rem;">
                        <i class="fa fa-images me-2 text-danger"></i> Images
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/service.php" style="font-size: .8rem;">
                        <i class="fa fa-concierge-bell me-2 text-purple"></i> Service
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/committee.php" style="font-size: .8rem;">
                        <i class="fa fa-users me-2 text-success"></i> Committee
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="../admin/project.php" style="font-size: .8rem;">
                        <i class="fa fa-project-diagram me-2 text-info"></i> Project
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item mb-3">
                    <a class="nav-link active text-dark fw-bold" href="index.php" style="font-size: .8rem;">
                        <i class="fa fa-tachometer-alt me-2 text-primary"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="documents.php" style="font-size: .8rem;">
                        <i class="fa fa-file-upload me-2 text-success"></i> Documents
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="payment.php" style="font-size: .8rem;">
                        <i class="fa fa-credit-card me-2 text-danger"></i> Payment
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="receipt.php" style="font-size: .8rem;">
                        <i class="fa fa-receipt me-2 text-info"></i> Receipt
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="passbook.php" style="font-size: .8rem;">
                        <i class="fa fa-receipt me-2 text-info"></i> passbook
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark fw-bold" href="password.php" style="font-size: .8rem;">
                        <i class="fa fa-key me-2 text-warning"></i> Password
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
    .nav-link {
        transition: all 0.3s ease-in-out;
        border-radius: 5px;
        padding: 5px 15px;
    }
    .nav-link:hover {
        background-color: #f8f9fa;
        color: #007bff !important;
        text-decoration: none;
    }
    .nav-link i {
        font-size: 1rem;
    }
    /* Extra Custom Color */
    .text-purple { color: #6f42c1 !important; }
</style>
