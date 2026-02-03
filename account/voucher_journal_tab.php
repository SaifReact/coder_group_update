<form id="journalVoucherForm">
    <div class="border rounded p-3 mb-3 bg-light">
        <div class="fw-bold mb-2">ডেবিট অংশ</div>
        <div id="journalDebitRows">
            <div class="row g-2 mb-3 journal-debit-row">
                <div class="col-md-4">
                    <select class="form-select" name="debit_gl[]">
                        <option>ডেবিট জি.এল</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="debit_amount[]" placeholder="টাকার পরিমাণ (ডেবিট)">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="debit_narration[]" placeholder="হিসাবের বিবরণ (ডেবিট)">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-primary add-debit-row" onclick="addJournalDebitRow(this)">+</button>
                    <button type="button" class="btn btn-outline-danger ms-1 remove-debit-row" style="display:none;" onclick="removeJournalDebitRow(this)">×</button>
                </div>
            </div>
        </div>
    </div>
    <div class="border rounded p-3 mb-3 bg-light">
        <div class="fw-bold mb-2">ক্রেডিট অংশ</div>
        <div id="journalCreditRows">
            <div class="row g-2 mb-3 journal-credit-row">
                <div class="col-md-4">
                    <select class="form-select" name="credit_gl[]">
                        <option>ক্রেডিট জি.এল</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="credit_amount[]" placeholder="টাকার পরিমাণ (ক্রেডিট)">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="credit_narration[]" placeholder="হিসাবের বিবরণ (ক্রেডিট)">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-primary add-credit-row" onclick="addJournalCreditRow(this)">+</button>
                    <button type="button" class="btn btn-outline-danger ms-1 remove-credit-row" style="display:none;" onclick="removeJournalCreditRow(this)">×</button>
                </div>
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
function addJournalDebitRow(btn) {
    var row = btn.closest('.journal-debit-row');
    var newRow = row.cloneNode(true);
    newRow.querySelectorAll('input').forEach(function(input) { input.value = ''; });
    newRow.querySelectorAll('select').forEach(function(select) { select.selectedIndex = 0; });
    newRow.querySelector('.remove-debit-row').style.display = '';
    document.getElementById('journalDebitRows').appendChild(newRow);
    row.querySelector('.remove-debit-row').style.display = '';
}
function removeJournalDebitRow(btn) {
    var row = btn.closest('.journal-debit-row');
    var container = document.getElementById('journalDebitRows');
    if (container.children.length > 1) {
        row.remove();
    }
}
function addJournalCreditRow(btn) {
    var row = btn.closest('.journal-credit-row');
    var newRow = row.cloneNode(true);
    newRow.querySelectorAll('input').forEach(function(input) { input.value = ''; });
    newRow.querySelectorAll('select').forEach(function(select) { select.selectedIndex = 0; });
    newRow.querySelector('.remove-credit-row').style.display = '';
    document.getElementById('journalCreditRows').appendChild(newRow);
    row.querySelector('.remove-credit-row').style.display = '';
}
function removeJournalCreditRow(btn) {
    var row = btn.closest('.journal-credit-row');
    var container = document.getElementById('journalCreditRows');
    if (container.children.length > 1) {
        row.remove();
    }
}
</script>
