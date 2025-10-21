<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/config/config.php';

// Access specific data from session
$siteName = $_SESSION['setup']['site_name_bn'] ?? '';
$reg_no    = $_SESSION['setup']['registration_no'] ?? '';
$address    = $_SESSION['setup']['address'] ?? '';
$phone1    = $_SESSION['setup']['phone1'] ?? '';
$phone2    = $_SESSION['setup']['phone2'] ?? '';
$phone = $phone1 . ($phone2 ? ', ' . $phone2 : '');
$email   = $_SESSION['setup']['email'] ?? '';
?>
   <?php include_once __DIR__ . '/includes/open.php'; ?>
   <!-- Hero Start -->
   <div class="container-fluid pb-3 hero-header bg-light">
     <div class="container">
       <div class="row justify-content-center">
         <div class="col-12 col-md-12 col-lg-12 col-xl-12">
           <div class="glass-card-header mb-1">
             <h5 class="text-center fw-bold" style="color:#045D5D; letter-spacing:1px; text-shadow:1px 2px 8px #fff8; font-size:1.5rem; font-family:'Poppins',sans-serif;">কোন প্রশ্ন আছে? ( Have Any Query? )</h5> 
           </div>
           <form method="post" action="process/member_register_process.php" enctype="multipart/form-data">
             <div id="formErrorMsg" class="alert alert-danger" style="display:none;"></div>
             
             <div class="mb-4">
               <div class="glass-card mb-2">
                 <div class="row">
                   <div class="col-md-6">
                     <div class="mb-2">
                       <label for="name" class="form-label">নাম: <span class="text-secondary small">(Name)</span>
                       </label>
                       <input type="text" class="form-control" id="name" name="name" required>
                     </div>
                     <div class="mb-2">
                       <label for="email" class="form-label">ইমেইল: <span class="text-secondary small">(Email)</span>
                       </label>
                       <input type="text" class="form-control" id="email" name="email" required>
                     </div>
                     <div class="mb-2">
                       <label for="subject" class="form-label">বিষয়: <span class="text-secondary small">(Subject)</span>
                       </label>
                       <input type="text" class="form-control" id="subject" name="subject">
                     </div>
                     <div class="mb-2">
                       <label for="message" class="form-label">বার্তা: <span class="text-secondary small">(Message)</span>
                       </label>
                       <textarea class="form-control" id="message" name="message" rows="4"></textarea>
                     </div>
                     <div class="d-grid gap-2 mt-2">
                     <button type="submit" class="btn btn-lg btn-success rounded-pill shadow-sm" style="font-size:1.2rem;letter-spacing:1px;">Submit Application (আবেদনটি জমা দিন)</button>
                   </div>
                   </div>
                   <div class="col-md-6 mt-4 pl-5">
                     <h4><?= htmlspecialchars($siteName); ?></h4>
                     <h5>ফাইল নং- <?= htmlspecialchars($reg_no); ?></h5>
                      <p><address>
                          <i class="fa fa-map-marker-alt me-3"></i><?= htmlspecialchars($address); ?>
                      </address></p>
                    <p><i class="fa fa-phone-alt me-3"></i><?= htmlspecialchars($phone); ?></p>
                    <p><i class="fa fa-envelope me-3"></i><?= htmlspecialchars($email); ?></p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-primary btn-square border-2 me-2" href="#!"><i
                                class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-primary btn-square border-2 me-2" href="#!"><i
                                class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-primary btn-square border-2 me-2" href="#!"><i
                                class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-primary btn-square border-2 me-2" href="#!"><i
                                class="fab fa-instagram"></i></a>
                        <a class="btn btn-outline-primary btn-square border-2 me-2" href="#!"><i
                                class="fab fa-linkedin-in"></i></a>
                    </div>
                   </div>
                   
                 </div>
               </div>
               <div class="section-card">
                 <h5>গুগল ম্যাপ ( Google Map )</h5>
                 <hr />
                 <div class="row">
                   
                   
                   
                 </div>
               </div>
               
             </div>
         </div>
         </form>
       </div>
     </div>
   </div>
   </div>
   <!-- Hero End --> <?php include_once __DIR__ . '/includes/end.php'; ?>