<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../includes/head.php';

//Fetch Data from utils table for GL Mapping

$query = "SELECT * FROM utils WHERE status = 'A'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$utils_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Data From glac_mst table parent_child = 'C'
$query_glac = "SELECT * FROM glac_mst WHERE parent_child = 'C'";
$stmt_glac = $pdo->prepare($query_glac);
$stmt_glac->execute();
$glac_data = $stmt_glac->fetchAll(PDO::FETCH_ASSOC);

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
                <form method="post" action="../process/gl_mapping_process.php">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ক্রম</th>
                                    <th>লেনদেনের ধরন</th>
                                    <th>জি.এল নির্ধারণ করুন</th>
                                    <th>জি.এল নির্ধারণ করুন</th>
                                    <th>অবস্থা</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($utils_data as $row): ?>
                                <tr>
                                    <td width="5%"><?= $row['id'] ?></td>
                                    <td width="20%">
                                        <select class="form-select" name="fee_type[]">
                                            <?php 
                                            // Fetch all unique fee_types for dropdown
                                            $type_name_bn = array_unique(array_column($utils_data, 'type_name_bn'));
                                            foreach ($type_name_bn as $ftype): ?>
                                                <option <?= $row['type_name_bn'] == $ftype ? 'selected' : '' ?>><?= $ftype ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <select class="form-select" name="gl[]">
                                            <?php foreach ($glac_data as $glac): ?>
                                                <option value="<?= $glac['id'] ?>">
                                                    <?= $glac['glac_name'] . ' - ' . $glac['glac_code'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <select class="form-select" name="contra[]">
                                            <?php foreach ($glac_data as $glac): ?>
                                                <option value="<?= $glac['id'] ?>">
                                                    <?= $glac['glac_name'] . ' - ' . $glac['glac_code'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td width="15%">
                                        <select class="form-select" name="type[]">
                                            <option value = "সক্রিয়">সক্রিয়</option>
                                            <option value = "নিষ্ক্রিয়">নিষ্ক্রিয়</option>
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
