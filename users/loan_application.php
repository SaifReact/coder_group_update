<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'];
$member_code = $_SESSION['member_code'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $product_id = $_POST['product_id'];
    $loan_amount = $_POST['loan_amount'];
    $loan_purpose = $_POST['loan_purpose'];
    $loan_term = $_POST['loan_term'];
    $grantor_id = $_POST['grantor_id'];

    $sql = "INSERT INTO loan_applications (member_id, product_id, loan_amount, loan_purpose, loan_term, grantor_id, application_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siissi', $member_id, $product_id, $loan_amount, $loan_purpose, $loan_term, $grantor_id);
    if ($stmt->execute()) {
        $success = 'Loan application submitted successfully!';
    } else {
        $error = 'Failed to submit loan application.';
    }
}

// Fetch member info by Member ID (AJAX)
if (isset($_GET['fetch_member']) && isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];
    $sql = "SELECT name, nid, phone FROM members_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    echo json_encode($member);
    exit;
}

// Fetch product info (AJAX)
if (isset($_GET['fetch_product']) && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $sql = "SELECT * FROM loan_products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    echo json_encode($product);
    exit;
}

// Fetch grantor list
// $grantors = [];
// $sql = "SELECT member_id, name FROM members_info";
// $result = $conn->query($sql);
// while ($row = $result->fetch_assoc()) {
//     $grantors[] = $row;
// }

// Fetch product list
// $products = [];
// $sql = "SELECT id, product_name FROM loan_products";
// $result = $conn->query($sql);
// while ($row = $result->fetch_assoc()) {
//     $products[] = $row;
// }
?>
<?php include_once __DIR__ . '/../includes/open.php'; ?>
<?php include_once __DIR__ . '/../includes/side_bar.php'; ?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-12 col-md-12">
                        <h3 class="mb-3 text-primary fw-bold">Product-wise Loan Application</h3>
                    </div>
                </div>
                <hr class="mb-4" />
                <form id="loanForm" method="POST">
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Member ID</label>
                            <input type="text" class="form-control" id="member_id" name="member_id" value="<?= htmlspecialchars($member_id) ?>" readonly required>
                        </div>
                        <div class="col-12 col-md-8" id="memberInfo"></div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Select Product</label>
                            <select class="form-control" id="product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                <?php foreach($products as $product): ?>
                                    <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-8" id="productInfo"></div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Loan Amount</label>
                            <input type="number" class="form-control" id="loan_amount" name="loan_amount" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Loan Purpose</label>
                            <input type="text" class="form-control" id="loan_purpose" name="loan_purpose" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Loan Term (months)</label>
                            <input type="number" class="form-control" id="loan_term" name="loan_term" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Grantor</label>
                            <select class="form-control" id="grantor_id" name="grantor_id" required>
                                <option value="">Select Grantor</option>
                                <?php foreach($grantors as $grantor): ?>
                                    <option value="<?= $grantor['member_id'] ?>"><?= $grantor['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">Apply for Loan</button>
                    </div>
                </form>
                <?php if(isset($success)) echo '<div class="alert alert-success mt-3">'.$success.'</div>'; ?>
                <?php if(isset($error)) echo '<div class="alert alert-danger mt-3">'.$error.'</div>'; ?>
            </div>
        </div>
    </div>
</main>
                                </div>

<?php include_once __DIR__ . '/../includes/end.php'; ?>


<script>

document.addEventListener('DOMContentLoaded', function() {
    var memberInput = document.getElementById('member_id');
    var productInput = document.getElementById('product_id');
    var sessionMemberId = memberInput ? memberInput.value : '';
    // Show member details if session member_id is set
    if (sessionMemberId) {
        fetch('loan_application.php?fetch_member=1&member_id=' + sessionMemberId)
        .then(response => response.json())
        .then(data => {
            if(data && data.name) {
                document.getElementById('memberInfo').innerHTML = `<div class="alert alert-info"><b>Name:</b> ${data.name}<br><b>NID:</b> ${data.nid}<br><b>Phone:</b> ${data.phone}</div>`;
            } else {
                document.getElementById('memberInfo').innerHTML = '<span class="text-danger">Member not found.</span>';
            }
        });
    }
    // Product selection interactive
    if (productInput) {
        productInput.addEventListener('change', function() {
            var productId = this.value;
            if(productId) {
                fetch('loan_application.php?fetch_product=1&product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if(data && data.product_name) {
                        document.getElementById('productInfo').innerHTML = `<div class="alert alert-info"><b>Product Name:</b> ${data.product_name}<br><b>Details:</b> ${data.details || ''}</div>`;
                    } else {
                        document.getElementById('productInfo').innerHTML = '<span class="text-danger">Product not found.</span>';
                    }
                });
            } else {
                document.getElementById('productInfo').innerHTML = '';
            }
        });
    }
});
</script>
