<form id="receiptVoucherForm">
    <div id="receiptVoucherRows">
        <div class="row g-2 mb-3 receipt-row">
            <div class="col-md-3">
                <select class="form-select" name="debit_gl[]">
                    <option>ডেবিট জি.এল</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="credit_gl[]">
                    <option>ক্রেডিট জি.এল</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="amount[]" placeholder="টাকার পরিমাণ">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="narration[]" placeholder="হিসাবের বিবরণ">
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-outline-primary add-row" onclick="addReceiptRow(this)">+</button>
                <button type="button" class="btn btn-outline-danger ms-1 remove-row" style="display:none;" onclick="removeReceiptRow(this)">×</button>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <span>মোট ডেবিট টাকা : <span id="total_debit">0.00</span></span>
        </div>
        <div class="col-md-6">
            <span>মোট ক্রেডিট টাকা : <span id="total_credit">0.00</span></span>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">ব্যালেন্স</label>
        <div id="balance"></div>
    </div>
    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm" disabled>ভাউচার জমা দিন</button>
    </div>
</form>
<script>
function addReceiptRow(btn) {
    var row = btn.closest('.receipt-row');
    var newRow = row.cloneNode(true);
    // Clear input values
    newRow.querySelectorAll('input').forEach(function(input) { input.value = ''; });
    newRow.querySelectorAll('select').forEach(function(select) { select.selectedIndex = 0; });
    // Show remove button for all but first row
    newRow.querySelector('.remove-row').style.display = '';
    document.getElementById('receiptVoucherRows').appendChild(newRow);
    // Show remove button for this row too (if not first)
    row.querySelector('.remove-row').style.display = '';
}
function removeReceiptRow(btn) {
    var row = btn.closest('.receipt-row');
    var container = document.getElementById('receiptVoucherRows');
    if (container.children.length > 1) {
        row.remove();
    }
}
</script>
