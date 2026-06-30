   <?php include_once __DIR__ . '/config/config.php'; ?>
   <?php include_once __DIR__ . '/includes/open.php'; ?>
   <!-- Hero Start -->
   <div class="container-fluid pb-5 hero-header bg-light">
     <div class="container">
       <div class="row justify-content-center">
         <div class="col-12 col-md-12 col-lg-12 col-xl-6">
           <div class="glass-card">
             <h5 class="text-center fw-bold mb-4" style="color:#045D5D; letter-spacing:1px; text-shadow:1px 2px 8px #fff8; font-size:1.5rem; font-family:'Poppins',sans-serif;">লগইন ( Login )</h5>
             <hr />
             <div class="mb-4">
               <form method="post" action="process/login_process.php">
                 <div class="mb-2">
                   <label for="username" class="form-label">ইউজারনেম <span class="text-secondary small">( Username )</span>
                    </label>
                   <input type="text" class="form-control" id="username" name="username" required autofocus>
                 </div>
                 <div class="mb-4">
                     <label for="password" class="form-label">পাসওয়ার্ড <span class="text-secondary small">( Password )</span>
                     </label>
                     <div class="input-group">
                       <input type="password" class="form-control" id="password" name="password" required>
                       <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)" tabindex="-1">
                         <i class="fa fa-eye"></i>
                       </button>
                     </div>
                 </div>
                 <div class="form-check mt-3">
                   <input class="form-check-input" type="checkbox" value="1" >
                   <label class="form-check-label"> আমাকে মনে রেখো ( Remember Me ) </label>
                 </div>
                 <div class="text-center mt-4">
                   <button type="submit" class="btn btn-success btn-md rounded-pill px-5" style="letter-spacing:1px;">লগইন করতে এগিয়ে যান ( Proceed to Login )</button>
                 </div>
               </form>

               <!-- Forget Password Link -->
               <div class="text-center mt-3">
                 <a href="#" class="text-danger text-decoration-none small fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#forgetPasswordModal">
                   <i class="bi bi-key me-1"></i> পাসওয়ার্ড ভুলে গেছেন? ( Forgot Password? )
                 </a>
               </div>

             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
   <!-- Hero End -->

   <!-- Forget Password Modal -->
   <div class="modal fade" id="forgetPasswordModal" tabindex="-1" aria-labelledby="forgetPasswordModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
         <div class="modal-header bg-primary text-white">
           <h5 class="modal-title" id="forgetPasswordModalLabel">
             <i class="bi bi-key me-2"></i> পাসওয়ার্ড রিসেট <span class="fw-normal small">( Forgot Password )</span>
           </h5>
           <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
         </div>
         <div class="modal-body">

           <!-- Alert area -->
           <div id="fpAlert" class="d-none mb-3"></div>

           <!-- Success result area (shown after reset) -->
           <div id="fpResult" class="d-none alert alert-success text-center">
             <p class="mb-2 fw-bold fs-5">✅ পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে!</p>
             <p class="mb-0 text-muted small">এখন আপনার নতুন পাসওয়ার্ড দিয়ে লগইন করুন।<br>(You can now log in with your new password.)</p>
           </div>

           <!-- Input form (hidden after success) -->
           <div id="fpForm">
             <p class="text-muted small mb-3">
               NID নম্বর ও জন্ম তারিখ দিয়ে পরিচয় নিশ্চিত করুন, তারপর নতুন পাসওয়ার্ড দিন।<br>
               <span class="text-secondary">(Verify with NID &amp; Date of Birth, then set your new password.)</span>
             </p>
             <div class="mb-3">
               <label class="form-label fw-semibold">
                 NID নম্বর <span class="text-secondary small">(National ID Number)</span>
               </label>
               <input type="text" class="form-control" id="fpNid" placeholder="আপনার NID নম্বর দিন">
             </div>
             <div class="mb-3">
               <label class="form-label fw-semibold">
                 জন্ম তারিখ <span class="text-secondary small">(Date of Birth)</span>
               </label>
               <input type="date" class="form-control" id="fpDob">
             </div>
             <hr class="my-3">
             <div class="mb-3">
               <label class="form-label fw-semibold">
                 নতুন পাসওয়ার্ড <span class="text-secondary small">(New Password)</span>
               </label>
               <div class="input-group">
                 <input type="password" class="form-control" id="fpNewPass" placeholder="নতুন পাসওয়ার্ড দিন">
                 <button type="button" class="btn btn-outline-secondary" onclick="toggleFpPass('fpNewPass', this)" tabindex="-1">
                   <i class="fa fa-eye"></i>
                 </button>
               </div>
             </div>
             <div class="mb-1">
               <label class="form-label fw-semibold">
                 পাসওয়ার্ড নিশ্চিত করুন <span class="text-secondary small">(Confirm Password)</span>
               </label>
               <div class="input-group">
                 <input type="password" class="form-control" id="fpConfirmPass" placeholder="আবার পাসওয়ার্ড দিন">
                 <button type="button" class="btn btn-outline-secondary" onclick="toggleFpPass('fpConfirmPass', this)" tabindex="-1">
                   <i class="fa fa-eye"></i>
                 </button>
               </div>
               <div id="fpPassMismatch" class="text-danger small mt-1 d-none">পাসওয়ার্ড মিলছে না! (Passwords do not match)</div>
             </div>
           </div>

         </div>
         <div class="modal-footer" id="fpFooter">
           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল (Cancel)</button>
           <button type="button" class="btn btn-primary" id="fpSubmitBtn" onclick="submitForgotPassword()">
             <span id="fpBtnText"><i class="bi bi-arrow-clockwise me-1"></i> পাসওয়ার্ড রিসেট করুন</span>
             <span id="fpBtnSpinner" class="d-none">
               <span class="spinner-border spinner-border-sm me-1"></span> প্রক্রিয়া চলছে...
             </span>
           </button>
         </div>
       </div>
     </div>
   </div>

   <script>
   function toggleFpPass(id, btn) {
     var inp = document.getElementById(id);
     var isHidden = inp.type === 'password';
     inp.type = isHidden ? 'text' : 'password';
     btn.querySelector('i').className = isHidden ? 'fa fa-eye-slash' : 'fa fa-eye';
   }

   function submitForgotPassword() {
     var nid        = document.getElementById('fpNid').value.trim();
     var dob        = document.getElementById('fpDob').value.trim();
     var newPass    = document.getElementById('fpNewPass').value;
     var confirmPass= document.getElementById('fpConfirmPass').value;
     var alertEl    = document.getElementById('fpAlert');
     var mismatch   = document.getElementById('fpPassMismatch');

     mismatch.classList.add('d-none');
     alertEl.className = 'd-none';

     if (!nid || !dob) {
       alertEl.className = 'alert alert-warning mb-3';
       alertEl.textContent = 'NID এবং জন্ম তারিখ উভয়ই প্রয়োজন।';
       return;
     }
     if (!newPass) {
       alertEl.className = 'alert alert-warning mb-3';
       alertEl.textContent = 'নতুন পাসওয়ার্ড দিন। (New password is required.)';
       return;
     }
     if (newPass.length < 6) {
       alertEl.className = 'alert alert-warning mb-3';
       alertEl.textContent = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে। (Minimum 6 characters.)';
       return;
     }
     if (newPass !== confirmPass) {
       mismatch.classList.remove('d-none');
       return;
     }

     // Show spinner
     document.getElementById('fpBtnText').classList.add('d-none');
     document.getElementById('fpBtnSpinner').classList.remove('d-none');
     document.getElementById('fpSubmitBtn').disabled = true;

     var formData = new FormData();
     formData.append('nid', nid);
     formData.append('dob', dob);
     formData.append('new_password', newPass);

     fetch('process/forget_password_process.php', {
       method: 'POST',
       body: formData
     })
     .then(r => r.json())
     .then(function(data) {
       document.getElementById('fpBtnText').classList.remove('d-none');
       document.getElementById('fpBtnSpinner').classList.add('d-none');
       document.getElementById('fpSubmitBtn').disabled = false;

       if (data.success) {
         document.getElementById('fpForm').classList.add('d-none');
         document.getElementById('fpFooter').classList.add('d-none');
         document.getElementById('fpResult').classList.remove('d-none');
       } else {
         alertEl.className = 'alert alert-danger mb-3';
         alertEl.textContent = data.message;
       }
     })
     .catch(function() {
       document.getElementById('fpBtnText').classList.remove('d-none');
       document.getElementById('fpBtnSpinner').classList.add('d-none');
       document.getElementById('fpSubmitBtn').disabled = false;
       alertEl.className = 'alert alert-danger mb-3';
       alertEl.textContent = 'সার্ভার ত্রুটি। আবার চেষ্টা করুন। (Server error, please try again.)';
     });
   }

   // Reset modal state when closed
   document.getElementById('forgetPasswordModal').addEventListener('hidden.bs.modal', function() {
     document.getElementById('fpNid').value = '';
     document.getElementById('fpDob').value = '';
     document.getElementById('fpNewPass').value = '';
     document.getElementById('fpConfirmPass').value = '';
     document.getElementById('fpAlert').className = 'd-none';
     document.getElementById('fpPassMismatch').classList.add('d-none');
     document.getElementById('fpResult').classList.add('d-none');
     document.getElementById('fpForm').classList.remove('d-none');
     document.getElementById('fpFooter').classList.remove('d-none');
     document.getElementById('fpSubmitBtn').disabled = false;
     document.getElementById('fpBtnText').classList.remove('d-none');
     document.getElementById('fpBtnSpinner').classList.add('d-none');
   });
   </script>

   <?php include_once __DIR__ . '/includes/end.php'; ?>
