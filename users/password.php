<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

include_once __DIR__ . '/../includes/open.php';
?>

<!-- Hero Start -->
<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
      <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
            <div class="container">
               <div class="card shadow-lg rounded-3 border-0">
                  <div class="card-body p-4">
                     <h3 class="mb-3 text-primary fw-bold">Password Change <span class="text-secondary">( পাসওয়ার্ড পরিবর্তন )</span></h3>
                     <hr class="mb-4" />
                     <form id="resetForm" action="../process/password_reset.php" method="post" autocomplete="off">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                        <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($_SESSION['member_id']); ?>">
                        <input type="hidden" name="member_code" value="<?php echo htmlspecialchars($_SESSION['member_code']); ?>">
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <label class="form-label">User Name <span class="text-secondary small">(ব্যবহারকারীর নাম)</span>
                              </label>
                               <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                              <label class="form-label">Previous Password <span class="text-secondary small">(আগের পাসওয়ার্ড)</span>
                              </label>
                               <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['re_password'] ?? ''); ?>" readonly>
                            </div>
                           <div class="col-md-6 mb-3">
                              <label for="password" class="form-label">New Password <span class="text-secondary small">(নতুন পাসওয়ার্ড)</span>
                              </label>
                              <!-- Password Field (with eye icon) -->
                              <div class="input-group">
                                 <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                 <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)" tabindex="-1">
                                 <i class="fa fa-eye"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <label for="retype_password" class="form-label">Retype Password <span class="text-secondary small">(পুনরায় পাসওয়ার্ড)</span>
                              </label>
                              <!-- Retype Password Field (rounded, with checkmark only) -->
                              <div class="position-relative">
                                 <input type="password" class="form-control" id="retype_password" name="retype_password" required minlength="6" oninput="checkPasswordMatch()" onfocus="clearPasswordMatchError()">
                                 <span id="retypePasswordSuccess" style="display:none; color:green; position:absolute; right:15px; top:50%; transform:translateY(-50%); font-size:1.3em;">
                                 &#10004;
                                 </span>
                              </div>
                              <span id="retypePasswordError" class="text-danger small"></span>
                           </div>
                           <div class="mt-4 text-end">
                              <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                              Update Password (পাসওয়ার্ড হালনাগাদ করুন)
                              </button>
                           </div>
                     </form>
                     </div>
                  </div>
               </div>
         </main>
  </div>
</div>
<!-- Hero End -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../includes/toast.php'; ?>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
