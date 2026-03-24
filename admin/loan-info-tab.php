<form class="mb-4">
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট টাইপ <span class="text-danger">*</span></label>
            <select class="form-select" name="product_type" required>
                <option value="">- নির্বাচন করুন -</option>
                <option value="Nano">Nano</option>
                <option value="Micro">Micro</option>
                <option value="SME">SME</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট কোড <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="product_code" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট নাম <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="product_name" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">শুরুর তারিখ <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="start_date" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সর্বনিম্ন ঋণের পরিমাণ <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="min_loan_amount" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সর্বোচ্চ ঋণের পরিমাণ <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="max_loan_amount" required>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">ঋণের মেয়াদ (মাস) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="loan_term" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">কিস্তি আদায়ের ফ্রিকোয়েন্সি <span class="text-danger">*</span></label>
            <select class="form-select" name="installment_frequency">
                <option value="">- নির্বাচন করুন -</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Yearly">Yearly</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">কিস্তি আদায়ের সংখ্যা</label>
            <input type="number" class="form-control" name="installment_count">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সার্ভিস চার্জ ক্যালকুলেশন পদ্ধতি <span class="text-danger">*</span></label>
            <select class="form-select" name="service_charge_calculation_method">
                <option value="">- নির্বাচন করুন -</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Yearly">Yearly</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">প্রোডাক্ট জি.এল <span class="text-danger">*</span></label>
            <select class="form-select" name="product_gl">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1">GL1</option>
                <option value="GL2">GL2</option>
                <option value="GL3">GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">মূলধন/আসল জি.এল <span class="text-danger">*</span></label>
            <select class="form-select" name="capital_gl">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1">GL1</option>
                <option value="GL2">GL2</option>
                <option value="GL3">GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">সার্ভিস চার্জ জি.এল <span class="text-danger">*</span></label>
            <select class="form-select" name="service_charge_gl">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1">GL1</option>
                <option value="GL2">GL2</option>
                <option value="GL3">GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">আদায়ের ক্রমানুসারে মূলধন/আসল<span class="text-danger">*</span></label>
            <select class="form-select" name="capital_recovery_order">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1">GL1</option>
                <option value="GL2">GL2</option>
                <option value="GL3">GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">আদায়ের ক্রমানুসারে সার্ভিস চার্জ<span class="text-danger">*</span></label>
            <select class="form-select" name="service_charge_recovery_order">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1">GL1</option>
                <option value="GL2">GL2</option>
                <option value="GL3">GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">আদায়ের ক্রমানুসারে বিলম্বিত চার্জ <span class="text-danger">*</span></label>
            <select class="form-select" name="late_charge_recovery_order">
                <option value="">- নির্বাচন করুন -</option>
                <option value="GL1">GL1</option>
                <option value="GL2">GL2</option>
                <option value="GL3">GL3</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">চেক বিতরণ আছে?</label>
            <select class="form-select" name="has_check_disbursement">
                <option value="">- নির্বাচন করুন -</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">একাধিক ঋণ সদস্য?</label>
            <select class="form-select" name="multiple_loan_member">
                <option value="">- নির্বাচন করুন -</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">কিস্তির পরিমানের পদ্ধতি</label>
            <select class="form-select" name="installment_measurement_method">
                <option value="">- নির্বাচন করুন -</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </div>
        <div class="col-md-4 mb-3 d-grid">
            <button type="submit" class="btn btn-success mt-2">সাবমিট</button>
        </div>
    </div>
</form>