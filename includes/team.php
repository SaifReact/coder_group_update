<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../config/config.php';

try {
    $stmt = $pdo->query("SELECT a.name_bn, a.profile_image, b.fb, b.li, c.position_bn FROM members_info a
    LEFT JOIN committee_member b ON b.member_id = a.id LEFT JOIN committee_role c ON c.id = b.committee_role_id
    WHERE b.committee_role_id in (2,3,4,5,6,7)
    ORDER BY c.id ASC;");
    $comittees = $stmt->fetchAll();
} catch (Exception $e) {
    die('Error fetching members: ' . $e->getMessage());
}
?>

<style>
.team-section { padding: 15px 0; }
.team-member-card {
    background: white;
    border-radius: 15px;
    padding: 20px 15px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 30px;
    height: 100%;
}
.team-member-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}
.team-member-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 20px;
    overflow: hidden;
    border: 5px solid #008080;
    box-shadow: 0 5px 15px rgba(0,128,128,0.3);
}
.team-member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.team-member-position {
    font-size: 0.9rem;
    color: #008080;
    font-weight: 600;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.team-member-name {
    font-size: 1.1rem;
    color: #045D5D;
    font-weight: 700;
    margin-bottom: 20px;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.team-social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
}
.team-social-links a {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #045D5D, #008080);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.2rem;
}
.team-social-links a:hover {
    transform: scale(1.1);
    background: linear-gradient(135deg, #008080, #045D5D);
}
</style>

<div class="team-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="mb-0">Committee <span class="text-uppercase text-primary bg-light px-2">Members</span>
            ( কমিটির <span class="text-uppercase text-primary bg-light px-2">সদস্য</span> )
            </h1>
            <a href="executive.php" class="btn btn-primary">Executive Members ( কার্যনির্বাহী সদস্য )</a>
        </div>
        <div class="row justify-content-center g-4">
            <?php foreach ($comittees as $comittee): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="team-member-card">
                    <div class="team-member-img">
                        <img src="<?= htmlspecialchars(!empty($comittee['profile_image']) ? $comittee['profile_image'] : 'assets/img/user.jpg') ?>"
                             alt="<?= htmlspecialchars($comittee['name_bn']) ?>">
                    </div>
                    <div class="team-member-position"><?= htmlspecialchars($comittee['position_bn']) ?></div>
                    <div class="team-member-name"><?= htmlspecialchars($comittee['name_bn']) ?></div>
                    <div class="team-social-links">
                        <?php if (!empty($comittee['fb'])): ?>
                        <a href="<?= htmlspecialchars($comittee['fb']) ?>" target="_blank" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($comittee['li'])): ?>
                        <a href="<?= htmlspecialchars($comittee['li']) ?>" target="_blank" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
