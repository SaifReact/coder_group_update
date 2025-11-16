<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the correct path to config.php
include_once __DIR__ . '/../config/config.php';

// Fetch banners from the database
try {
    $stmt = $pdo->query("SELECT a.name_bn, b.fb, b.li, c.position_bn FROM members_info a 
    LEFT JOIN committee_member b ON b.member_id = a.id LEFT JOIN committee_role c ON c.id = b.committee_role_id 
    WHERE b.committee_role_id in (2,3,4,5,6,7)
    ORDER BY c.id ASC;");
    $comittees = $stmt->fetchAll();
} catch (Exception $e) {
    die('Error fetching banners: ' . $e->getMessage());
}
?>
<div class="container py-3">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="mb-0">Committee <span class="text-uppercase text-primary bg-light px-2">Members</span>
                ( কমিটির  <span class="text-uppercase text-primary bg-light px-2">সদস্য</span> )
                </h1>
                <a href="executive.php" class="btn btn-primary">Executive Members ( কার্যনির্বাহী সদস্য )</a>
            </div>
            <div class="row g-4">
                <?php foreach ($comittees as $comittee): ?>
                <div class="col-md-6 col-lg-2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="team-item position-relative overflow-hidden">
                        <img class="img-fluid w-100" src="assets/img/user.jpg" alt="">
                        <div class="team-overlay">
                            <small class="d-block mb-1"><?= htmlspecialchars($comittee['name_bn']); ?></small>
                            <small class="d-block mb-1"><?= htmlspecialchars($comittee['position_bn']); ?></small>
                            <div class="d-flex justify-content-center">
                                <a class="btn btn-outline-primary btn-sm-square border-2 me-2" href="<?= htmlspecialchars($comittee['fb']); ?>">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a class="btn btn-outline-primary btn-sm-square border-2 me-2" href="<?= htmlspecialchars($comittee['li']); ?>">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>