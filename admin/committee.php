<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

// Fetch members
$memberStmt = $pdo->query("SELECT id, name_bn, name_en, member_code FROM members_info ORDER BY id ASC");
$members = $memberStmt->fetchAll(PDO::FETCH_ASSOC);

$committeeStmt = $pdo->query("SELECT * FROM committee_role ORDER BY id ASC");
$designations = $committeeStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch committee members with members_info details
$stmt = $pdo->query("
    SELECT 
        cm.id, 
        cm.member_id, 
        cm.member_code, 
        cm.committee_role_id,
        cr.position_bn, 
        cm.fb, 
        cm.li, 
        cm.role, 
        mi.name_bn, 
        mi.name_en 
    FROM 
        committee_member cm
        JOIN committee_role cr ON cm.committee_role_id = cr.id
    JOIN 
        members_info mi 
    ON 
        cm.member_id = mi.id
    ORDER BY 
        cm.id ASC
");
$committees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h3 class="mb-3 text-primary fw-bold">Entrepreneur & Committee <span class="text-secondary">( উদ্যোক্তা ও কমিটি )</span></h3> 
            <hr class="mb-4" />

            <!-- Add Committee Form -->
            <form method="post" action="../process/committee_process.php">
              <input type="hidden" name="action" value="insert">
              <div class="row">
                <div class="col-12 col-md-6 mb-3">
                  <label for="member_id" class="form-label">Member Name</label>
                  <select class="form-select select2" id="member_id" name="member_id" required>
                    <option value="">Select Member ( সদস্য বাছাই করুন )</option>
                    <?php foreach($members as $member): ?>
                      <option value="<?= $member['id'] ?>">
                        <?= htmlspecialchars($member['name_bn']) ?> (<?= htmlspecialchars($member['name_en']) ?>) (<?= htmlspecialchars($member['member_code']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 col-md-6 mb-3">
                  <label for="designation" class="form-label">Designation</label>
                  <select class="form-select" id="designation" name="designation" required>
                    <option value="">Select Designation ( পদবি বাছাই করুন )</option>
                   <?php foreach($designations as $designation): ?>
                      <option value="<?= $designation['id'] ?>">
                        <?= htmlspecialchars($designation['position_bn']) ?> (<?= htmlspecialchars($designation['position_en']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Switch Input -->
                <div class="col-12 col-md-3 mb-3">
                  <label class="form-label">Role</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="role_switch" name="role" value="Entrepreneur">
                    <label class="form-check-label" for="role_switch">Entrepreneur</label>
                  </div>
                </div>

                <div class="col-12 col-md-5 mb-3">
                  <label for="facebook" class="form-label">Facebook</label>
                  <input type="text" class="form-control" id="facebook" name="facebook" required>
                </div>

                <div class="col-12 col-md-4 mb-3">
                  <label for="linkedin" class="form-label">LinkedIn</label>
                  <input type="text" class="form-control" id="linkedin" name="linkedin" required>
                </div>

                <div class="col-12 mt-4 text-end">
                  <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                    Save Committee ( কমিটি সংরক্ষণ করুন )
                  </button>
                </div>
              </div>
            </form>

            <hr class="my-4" />

            <!-- Committee Table -->
            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Designation</th>
                    <th>Member Info</th>
                    <th>Social</th>
                    <th>Role</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($committees as $committee): ?>
                  <tr>
                    <td><?= $committee['id']; ?></td>
                    <td><?= htmlspecialchars($committee['position_bn']); ?></td>
                    <td>
                      <?= htmlspecialchars($committee['member_code']); ?><br>
                      <?= htmlspecialchars($committee['name_en']); ?><br>
                      <?= htmlspecialchars($committee['name_bn']); ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($committee['fb']); ?><br>
                      <?= htmlspecialchars($committee['li']); ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($committee['role']); ?>
                    </td>
                    <td>
                      <!-- Delete Button -->
                      <form action="../process/committee_process.php" method="post" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?= $committee['id']; ?>">
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete This committee?');">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>

                      <!-- Edit Button -->
                      <button type="button" class="btn btn-info btn-sm"
                        onclick='editCommittee(
                          <?= (int)$committee["id"] ?>,
                          <?= json_encode($committee["member_id"]) ?>,
                          <?= json_encode($committee["committee_role_id"]) ?>,
                          <?= json_encode($committee["fb"]) ?>,
                          <?= json_encode($committee["li"]) ?>,
                          <?= json_encode($committee["role"]) ?>
                        )'>
                        <i class="fa fa-edit"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Edit Committee Modal -->
        <div class="modal fade" id="editCommitteeModal" tabindex="-1" aria-labelledby="editCommitteeModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="../process/committee_process.php" method="post">
                <div class="modal-header">
                  <h5 class="modal-title" id="editCommitteeModalLabel">Edit Committee Member</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" id="edit_id">

                  <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                      <label for="edit_member_id" class="form-label">Member Name</label>
                      <select class="form-select select2" id="edit_member_id" name="edit_member_id" required>
                        <option value="">Select Member ( সদস্য বাছাই করুন )</option>
                        <?php foreach($members as $member): ?>
                          <option value="<?= $member['id'] ?>">
                            <?= htmlspecialchars($member['name_bn']) ?> (<?= htmlspecialchars($member['name_en']) ?>) (<?= htmlspecialchars($member['member_code']) ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                      <label for="edit_designation" class="form-label">Designation</label>
                      <select class="form-select" id="edit_designation" name="edit_designation" required>
                        <option value="">Select Designation ( পদবি বাছাই করুন )</option>
                       <?php foreach($designations as $designation): ?>
                      <option value="<?= $designation['id'] ?>">
                        <?= htmlspecialchars($designation['position_bn']) ?> (<?= htmlspecialchars($designation['position_en']) ?>)
                      </option>
                    <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                      <label for="edit_facebook" class="form-label">Facebook</label>
                      <input type="text" class="form-control" id="edit_facebook" name="edit_facebook">
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                      <label for="edit_linkedin" class="form-label">LinkedIn</label>
                      <input type="text" class="form-control" id="edit_linkedin" name="edit_linkedin">
                    </div>

                    <!-- Switch Input -->
                    <div class="col-12 col-md-6 mb-3">
                      <label class="form-label">Role</label>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="edit_role_switch" name="edit_role" value="Entrepreneur">
                        <label class="form-check-label" for="edit_role_switch">Entrepreneur</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Update Committee ( কমিটি হালনাগাদ করুন )</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<!-- Edit JS -->
<script>
function editCommittee(id, member_id, committee_role_id, fb, li, role) {
    // Set the hidden input for the ID
    document.getElementById('edit_id').value = id;

    // Set the Member Name field
    document.getElementById('edit_member_id').value = member_id;

    // Set the Designation field
    document.getElementById('edit_designation').value = committee_role_id;

    // Set the Facebook and LinkedIn fields
    document.getElementById('edit_facebook').value = fb || '';
    document.getElementById('edit_linkedin').value = li || '';

    // Set the Role Switch
    const roleSwitch = document.getElementById('edit_role_switch');
    roleSwitch.checked = (role === 'Entrepreneur');

    // Show the Edit Modal
    const modal = new bootstrap.Modal(document.getElementById('editCommitteeModal'));
    modal.show();
}

// Initialize Select2 for all elements with the class "select2"
document.addEventListener('DOMContentLoaded', function () {
  $('.select2').select2({
    placeholder: "Select an option",
    allowClear: true
  });
});
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
