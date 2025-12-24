<?php
// users/meeting.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$stmt = $pdo->query("SELECT id, mdate, place, agenda, decision, presided_by, members FROM meeting ORDER BY mdate DESC, id DESC");
$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 
<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?> 
<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
  <div class="row px-2">
    <div class="card shadow-lg rounded-3 border-0">
      <div class="card-body p-4">
        <h3 class="mb-3 text-primary fw-bold">Meeting Minutes <span class="text-secondary">( সভার কার্যবিবরণী )</span>
        </h3>
        <hr class="mb-4" />
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Meeting Date</th>
                <th>Place</th>
                <th>Agenda</th>
                <th>Decision</th>
                <th>Presided By</th>
                <th>Present Members</th>
              </tr>
            </thead>
            <tbody> <?php $i = 1; foreach ($meetings as $meeting): ?> <tr>
                <td> <?= $i++; ?> </td>
                <td> <?php
                  $date = $meeting['mdate'];
                  if ($date && $date !== '0000-00-00') {
                    $d = DateTime::createFromFormat('Y-m-d', $date);
                    echo $d ? $d->format('d/m/Y') : htmlspecialchars($date);
                  } else {
                    echo '';
                  }
                ?> </td>
                <td> <?= htmlspecialchars($meeting['place']); ?> </td>
                <td> <?= nl2br(htmlspecialchars($meeting['agenda'])); ?> </td>
                <td> <?= nl2br(htmlspecialchars($meeting['decision'])); ?> </td>
                <td> <?= htmlspecialchars($meeting['presided_by']); ?> </td>
                <td> <?php
                        $members = json_decode($meeting['members'], true);
                        if (is_array($members) && count($members)) {
                            // Fetch member names
                            $in = str_repeat('?,', count($members) - 1) . '?';
                            $stmt2 = $pdo->prepare("SELECT name_bn FROM members_info WHERE id IN ($in)");
                            $stmt2->execute($members);
                            $names = $stmt2->fetchAll(PDO::FETCH_COLUMN);
                            echo implode(', ', array_map('htmlspecialchars', $names));
                        } else {
                            echo '<span class="text-muted">None</span>';
                        }
                        ?> </td>
              </tr> <?php endforeach; ?> </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
</div>
</div>
<?php include_once __DIR__ . '/../includes/end.php'; ?>