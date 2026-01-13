<?php
// Modal content for member payments
include_once __DIR__ . '/../config/config.php';
if (!isset($_SESSION)) session_start();
$member_id = $_SESSION['member_id'] ?? 0;

// Fetch payments
$stmt = $pdo->prepare("SELECT payment_method, for_fees, amount, DATE_FORMAT(created_at, '%d-%m-%Y') as pay_date, status FROM member_payments WHERE member_id = ? ORDER BY id DESC");
$stmt->execute([$member_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="modal fade" id="memberPaymentModal" tabindex="-1" aria-labelledby="memberPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="memberPaymentModalLabel">Member Payments</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Status</th>
              <th>Payment Method</th>
              <th>For Fees</th>
              <th>Amount</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $row): ?>
              <tr>
                <td>
                  <?php if ($row['status'] === 'A'): ?>
                    <span class="badge bg-success">অনুমোদিত</span>
                  <?php elseif($row['status'] === 'I'): ?>
                    <span class="badge bg-primary">অনুমোদনে অপেক্ষমান</span>
                  <?php else: ?>  
                    <span class="badge bg-danger">বাতিল</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td><?= htmlspecialchars($row['for_fees']) ?></td>
                <td><?= htmlspecialchars($row['amount']) ?></td>
                <td><?= htmlspecialchars($row['pay_date']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
