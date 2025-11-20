Coder Peshajibi Samabay Samity Ltd.

<script>
document.addEventListener('DOMContentLoaded', function() {
    var paymentType = document.getElementById('payment_type');
    var paymentYear = document.getElementById('payment_year');
    var amountInput = document.getElementById('amount');
    var paymentMode = document.getElementById('payment_date');
    var admissionInfo = document.getElementById('admissionInfo');
    var admissionPaidMsg = document.getElementById('admissionPaidMsg');
    var bankTrans = document.getElementById('bank_trans');
    var paymentDate = document.getElementById('payment_date');
    var paymentSlip = document.getElementById('payment_slip');
    var submitButton = document.getElementById('submit');
    var shareCount = document.getElementById('shareCount');
    var payModeAD = document.getElementById('AD');
    var payModeBP = document.getElementById('BP');
    var bankTransDiv = document.getElementById('bankTransDiv');
    var paymentDateDiv = document.getElementById('paymentDateDiv');
    var paymentSlipDiv = document.getElementById('paymentSlipDiv');

    var noShare = <?php echo json_encode($no_share); ?>;
    var samityShare = <?php echo json_encode($samity_share); ?>;
    var samityShareAmount = <?php echo json_encode($samity_share_amt); ?>;
    var extraShare = <?php echo json_encode($extra_share); ?>;
    var admissionPaid = <?php echo json_encode($admission_paid); ?>;
    var lateAssign = <?php echo json_encode($late_assign); ?>;
    var lateFee = <?php echo json_encode($late_fee); ?>;
    var sundrySamityShare = <?php echo json_encode($sundry_samity_share); ?>;
    var installAdvance = <?php echo json_encode($install_advance); ?>;
    var projectShare = <?php echo json_encode($project_share); ?>;
    var shareAmount = <?php echo json_encode($share_amount); ?>;
    var paidAmount = <?php echo json_encode($paid_amount); ?>;
    var sundryShare = <?php echo json_encode($sundry_share); ?>;
    var admissionPaid = <?php echo json_encode($admission_paid); ?>;
    var existingPayments = <?php echo json_encode($payments); ?>; // "month-year" format
    var perShareValue = <?php echo json_encode($per_share_value); ?>;
    var admissionfee = <?php echo json_encode($admissionfee); ?>;
    var monthly = <?php echo json_encode($monthly); ?>;
 
  
  // Get current month (0-11) and convert to month name
  var currentDate = new Date();
  var currentMonth = currentDate.getMonth(); // 0 = January, 11 = December
  var monthNames = ['january', 'february', 'march', 'april', 'may', 'june', 
                    'july', 'august', 'september', 'october', 'november', 'december'];
  var currentMonthName = monthNames[currentMonth];
  
  // Function to toggle bank fields based on payment mode
  function toggleBankFields() {
    if(payModeBP.checked) {
      bankTransDiv.style.display = 'block';
      paymentDateDiv.style.display = 'block';
      paymentSlipDiv.style.display = 'block';
      bankTrans.required = true;
      paymentDate.required = true;
      paymentSlip.required = true;
    } else if(payModeAD.checked) {
      bankTransDiv.style.display = 'none';
      paymentDateDiv.style.display = 'none';
      paymentSlipDiv.style.display = 'none';
      bankTrans.required = false;
      paymentDate.required = false;
      paymentSlip.required = false;
    }
  }
  
  // Add event listeners to payment mode radio buttons
  payModeAD.addEventListener('change', toggleBankFields);
  payModeBP.addEventListener('change', toggleBankFields);

  function updateForm() {
    var type = paymentType.value;
    var year = paymentYear.value;
    
    // Get share div
    var totalShareDiv = document.getElementById('totalShareDiv');

    // First check if payment exists
    var key = type + '-' + year;
    
    // For Samity Share payment, block if samity_share_amt > 0 and sundry_samity_share == 0
    if(type === 'Samity Share' && samityShareAmount > 0 && sundrySamityShare == 0) {
        amountInput.value = '';
        amountInput.disabled = true;
        bankTrans.disabled = true;
        paymentDate.disabled = true;
        paymentSlip.disabled = true;
        admissionInfo.style.display = 'none';
        totalShareDiv.style.display = 'none';
        admissionPaidMsg.textContent = "সমিতি শেয়ার ফি ইতিমধ্যেই প্রদান করা হয়েছে";
        admissionPaidMsg.style.display = 'block';
        submitButton.style.display = 'none';
        return;
    } else if(type === 'Project Share' && existingPayments.includes(key) && sundryProjectShare <= 0) {
      // Project Share payment already completed (no balance remaining)
      amountInput.value = '';
      amountInput.disabled = true;
      bankTrans.disabled = true;
      paymentDate.disabled = true;
      paymentSlip.disabled = true;
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
      admissionPaidMsg.textContent = "প্রকল্প শেয়ার ফি ইতিমধ্যেই প্রদান করা হয়েছে";
      admissionPaidMsg.style.display = 'block';
      submitButton.style.display = 'none';
      return;
    } else if(type !== 'Samity Share' && type !== 'Project Share' && (existingPayments.includes(key) || (type === 'admission' && admissionPaid))) {
      // For non-share payments, block if already paid
      amountInput.value = '';
      amountInput.disabled = true;
      bankTrans.disabled = true;
      paymentDate.disabled = true;
      paymentSlip.disabled = true;
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
      admissionPaidMsg.textContent = type === 'admission' ? 
        "ভর্তি ফি ইতিমধ্যেই প্রদান করা হয়েছে" : 
        "এই মাসের পেমেন্ট ইতিমধ্যেই প্রদান করা হয়েছে";
      admissionPaidMsg.style.display = 'block';
      submitButton.style.display = 'none';
      return;
    }

    // Enable form fields for new payment
    amountInput.disabled = false;
    bankTrans.disabled = false;
    paymentDate.disabled = false;
    paymentSlip.disabled = false;
    admissionPaidMsg.style.display = 'none';
    submitButton.style.display = 'inline-block';

    // Set amount based on payment type
    if(type === 'admission') {
      amountInput.value = (admissionfee).toFixed(2);
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
    } else if(type === 'Samity Share') {
      // Samity Share = (no_share - extra_share) * per_share_value
      var samityShareCount = no_share - extra_share;
      var totalShareValue = samityShareCount * perShareValue;
      console.log('Samity Share Count:', samityShareCount, totalShareValue);
      amountInput.value = ''; // User will enter amount manually
      shareCount.textContent = samityShareCount;
      admissionInfo.style.display = 'block';
      
      // Show total share value field
      totalShareDiv.style.display = 'block';
      
      // Set initial value - show sundry_share if exists, otherwise show calculated total
      if(sundry_samity_share > 0) {
        // Second or subsequent transaction - show sundry_share from database
        document.getElementById('total_share_value').value = sundry_samity_share.toFixed(2);
      } else {
        // First transaction - show (no_share - extra_share) * per_share_value
        document.getElementById('total_share_value').value = totalShareValue.toFixed(2);
      }
      
      // Calculate remaining on amount change
      updateTotalShareValue();
    } else if(type === 'Project Share') {
      // Project Share = extra_share * per_share_value
      var projectShareCount = projectShare;
      var totalProjectShareValue = projectShareCount * perShareValue;
      amountInput.value = ''; // User will enter amount manually
      shareCount.textContent = projectShareCount;
      admissionInfo.style.display = 'block';
      
      // Show total share value field
      totalShareDiv.style.display = 'block';
      
      // Set initial value - show sundry_project_share if exists, otherwise show calculated total
      if(sundryProjectShare > 0) {
        // Second or subsequent transaction - show sundry_project_share from database
        document.getElementById('total_share_value').value = sundryProjectShare.toFixed(2);
      } else {
        // First transaction - show projectShareCount * per_share_value
        document.getElementById('total_share_value').value = totalProjectShareValue.toFixed(2);
      }
      
      // Calculate remaining on amount change
      updateTotalShareValue();
    } else if(type !== '') { // Monthly payments (january-december)
      var paymentAmount = monthly;
      
      // Check if late fee should be applied based on late_assign value and current month
      var selectedMonthIndex = monthNames.indexOf(type);
      if(lateAssign === 'A' && selectedMonthIndex !== -1 && selectedMonthIndex < currentMonth) {
        // Add late fee if late_assign is 'A' AND selected month is before current month
        paymentAmount = monthly + late;
      } else {
        // No late fee
        paymentAmount = monthly;
      }
      
      amountInput.value = paymentAmount.toFixed(2);
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
    } else {
      amountInput.value = '';
      admissionInfo.style.display = 'none';
      totalShareDiv.style.display = 'none';
    }
  }
  
  function updateTotalShareValue() {
    var type = paymentType.value;
    if(type === 'Samity Share') {
      var amountPaid = parseFloat(amountInput.value) || 0;
      var initialTotal = 0;
      
      // Get initial total based on whether sundry_share exists
      if(sundryShare > 0) {
        // Second or subsequent transaction - use sundry_share
        initialTotal = sundryShare;
      } else {
        // First transaction - use (no_share - extra_share) * per_share_value
        var samityShareCount = noShare - extraShare;
        initialTotal = samityShareCount * perShareValue;
      }
      
      var remainingTotal = initialTotal - amountPaid;
      document.getElementById('total_share_value').value = remainingTotal.toFixed(2);
    } else if(type === 'Project Share') {
      var amountPaid = parseFloat(amountInput.value) || 0;
      var initialTotal = 0;
      
      // Get initial total based on whether sundry_project_share exists
      if(sundryProjectShare > 0) {
        // Second or subsequent transaction - use sundry_project_share
        initialTotal = sundryProjectShare;
      } else {
        // First transaction - use extra_share * per_share_value
        initialTotal = extraShare * perShareValue;
      }
      
      var remainingTotal = initialTotal - amountPaid;
      document.getElementById('total_share_value').value = remainingTotal.toFixed(2);
    }
  }

  paymentType.addEventListener('change', updateForm);
  paymentYear.addEventListener('change', updateForm);
  amountInput.addEventListener('input', updateTotalShareValue);

  // Initial load
  updateForm();
});

function previewPaymentSlip(event) {
  var img = document.getElementById('paymentSlipPreview');
  if(event.target.files && event.target.files[0]) {
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
}
</script>
