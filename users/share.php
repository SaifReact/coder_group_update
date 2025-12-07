<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$member_code = $_SESSION['member_code'];

$stmt = $pdo->prepare("SELECT * FROM member_share WHERE member_id = ?");
$stmt->execute([$member_id]);
$share = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_member_project = $pdo->prepare("SELECT * FROM member_project WHERE member_id = ? AND member_code = ?");
$stmt_member_project->execute([$member_id, $member_code]);
$member_projects = $stmt_member_project->fetchAll(PDO::FETCH_ASSOC);

// Fetch all projects
$stmt_projects = $pdo->query("SELECT id, project_name_bn, project_name_en FROM project ORDER BY id DESC");
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

$stmt_committee_role = $pdo->prepare("SELECT * FROM committee_member WHERE member_id = ? AND role = ? LIMIT 1");
$stmt_committee_role->execute([$member_id, 'Entrepreneur']);
$committee_role = $stmt_committee_role->fetchAll(PDO::FETCH_ASSOC);

$remainingShare = (isset($share[0]['extra_share']) ? $share[0]['extra_share'] : 0);
$samityShare = (isset($share[0]['samity_share']) ? $share[0]['samity_share'] : 0);
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
               <div class="card shadow-lg rounded-3 border-0">
                  <div class="card-body p-4">
                     <h3 class="mb-3 text-primary fw-bold">Add Share <span class="text-secondary">( শেয়ার যোগ করুন )</span></h3>
                     <hr class="mb-4" />
                     <form id="resetForm" action="../process/add_share.php" method="post" autocomplete="off">
                        <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($_SESSION['member_id']); ?>">
                        <input type="hidden" name="member_code" value="<?php echo htmlspecialchars($_SESSION['member_code']); ?>">
                        <input type="hidden" name="findProject" value="<?php echo isset($project_id) ? htmlspecialchars($project_id) : '0'; ?>">
                        <div class="row">
                             <div class="col-md-6 mb-3">
                              <label class="form-label">Select Project <span class="text-secondary small">(প্রকল্প নির্বাচন করুন)</span>
                              </label>
                               <select class="form-select" name="project_id" id="project_id" required>
                                 <option value="">প্রকল্প নির্বাচন করুন (Select Project)</option>
                                 <?php
                                 // If committee_role is 'Entrepreneur', add CPSSL option
                                 if($remainingShare == 0 ){
                                    echo '<option value="2">শেয়ার সংযুক্তকরণ (Share Attachment)</option>';
                                 }
                                 if (!empty($committee_role) && isset($committee_role[0]['role']) && $committee_role[0]['role'] === 'Entrepreneur') {
                                     echo '<option value="1">সমিতি শেয়ার (CPSSL)</option>';
                                 }
                                 foreach ($projects as $project): ?>
                                   <option value="<?php echo htmlspecialchars($project['id']); ?>">
                                     <?php echo htmlspecialchars($project['project_name_bn']) . ' - ' . htmlspecialchars($project['project_name_en']); ?>
                                   </option>
                                 <?php endforeach; ?>
                               </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                              <label class="form-label"> অবশিষ্ট শেয়ার <span class="text-secondary small">(Remaining Share)</span>
                              </label>
                               <input type="text" class="form-control" name="previousShare" id="previousShare" value="<?php echo isset($remainingShare) ? htmlspecialchars($remainingShare) : 0; ?>" readOnly>
                            </div>

                            <div class="col-md-6 mb-3">
                              <label class="form-label"> সমিতির ক্রয়কৃত শেয়ার <span class="text-secondary small">(Shares purchased by the Samity)</span>
                              </label>
                               <input type="text" class="form-control" name="samityShare" id="samityShare" value="<?php echo isset($samityShare) ? htmlspecialchars($samityShare) : 0; ?>" readOnly>
                            </div>

                            <div class="col-md-6 mb-3">
                              <label class="form-label"> প্রকল্পের ক্রয়কৃত শেয়ার <span class="text-secondary small">(Purchased shares of the project)</span>
                              </label>
                               <input type="text" class="form-control" name="sellingShare" id="sellingShare" value="<?php echo isset($member_projects[0]['project_share']) ? htmlspecialchars($member_projects[0]['project_share']) : 0; ?>" readOnly>
                            </div>

                            <div class="col-md-6 mb-3">
                              <label class="form-label"> শেয়ার যোগ করুন <span class="text-secondary small">(Add Share)</span></label>
                               <input type="text" class="form-control" name="buyingShare" id="buyingShare" value="0" required>
                            </div> 

                            <div class="col-12 col-md-6 mb-3" id="projectInfoBox" style="display:none;"></div>
                            
                            <div class="col-md-6 mb-3">
                              <input type="hidden" class="form-control" name="addShare" id="addShare" value="0" >
                            </div>
                            
                           <div class="mt-4 text-end">
                            <?php if (empty($member_projects)) : ?>
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                Add Share (শেয়ার যোগ করুন)
                              </button>
                            <?php else : ?>
                              <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                Update Share (শেয়ার হালনাগাদ করুন)
                              </button>
                            <?php endif; ?>
                           </div>
                     </form>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var projectSelect = document.getElementById('project_id');
  var infoBox = document.getElementById('projectInfoBox');
  var findProjectInput = document.querySelector('input[name="findProject"]');
  var addShareDiv = document.getElementById('addShare').closest('.col-md-6.mb-3');
  // Initial hide if value is not > 0
  if (!(parseInt(findProjectInput.value) > 0)) {
    addShareDiv.style.display = 'none';
  }
  projectSelect.addEventListener('change', function() {
    var pid = this.value;
    var sellingShareInput = document.getElementById('sellingShare');
    
    if (!pid) {
      infoBox.style.display = 'none';
      infoBox.innerHTML = '';
      if (findProjectInput) findProjectInput.value = '0';
      addShareDiv.style.display = 'none';
      if (sellingShareInput) sellingShareInput.value = '0';
      return;
    }
    
    // Check if project exists in member_project for this member
    var exists = 0;
    <?php
    // Prepare a JS array of project_ids from member_project for this member
    $mp_ids = array_map(function($mp) { return (int)$mp['project_id']; }, $member_projects);
    ?>
    var memberProjectIds = <?php echo json_encode($mp_ids); ?>;
    if (memberProjectIds.includes(parseInt(pid))) {
      exists = pid;
    }
    if (findProjectInput) findProjectInput.value = exists ? exists : '0';
    
    // Show/hide addShare input based on findProject value
    if (parseInt(findProjectInput.value)) {
      addShareDiv.style.display = '';
    }
    
    // Update sellingShare value based on selected project
    if (parseInt(pid) > 1) {
      // Fetch member's share for this specific project
      fetch('get_member_project_share.php?project_id=' + encodeURIComponent(pid))
        .then(r => r.json())
        .then(function(data) {
          if (data.project_share) {
            if (sellingShareInput) sellingShareInput.value = data.project_share;
          } else {
            if (sellingShareInput) sellingShareInput.value = '0';
          }
        })
        .catch(function() {
          if (sellingShareInput) sellingShareInput.value = '0';
        });
    } else {
      // For project_id = 1 (CPSSL), keep the original samity share value
      if (sellingShareInput) sellingShareInput.value = '0';
    } 
    fetch('project_info.php?project_id=' + encodeURIComponent(pid))
      .then(r => r.json())
      .then(function(data) {
        if (data.error) {
          infoBox.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
          infoBox.style.display = '';
          return;
        }
        var html = '<table class="table table-bordered table-sm">';
        html += '<tr><th>Project Name (BN)</th><td>' + (data.project_name_bn || '') + '</td></tr>';
        html += '<tr><th>Project Name (EN)</th><td>' + (data.project_name_en || '') + '</td></tr>';
        html += '<tr><th>About Project</th><td>' + (data.about_project || '') + '</td></tr>';
        html += '<tr><th>Project Value (TK)</th><td>' + (data.project_value || '') + '</td></tr>';
        html += '<tr><th>No of Project Share</th><td>' + (data.project_share || '') + '</td></tr>';
        html += '<tr><th>Per Share Value (TK)</th><td>' + (data.per_share_value || '') + '</td></tr>';
        html += '</table>';
        infoBox.innerHTML = html;
        infoBox.style.display = '';
      })
      .catch(function() {
        infoBox.innerHTML = '<div class="alert alert-danger">Could not load project info.</div>';
        infoBox.style.display = '';
      });
  });
});
</script>
                     </div>
                  </div>
               </div>
         </main>
  </div>
</div>
<!-- Hero End -->

<?php include_once __DIR__ . '/../includes/end.php'; ?>
