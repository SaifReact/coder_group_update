<form class="mb-4">
  <div class="row align-items-end">
    <div class="col-md-4 mb-3">
      <label class="form-label">প্রোডাক্টের নাম <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="product_name" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">সার্ভিস চার্জের হার(%) <span class="text-danger">*</span></label>
      <input type="number" step="0.01" class="form-control" name="service_charge_rate" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">বিলম্বিত সার্ভিস চার্জের হার(%)</label>
      <input type="number" step="0.01" class="form-control" name="detailed_service_charge_rate">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">মেয়াদোত্তির্ণ সার্ভিস চার্জের হার(%)</label>
      <input type="number" step="0.01" class="form-control" name="max_total_service_charge_rate">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">চার্জের নাম</label>
      <select class="form-select" name="charge_name">
        <option value="">- নির্বাচন করুন -</option>
        <option value="Processing Fee">Processing Fee</option>
        <option value="Service Fee">Service Fee</option>
        <option value="Other">Other</option>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">চার্জের পরিমাণ <span class="text-danger">*</span></label>
      <input type="number" step="0.01" class="form-control" name="charge_amount" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">চার্জ ক্রেডিট জি.এল</label>
      <select class="form-select" name="charge_credit_gl">
        <option value="">- নির্বাচন করুন -</option>
        <option value="GL1">GL1</option>
        <option value="GL2">GL2</option>
        <option value="GL3">GL3</option>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">কার্যকর তারিখ</label>
      <input type="date" class="form-control" name="effective_date" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="col-md-4 mb-3 d-grid">
      <button type="submit" class="btn btn-success mt-2">সাবমিট</button>
    </div>
  </div>
</form>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>সার্ভিস চার্জ (%)</th>
      <th>বিস্তারিত চার্জ (%)</th>
      <th>মোট সর্বোচ্চ চার্জ (%)</th>
      <th>কার্যকর তারিখ</th>
      <th>মন্তব্য</th>
      <th>কর্মধারা</th>
    </tr>
  </thead>
  <tbody>
    <!-- ডাটাবেস থেকে ডাটা লোড করুন -->
  </tbody>
</table>
