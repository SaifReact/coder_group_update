<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';
?>

<?php 
// Assuming these includes handle your HTML <head> and sidebar structure
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

   <main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
        <div class="row px-2">
                <div class="card shadow-lg rounded-3 border-0">
                    <div class="card-body p-4">
                      <h3 class="mb-3 text-primary fw-bold">Meeting <span class="text-secondary">( মিটিং )</span></h3> 
                      <hr class="mb-4" />

                        <form action="../process/meeting_process.php" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="meeting_date">Meeting Date</label>
                                    <input type="date" class="form-control" id="meeting_date" name="meeting_date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="meeting_place">Meeting Place</label>
                                    <input type="text" class="form-control" id="meeting_place" name="meeting_place" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="meeting_agenda">Meeting Agenda</label>
                                    <textarea class="form-control" id="meeting_agenda" name="meeting_agenda" rows="2" required></textarea>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="meeting_decision">Meeting Decision</label>
                                    <textarea class="form-control" id="meeting_decision" name="meeting_decision" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                     <label for="presided_by">Presided By</label>
                                     <input type="text" class="form-control" id="presided_by" name="presided_by" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Members List</label>
                                    <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto; background: #f9f9f9;">
                                    <div class="row">
                                    <?php
                                        // Fetch members from members_info table
                                        $stmt = $pdo->query("SELECT a.id, a.name_bn FROM members_info a, user_login b Where a.id = b.member_id AND b.status in ('A', 'P') AND b.member_id != 0 ORDER BY id ASC");
                                        $col = 0;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            // Layout the checkboxes in 3 columns (col-md-4) on medium screens and 2 columns (col-6) on small screens
                                            if ($col % 3 === 0 && $col !== 0) echo '</div><div class="row">';
                                            echo '<div class="col-md-4 col-6">';
                                            echo '<div class="form-check">';
                                            echo '<input class="form-check-input" type="checkbox" name="meeting_members[]" value="' . $row['id'] . ') ' . $row['name_bn'] . '" id="member_' . $row['name_bn'] . '">';
                                            echo '<label class="form-check-label ms-1" for="member_' . $row['name_bn'] . '">' . htmlspecialchars($row['name_bn']) . '</label>';
                                            echo '</div>';
                                            echo '</div>';
                                            $col++;
                                        }
                                    ?>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" name="action" value="insert" class="btn btn-primary btn-lg px-4 shadow-sm">
                                        Save Meeting (মিটিং সংরক্ষণ করুন)
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
  </div>
</div>

<?php include_once __DIR__ . '/../includes/toast.php'; ?>
<?php include_once __DIR__ . '/../includes/end.php'; ?>