<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];

$stmt = $pdo->prepare("SELECT * FROM member_share WHERE member_id = ?");
$stmt->execute([$member_id]);
$share = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                     <h3 class="mb-3 text-primary fw-bold">Add Share <span class="text-secondary">( শেয়ার যোগ করুন )</span></h3>
                     <hr class="mb-4" />
                     <form id="resetForm" action="../process/add_share.php" method="post" autocomplete="off">
                        <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($_SESSION['member_id']); ?>">
                        <input type="hidden" name="member_code" value="<?php echo htmlspecialchars($_SESSION['member_code']); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                              <label class="form-label">Previous Share <span class="text-secondary small">(আগের শেয়ার)</span>
                              </label>
                               <input type="text" class="form-control" value="<?php echo isset($share[0]['no_share']) ? htmlspecialchars($share[0]['no_share']) : ''; ?>" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                              <label class="form-label">Share Add <span class="text-secondary small">(শেয়ার যোগ করুন)</span>
                              </label>
                               <input type="number" class="form-control" name="addShare" id="addShare" required>
                            </div>
                           
                           <div class="mt-4 text-end">
                              <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                              Update Share (শেয়ার হালনাগাদ করুন)
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

<?php include_once __DIR__ . '/../includes/end.php'; ?>
