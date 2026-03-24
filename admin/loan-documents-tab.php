<form class="mb-4">
  <div class="row align-items-end">
    <div class="col-md-4 mb-3">
      <label class="form-label">প্রোডাক্টের নাম <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="product_name" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">ডকুমেন্টের ধরন</label>
      <select class="form-select" name="document_type">
        <option value="">- নির্বাচন করুন -</option>
        <option value="NID">NID</option>
        <option value="Photo">Photo</option>
        <option value="Signature">Signature</option>
        <option value="Other">Other</option>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">বাধ্যতামূলক?</label>
      <select class="form-select" name="is_required">
        <option value="">- নির্বাচন করুন -</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>
    </div>
    <div class="col-md-4 mb-3 d-grid">
      <button type="submit" class="btn btn-success mt-2">সাবমিট</button>
    </div>
  </div>
</form>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>ডকুমেন্টের নাম</th>
      <th>বাধ্যতামূলক</th>
      <th>কমেন্ট</th>
    </tr>
  </thead>
  <tbody>
    <!-- ডাটাবেস থেকে ডাটা লোড করুন -->
  </tbody>
</table>
