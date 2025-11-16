<style>
.executive-section {
    padding: 60px 0;
}
.section-title {
    text-align: center;
    margin-bottom: 50px;
    position: relative;
}
.section-title h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #045D5D;
    margin-bottom: 15px;
}
.section-title .subtitle {
    font-size: 1.3rem;
    color: #008080;
    font-weight: 600;
}
.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #045D5D, #008080);
    border-radius: 2px;
}
.member-card {
    background: white;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 30px;
    height: 100%;
}
.member-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}
.member-img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin: 0 auto 20px;
    overflow: hidden;
    border: 5px solid #008080;
    box-shadow: 0 5px 15px rgba(0,128,128,0.3);
}
.member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.member-position {
    font-size: 0.9rem;
    color: #008080;
    font-weight: 600;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.member-name {
    font-size: 1.3rem;
    color: #045D5D;
    font-weight: 700;
    margin-bottom: 20px;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
}
.social-links a {
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
.social-links a:hover {
    transform: scale(1.1);
    background: linear-gradient(135deg, #008080, #045D5D);
}

</style>

<?php 
include_once __DIR__ . '/config/config.php'; 

// Fetch Advisors (উপদেষ্টা)
try {
    $stmt = $pdo->query("SELECT a.name_bn, b.fb, b.li, c.position_bn FROM members_info a 
    LEFT JOIN committee_member b ON b.member_id = a.id 
    LEFT JOIN committee_role c ON c.id = b.committee_role_id 
    WHERE b.committee_role_id = 1
    ORDER BY b.id ASC");
    $advisors = $stmt->fetchAll();
} catch (Exception $e) {
    $advisors = [];
}

// Fetch Entrepreneurs (উদ্যোক্তা) - role_id 9
try {
    $stmt = $pdo->query("SELECT a.name_bn, b.fb, b.li FROM members_info a 
    LEFT JOIN committee_member b ON b.member_id = a.id 
    WHERE b.role = 'Entrepreneur'
    ORDER BY b.id ASC");
    $entrepreneurs = $stmt->fetchAll();
} catch (Exception $e) {
    $entrepreneurs = [];
}

// Fetch Executive Committee (কার্যকরী কমিটি)
try {
    $stmt = $pdo->query("SELECT a.name_bn, b.fb, b.li, c.position_bn FROM members_info a 
    LEFT JOIN committee_member b ON b.member_id = a.id 
    LEFT JOIN committee_role c ON c.id = b.committee_role_id 
    WHERE b.committee_role_id = 8
    ORDER BY b.id ASC");
    $executives = $stmt->fetchAll();
} catch (Exception $e) {
    $executives = [];
}
?>
<?php include_once __DIR__ . '/includes/open.php'; ?>
   <!-- Hero Start -->
   
   <!-- Hero End -->

   <!-- Advisors Section Start -->
   <?php if (!empty($advisors)): ?>
   <div class="executive-section">
     <div class="container">
       <div class="section-title">
         <h2>Advisors</h2>
         <div class="subtitle">উপদেষ্টা</div>
       </div>
       <div class="row justify-content-center">
         <?php foreach ($advisors as $advisor): ?>
         <div class="col-lg-3 col-md-4 col-sm-6">
           <div class="member-card">
             <div class="member-img">
               <img src="assets/img/user.jpg" alt="<?= htmlspecialchars($advisor['name_bn']); ?>">
             </div>
             <div class="member-position"><?= htmlspecialchars($advisor['position_bn']); ?></div>
             <div class="member-name"><?= htmlspecialchars($advisor['name_bn']); ?></div>
             <div class="social-links">
               <?php if (!empty($advisor['fb'])): ?>
               <a href="<?= htmlspecialchars($advisor['fb']); ?>" target="_blank" title="Facebook">
                 <i class="fab fa-facebook-f"></i>
               </a>
               <?php endif; ?>
               <?php if (!empty($advisor['li'])): ?>
               <a href="<?= htmlspecialchars($advisor['li']); ?>" target="_blank" title="LinkedIn">
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
   <?php endif; ?>
   <!-- Advisors Section End -->

   <!-- Entrepreneurs Section Start -->
   <?php if (!empty($entrepreneurs)): ?>
   <div class="executive-section" style="background: linear-gradient(135deg, #e0e7ef 0%, #f5f7fa 100%);">
     <div class="container">
       <div class="section-title">
         <div class="subtitle">উদ্যোক্তা ( Entrepreneurs )</div>
       </div>
       <div class="row justify-content-center">
         <?php foreach ($entrepreneurs as $entrepreneur): ?>
         <div class="col-lg-3 col-md-4 col-sm-6">
           <div class="member-card">
             <div class="member-img">
               <img src="assets/img/user.jpg" alt="<?= htmlspecialchars($entrepreneur['name_bn']); ?>">
             </div>
             <div class="member-name"><?= htmlspecialchars($entrepreneur['name_bn']); ?></div>
             <div class="social-links">
               <?php if (!empty($entrepreneur['fb'])): ?>
               <a href="<?= htmlspecialchars($entrepreneur['fb']); ?>" target="_blank" title="Facebook">
                 <i class="fab fa-facebook-f"></i>
               </a>
               <?php endif; ?>
               <?php if (!empty($entrepreneur['li'])): ?>
               <a href="<?= htmlspecialchars($entrepreneur['li']); ?>" target="_blank" title="LinkedIn">
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
   <?php endif; ?>
   <!-- Entrepreneurs Section End -->

   <!-- Executive Committee Section Start -->
   <?php if (!empty($executives)): ?>
   <div class="executive-section">
     <div class="container">
       <div class="section-title">
         <h2>Executive Committee</h2>
         <div class="subtitle">কার্যকরী কমিটি</div>
       </div>
       <div class="row justify-content-center">
         <?php foreach ($executives as $executive): ?>
         <div class="col-lg-3 col-md-4 col-sm-6">
           <div class="member-card">
             <div class="member-img">
               <img src="assets/img/user.jpg" alt="<?= htmlspecialchars($executive['name_bn']); ?>">
             </div>
             <div class="member-position"><?= htmlspecialchars($executive['position_bn']); ?></div>
             <div class="member-name"><?= htmlspecialchars($executive['name_bn']); ?></div>
             <div class="social-links">
               <?php if (!empty($executive['fb'])): ?>
               <a href="<?= htmlspecialchars($executive['fb']); ?>" target="_blank" title="Facebook">
                 <i class="fab fa-facebook-f"></i>
               </a>
               <?php endif; ?>
               <?php if (!empty($executive['li'])): ?>
               <a href="<?= htmlspecialchars($executive['li']); ?>" target="_blank" title="LinkedIn">
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
   <?php endif; ?>
   <!-- Executive Committee Section End -->

<?php include_once __DIR__ . '/includes/end.php'; ?>
     