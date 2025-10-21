<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../config/config.php';

$about_bn    = $_SESSION['setup']['about_text'] ?? '';
$about_en    = $_SESSION['setup']['about_text_en'] ?? '';
$logo        = $_SESSION['setup']['logo'] ?? '';
?>

<div class="row g-5">
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-4 wow fadeIn" data-wow-delay="0.1s" style="display: flex; align-items: center; justify-content: center;">
                            <img class="img-fluid" src="<?php echo BASE_URL; ?>assets/img/<?= htmlspecialchars($logo) ?>" alt="CPSSL Logo">
                        </div>
                        <div class="col-8 wow fadeIn" data-wow-delay="0.3s">
                            <h1 class="mb-4" style="font-size: 2.2rem;"><span class="text-uppercase text-primary bg-light px-2">History</span> of Our CPSSL</h1>
                            <p class="mb-2" style="font-size: 14px; text-align: justify"><?= strip_tags($about_en, '<p><br><b><strong><i><u>'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4"><span class="text-uppercase text-primary bg-light px-2">আমাদের</span>  সৃষ্টির ইতিহাস</h1>
                    <p class="mb-2" style="font-size: 14px; text-align: justify"><?= strip_tags($about_bn, '<p><br><b><strong><i><u>'); ?></p>
                </div>
            </div>