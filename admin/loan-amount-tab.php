<form class="mb-4">
  <div class="row align-items-end">
    <div class="col-md-4 mb-3">
      <label class="form-label">প্রোডাক্টের নাম <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="product_name" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">সর্বাধিক টাকার পরিমাণ <span class="text-danger">*</span></label>
      <input type="number" class="form-control" name="max_amount" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">সর্বনিম্ন টাকার পরিমাণ <span class="text-danger">*</span></label>
      <input type="number" class="form-control" name="min_amount" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">সঞ্চয়ের শতকরা হার(%)</label>
      <input type="number" step="0.01" class="form-control" name="savings_percentage">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">শেয়ার এর শতকরা হার(%)</label>
      <input type="number" step="0.01" class="form-control" name="share_percentage">
    </div>
     <div class="col-md-4 mb-3 d-grid">
      <button type="submit" class="btn btn-success mt-2">সাবমিট</button>
    </div>
  </div>
</form>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>সর্বাধিক টাকা</th>
      <th>সর্বনিম্ন টাকা</th>
      <th>সঞ্চয়ের শতকরা হার</th>
      <th>শেয়ার এর শতকরা হার</th>
      <th>কমেন্ট</th>
    </tr>
  </thead>
  <tbody>
    <!-- ডাটাবেস থেকে ডাটা লোড করুন -->
  </tbody>
</table>
