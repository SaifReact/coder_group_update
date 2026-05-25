<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../config/config.php';

$site_name_en = $_SESSION['setup']['site_name_en'] ?? '';
$site_name_bn = $_SESSION['setup']['site_name_bn'] ?? '';
$slogan_bn    = $_SESSION['setup']['slogan_bn'] ?? '';
$slogan_en    = $_SESSION['setup']['slogan_en'] ?? '';
$slogan = $slogan_bn . ($slogan_en ? ' ( ' . $slogan_en . ' )' : '');
$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
?>
    <nav class="navbar navbar-expand-lg navbar-light border-bottom border-2 border-white">
        <div class="container-fluid">
        <a href="<?= BASE_URL ?>" class="navbar-brand d-flex align-items-center gap-3">
            <?php if (!empty($_SESSION['setup']['logo'])): ?>
                <img src="<?= BASE_URL ?>assets/img/<?= htmlspecialchars($_SESSION['setup']['logo']) ?>" alt="Logo" style="height:56px; width:auto; display:block;">
            <?php endif; ?>
            <div style="line-height:1.1; max-width: calc(100vw - 280px); white-space: normal;">
                <div style="font-family: 'Poppins', Arial, sans-serif; font-size: 1.05rem; font-weight: 700; color: #008485; letter-spacing: 0.5px; line-height: 30px; text-shadow: 1px 2px 8px #fff8, 0 2px 8px #00848522;">
                    <?= htmlspecialchars($site_name_bn); ?>
                </div>
                <div style="font-family: 'Poppins', Arial, sans-serif; font-size: .8rem; font-weight: 700; color: #f29b2d; letter-spacing: 1px; text-shadow: 1px 2px 8px #fff8, 0 2px 8px #f29b2d22;">
                    <?= htmlspecialchars($site_name_en); ?>
                </div>
            </div>
        </a>
        <button type="button" class="navbar-toggler ms-auto" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-item nav-link active" href="#">
                        স্বাগতম, <b><?= htmlspecialchars($_SESSION['user_name']); ?> - <?= htmlspecialchars($_SESSION['member_code']); ?></b>
                        <?php if ($status === 'P'): ?> <span style="color:orange;">( প্রক্রিয়াধীন )</span><?php elseif ($status === 'A'): ?> <span style="color:green;">( অনুমোদিত )</span><?php endif; ?> !
                    </a>
                    <a class="nav-item nav-link active" href="../includes/logout.php">
                        Logout ( লগআউট )
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>" class="nav-item nav-link active">প্রচ্ছদ (Home)</a>
                    <a href="<?= BASE_URL ?>members.php" class="nav-item nav-link active">সদস্য (Members)</a>
                    <a href="<?= BASE_URL ?>docs.php" class="nav-item nav-link active">ডকুমেন্টস (Documents)</a>
                    <a href="<?= BASE_URL ?>projects.php" class="nav-item nav-link active">প্রকল্পসমূহ (Projects)</a>
                    <a href="<?= BASE_URL ?>form.php" class="nav-item nav-link">নিবন্ধন (Registration)</a>
                    <a href="<?= BASE_URL ?>login.php" class="nav-item nav-link">লগইন (Login)</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </nav>
    <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
        <?= htmlspecialchars($slogan); ?>
    </marquee>

