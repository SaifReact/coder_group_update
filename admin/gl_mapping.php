<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../includes/head.php';

// Fetch mapping data (dummy for now, replace with DB fetch)
$mapping_rows = [
    ['id' => 1, 'desc' => 'মূলধন আদায়', 'gl' => '10202001 - Cash at Bank (Disbursable Loan Fund)', 'type' => 'সক্রিয়'],
    ['id' => 2, 'desc' => 'ঋণ বিতরণ', 'gl' => '10202001 - Cash at Bank (Disbursable Loan Fund)', 'type' => 'সক্রিয়'],
    ['id' => 3, 'desc' => 'সঞ্চয় উত্তোলন', 'gl' => '10202002 - Cash at Bank (Savings Fund)', 'type' => 'সক্রিয়'],
    ['id' => 4, 'desc' => 'সঞ্চয় আদায়', 'gl' => '10202002 - Cash at Bank (Savings Fund)', 'type' => 'সক্রিয়'],
    ['id' => 5, 'desc' => 'সার্ভিস চার্জ বিতরণ', 'gl' => '10202004 - Cash at Bank (Salary Fund)', 'type' => 'সক্রিয়'],
    ['id' => 6, 'desc' => 'মূলধন বিতরণ', 'gl' => '10202015 - Cash at Bank (Net Income)', 'type' => 'সক্রিয়'],
];
?>

<?php 
include_once __DIR__ . '/../includes/open.php';
include_once __DIR__ . '/../includes/side_bar.php'; 
?>

<main class="col-12 col-md-10 col-lg-10 col-xl-10 px-md-3">
    <div class="row px-2">
        <div class="card shadow-lg rounded-3 border-0">
            <div class="card-body p-4">
                <h3 class="mb-3 text-primary fw-bold">GL Mapping</h3>
                <hr class="mb-4" />
                <form method="post" action="gl_mapping_process.php">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ক্রম</th>
                                    <th>লেনদেনের ধরন</th>
                                    <th>জি.এল নির্ধারণ করুন</th>
                                    <th>অবস্থা</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mapping_rows as $row): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td>
                                        <select class="form-select" name="desc[]">
                                            <option <?= $row['desc'] == 'মূলধন আদায়' ? 'selected' : '' ?>>মূলধন আদায়</option>
                                            <option <?= $row['desc'] == 'ঋণ বিতরণ' ? 'selected' : '' ?>>ঋণ বিতরণ</option>
                                            <option <?= $row['desc'] == 'সঞ্চয় উত্তোলন' ? 'selected' : '' ?>>সঞ্চয় উত্তোলন</option>
                                            <option <?= $row['desc'] == 'সঞ্চয় আদায়' ? 'selected' : '' ?>>সঞ্চয় আদায়</option>
                                            <option <?= $row['desc'] == 'সার্ভিস চার্জ বিতরণ' ? 'selected' : '' ?>>সার্ভিস চার্জ বিতরণ</option>
                                            <option <?= $row['desc'] == 'মূলধন বিতরণ' ? 'selected' : '' ?>>মূলধন বিতরণ</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select" name="gl[]">
                                            <option <?= $row['gl'] == '10202001 - Cash at Bank (Disbursable Loan Fund)' ? 'selected' : '' ?>>10202001 - Cash at Bank (Disbursable Loan Fund)</option>
                                            <option <?= $row['gl'] == '10202002 - Cash at Bank (Savings Fund)' ? 'selected' : '' ?>>10202002 - Cash at Bank (Savings Fund)</option>
                                            <option <?= $row['gl'] == '10202004 - Cash at Bank (Salary Fund)' ? 'selected' : '' ?>>10202004 - Cash at Bank (Salary Fund)</option>
                                            <option <?= $row['gl'] == '10202015 - Cash at Bank (Net Income)' ? 'selected' : '' ?>>10202015 - Cash at Bank (Net Income)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select" name="type[]">
                                            <option <?= $row['type'] == 'সক্রিয়' ? 'selected' : '' ?>>সক্রিয়</option>
                                            <option <?= $row['type'] == 'নিষ্ক্রিয়' ? 'selected' : '' ?>>নিষ্ক্রিয়</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">Save Mapping</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
                                </div>
                                </div>
<?php include_once __DIR__ . '/../includes/end.php'; ?>
