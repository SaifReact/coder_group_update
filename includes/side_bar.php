<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
$role = $_SESSION['role'] ?? '';
?>
<div class="container-fluid pb-3 hero-header bg-light">
<div class="row" style="padding: 0px 20px 0px 38px">
<nav class="col-12 col-md-2 col-lg-2 bg-dark shadow-sm">
    <div class="position-sticky pt-3">
        <div class="sidebar">
            <nav class="nav flex-column">
                <h5 class="mb-3 text-primary fw-bold">Menu <span class="text-secondary">( মেন্যু )</span></h5>
                <hr class="mb-4" />
                <?php if ($role === 'Admin'): ?>
                <a class="nav-link" href="index.php">
                    <span class="icon"><i class="bi bi-speedometer2"></i></span> 
                    <span class="description">Dashboard</span>
                </a>
                <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#approvalMenu" aria-expanded="false" aria-controls="approvalMenu">
                    <span class="icon"><i class="bi bi-check-circle"></i></span> 
                    <span class="description"> Approval <i class="bi bi-caret-down-fill"></i></span></a>
                    <div class="sub-menu collapse" id="approvalMenu">
                        <a class="nav-link" href="approval.php">
                            <span class="icon"><i class="bi bi-person-lines-fill"></i></span> 
                            <span class="description"> Members </span>
                        </a>
                        <a class="nav-link" href="payment_approval.php">
                            <span class="icon"><i class="bi bi-cash-coin"></i></span> 
                            <span class="description"> Payments </span>
                        </a>
                    </div>

                <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#setupMenu" aria-expanded="false" aria-controls="setupMenu">
                    <span class="icon"><i class="bi bi-gear"></i></span> 
                    <span class="description"> Setup <i class="bi bi-caret-down-fill"></i></span></a>
                    <div class="sub-menu collapse" id="setupMenu">
                    <a class="nav-link" href="company.php">
                        <span class="icon"><i class="bi bi-building"></i></span> 
                        <span class="description"> Company </span>
                    </a>
                    <a class="nav-link" href="meeting.php">
                        <span class="icon"><i class="bi bi-calendar-event"></i></span> 
                        <span class="description"> Meeting </span>
                    </a>
                    <a class="nav-link" href="project.php">
                        <span class="icon"><i class="bi bi-kanban"></i></span> 
                        <span class="description"> Project </span>
                    </a>
                    <a class="nav-link" href="imgdocs.php">
                        <span class="icon"><i class="bi bi-file-earmark-image"></i></span> 
                        <span class="description"> Img & Docs </span>
                    </a>
                    <a class="nav-link" href="service.php">
                        <span class="icon"><i class="bi bi-wrench"></i></span> 
                        <span class="description"> Services </span>
                    </a>
                    <a class="nav-link" href="committee.php">
                        <span class="icon"><i class="bi bi-people-fill"></i></span> 
                        <span class="description"> Committee </span>
                    </a>
                </div>

                <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#accountsMenu" aria-expanded="false" aria-controls="accountsMenu">
                    <span class="icon"><i class="bi bi-currency-dollar"></i></span> 
                    <span class="description"> Accounts <i class="bi bi-caret-down-fill"></i></span></a>
                    <div class="sub-menu collapse" id="accountsMenu">
                    <a class="nav-link" href="general_ledger.php">
                        <span class="icon"><i class="bi bi-journal-bookmark"></i></span> 
                        <span class="description"> Gl Setup </span>
                    </a>
                    <a class="nav-link" href="gl_mapping.php">
                        <span class="icon"><i class="bi bi-journal-bookmark"></i></span> 
                        <span class="description"> Gl Mapping </span>
                    </a>
                    <a class="nav-link" href="voucher.php">
                        <span class="icon"><i class="bi bi-journal-bookmark"></i></span> 
                        <span class="description"> Voucher Posting </span>
                    </a>
                    </div>

                    <a class="nav-link" data-bs-toggle="collapse" data-bs-target="#expenseMenu" aria-expanded="false" aria-controls="expenseMenu">
                    <span class="icon"><i class="bi bi-wallet2"></i></span> 
                    <span class="description"> Expense <i class="bi bi-caret-down-fill"></i></span></a>
                    <div class="sub-menu collapse" id="expenseMenu">
                    <a class="nav-link" href="expense_category.php">
                        <span class="icon"><i class="bi bi-tags"></i></span> 
                        <span class="description"> Expense Category </span>
                    </a>
                    <a class="nav-link" href="expenses.php">
                        <span class="icon"><i class="bi bi-file-earmark-minus"></i></span> 
                        <span class="description"> Expenses </span>
                    </a>
                    </div>
                <?php else: ?>
                <a class="nav-link"
                   href="index.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-speedometer2"></i></span> 
                    <span class="description">Dashboard</span>
                </a>
                <a class="nav-link" 
                   href="meeting.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-calendar-event"></i></span> 
                    <span class="description"> Meeting </span>
                </a>
                <a class="nav-link"
                   href="documents.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-file-earmark-text"></i></span> 
                    <span class="description">Documents</span>
                </a>
                <a class="nav-link"
                   href="project_shares.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-share"></i></span> 
                    <span class="description">Add Share</span>
                </a>
                <a class="nav-link"
                   href="payment.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-credit-card"></i></span> 
                    <span class="description">Payment</span>
                </a>
                <a class="nav-link"
                   href="receipt.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-receipt"></i></span> 
                    <span class="description">Receipt</span>
                </a>
                <a class="nav-link"
                   href="passbook.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-journal-text"></i></span> 
                    <span class="description">Passbook</span>
                </a>
                <a class="nav-link"
                   href="certificate.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-award"></i></span> 
                    <span class="description">Certificate</span>
                </a>
                
                <a class="nav-link"
                   href="password.php" style="font-size:.8rem;">
                    <span class="icon"><i class="bi bi-key"></i></span> 
                    <span class="description">Password</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
</nav>


<style>
.sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 15px;
    color: #FFF;

}

.sidebar .description {
    font-size: 14px;
}

.sidebar .nav-link:hover {
    background-color: #b3d109ff;
}

.sidebar .sub-menu {
    background-color: #002b64ff;
    padding-left: 5%;
}
</style>


