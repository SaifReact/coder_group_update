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
        <a href="/coder_group/index.php" class="navbar-brand">
            <span style="
                display: inline-block;
                font-family: 'Poppins', Arial, sans-serif;
                font-size: .9rem;
                font-weight: 700;
                color: #b85c38;
                letter-spacing: 1.5px;
                text-shadow: 1px 2px 8px #fff8, 0 2px 8px #b85c3822;
                padding: 0.2em 0.1em;
                margin: 0.2em 0;
            ">
                <span style="vertical-align:middle; font-size: 1rem;">
                    <?= htmlspecialchars($site_name_bn); ?>
                </span><br />
                <?= htmlspecialchars($site_name_en); ?>
            </span>
        </a>
        <button type="button" class="navbar-toggler ms-auto" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-item nav-link active" href="#">
                        স্বাগতম, <b><?= htmlspecialchars($_SESSION['user_name']); ?></b>
                        <?php if ($status === 'P'): ?> <span style="color:orange;">( প্রক্রিয়াধীন )</span><?php elseif ($status === 'A'): ?> <span style="color:green;">( অনুমোদিত )</span><?php endif; ?> !
                    </a>
                    <a class="nav-item nav-link active" href="../includes/logout.php">
                        Logout ( লগআউট )
                    </a>
                <?php else: ?>
                    <a href="/" class="nav-item nav-link active">প্রচ্ছদ (Home)</a>
                    <a href="members.php" class="nav-item nav-link active">সদস্য (Members)</a>
                    <a href="form.php" class="nav-item nav-link">নিবন্ধন (Registration)</a>
                    <a href="login.php" class="nav-item nav-link">লগইন (Login)</a>
                    <a href="contact.php" class="nav-item nav-link">যোগাযোগ (Contact)</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </nav>
    <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
        <?= htmlspecialchars($slogan); ?>
    </marquee>

