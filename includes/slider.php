<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ensure the correct path to config.php
include_once __DIR__ . '/../config/config.php';

// Fetch banners from the database
try {
    $stmt = $pdo->query("SELECT banner_image FROM banner ORDER BY id ASC");
    $banners = $stmt->fetchAll();
} catch (Exception $e) {
    die('Error fetching banners: ' . $e->getMessage());
}
?>

<div class="row g-5 align-items-center mb-5">
    <div class="col-lg-6">
        <h1 class="display-1 mb-4 animated slideInRight">May Allah Make Our <span class="text-primary">Future</span>
            Better</h1>
        <h5 class="d-inline-block border border-2 border-white py-3 px-5 mb-0 animated slideInRight">
            আল্লাহ আমাদের ভবিষ্যৎ আরও সুন্দর করে দিন...</h5>
    </div>
    <div class="col-lg-6">
        <div class="owl-carousel header-carousel animated fadeIn">
            <?php foreach ($banners as $banner): ?>
                <img class="img-fluid" src="banner/<?= htmlspecialchars($banner['banner_image']); ?>" alt="Banner Image">
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="row g-5 animated fadeIn">
    <div class="col-md-6 col-lg-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 btn-square border border-2 border-white me-3">
                <i class="fa fa-robot text-primary"></i>
            </div>
            <h5 class="lh-base mb-0">Pronouncement → দৃঢ়ভাবে</h5>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 btn-square border border-2 border-white me-3">
                <i class="fa fa-robot text-primary"></i>
            </div>
            <h5 class="lh-base mb-0">Sustainable → টেকসই</h5>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 btn-square border border-2 border-white me-3">
                <i class="fa fa-robot text-primary"></i>
            </div>
            <h5 class="lh-base mb-0">Innovative → সৃজনশীল</h5>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 btn-square border border-2 border-white me-3">
                <i class="fa fa-robot text-primary"></i>
            </div>
            <h5 class="lh-base mb-0">Friendly → সৌহার্দ্যপূর্ণ</h5>
        </div>
    </div>
</div>
