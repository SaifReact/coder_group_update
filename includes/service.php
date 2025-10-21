<?php
// Ensure the correct path to config.php
include_once __DIR__ . '/../config/config.php';

// Fetch banners from the database
try {
    $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $stmt->fetchAll();
} catch (Exception $e) {
    die('Error fetching banners: ' . $e->getMessage());
}
?>

<!-- Feature Start -->
    <div class="container-fluid py-3">
        <div class="container">
            <div class="text-center wow fadeIn" data-wow-delay="0.1s">
                <h1 class="mb-5">Goal & Objectives
                <span class="text-uppercase text-primary bg-light px-2">( লক্ষ্য ও উদ্দেশ্য )
    </span>            </h1>
            </div>
            <div class="row g-5">
                <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                    <div class="choose-card-wrapper">
						<div class="choose-card">
							<div class="icon">
								<i class="fa <?= htmlspecialchars($service['icon']); ?> fa-3x text-primary mb-4"></i>
							</div>
							<div class="content-title">
								<h4> <?= htmlspecialchars($service['service_name_bn']); ?></h4>
								<h6> <?= htmlspecialchars($service['service_name_en']); ?></h6>
							</div>
							<div>
								<p><?= strip_tags($service['about_service'], '<p><ul><li><b><i><br>'); ?></p>
							</div>
						</div>
					</div>
				</div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Feature End -->