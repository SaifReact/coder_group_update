<?php
// Modal content for member payments
include_once __DIR__ . '/../config/config.php';
if (!isset($_SESSION)) session_start();
$member_id = $_SESSION['member_id'] ?? 0;

// Fetch payments
$stmt = $pdo->prepare("SELECT id, payment_method, for_fees, amount, DATE_FORMAT(created_at, '%d-%m-%Y') as pay_date, status FROM member_payments WHERE member_id = ? ORDER BY id DESC");
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
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2 edit-payment-btn" 
                      data-id="<?= $row['id'] ?>" 
                      data-for_fees="<?= strtolower(htmlspecialchars($row['for_fees'])) ?>" 
                      data-amount="<?= htmlspecialchars($row['amount']) ?>"
                      title="Edit Payment">
                      <i class="bi bi-pencil"></i>
                    </button>
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

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPaymentModalLabel">Edit Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editPaymentForm">
          <input type="hidden" name="id" id="editPaymentId">
          <div class="mb-3">
            <label for="editForFees" class="form-label">For Fees (Month)</label>
            <select class="form-select" id="editForFees" name="for_fees" required onchange="handlePaymentTypeChange()">
            <option value="">ফি বাছাই করুন (Select Fee)</option>
            <?php
              $months = [
                'january' => 'January (জানুয়ারি)',
                'february' => 'February (ফেব্রুয়ারি)',
                'march' => 'March (মার্চ)',
                'april' => 'April (এপ্রিল)',
                'may' => 'May (মে)',
                'june' => 'June (জুন)',
                'july' => 'July (জুলাই)',
                'august' => 'August (আগস্ট)',
                'september' => 'September (সেপ্টেম্বর)',
                'october' => 'October (অক্টোবর)',
                'november' => 'November (নভেম্বর)',
                'december' => 'December (ডিসেম্বর)'
              ];
              foreach ($months as $key => $val): ?>
                <option value="<?= $key ?>" <?= (!empty($payment_type) && $payment_type == $key) ? 'selected' : '' ?>>
                  <?= $val ?>
                </option>
              <?php endforeach; ?>
          </select>
          </div>
          <div class="mb-3">
            <label for="editAmount" class="form-label">Amount</label>
            <input type="number" class="form-control" id="editAmount" name="amount" required>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Show modal and fill form
  document.querySelectorAll('.edit-payment-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.getElementById('editPaymentId').value = btn.getAttribute('data-id');
      // Set the select option for month, default to first if not matched
      let forFeesValue = btn.getAttribute('data-for_fees');
      if (forFeesValue) forFeesValue = forFeesValue.toLowerCase();
      let forFeesSelect = document.getElementById('editForFees');
      let found = false;
      for (let i = 0; i < forFeesSelect.options.length; i++) {
        if (forFeesSelect.options[i].value === forFeesValue) {
          forFeesSelect.selectedIndex = i;
          found = true;
          break;
        }
      }
      if (!found) forFeesSelect.selectedIndex = 0;
      document.getElementById('editAmount').value = btn.getAttribute('data-amount');
      var modal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
      modal.show();
    });
  });

  // Handle form submit
  document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    fetch('../process/update_member_payment.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message || 'Payment updated successfully.');
        location.reload();
      } else {
        alert('Update failed: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(() => {
      alert('Update failed.');
      // Optionally reload if you want to force reload on any error
      // location.reload();
    });
  });
});
</script>
