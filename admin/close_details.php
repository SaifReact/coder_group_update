<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    http_response_code(403);
    echo '<div class="alert alert-danger">Access denied.</div>';
    exit;
}

include_once __DIR__ . '/../config/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<div class="alert alert-danger">Invalid request id.</div>';
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, m.member_code, m.name_bn, m.name_en, m.mobile FROM account_close a LEFT JOIN members_info m ON m.id = a.member_id WHERE a.id = ? LIMIT 1");
$stmt->execute([$id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) {
    echo '<div class="alert alert-danger">Request not found.</div>';
    exit;
}

$date = !empty($r['created_at']) ? date('d F Y', strtotime($r['created_at'])) : date('d F Y');
$member_name = $r['name_bn'] ?: $r['name_en'];
$account_no = $r['member_code'];
$mobile = $r['mobile'] ?? '';

function bn_digits($value) {
    $map = [
        '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪',
        '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯'
    ];
    return strtr((string)$value, $map);
}

?>
<div class="container-fluid">
    <div class="p-3" style="margin-top: 50px"> 
        <p class="text-end"><?php echo htmlspecialchars($date); ?></p>

        <p>বরাবর<br/>
        সভাপতি<br/>
        কোডার পেশাজীবী সমবায় সমিতি লিঃ
        </p>

        <p>বিষয়: সদস্যপদ ও হিসাব বন্ধ করার আবেদন।</p>

        <p>জনাব/জনাবা,</p>

        <p>বিনীত নিবেদন এই যে, আমার নিম্নোক্ত সদস্যপদ ও হিসাবটি বন্ধ করার জন্য আপনার সদয় অনুমতি প্রার্থনা করছি।</p>

        <p><strong>হিসাবের নাম:</strong> <?php echo htmlspecialchars($member_name); ?><br/>
        <strong>হিসাব নম্বর:</strong> <?php echo htmlspecialchars($account_no); ?></p>
        <p><strong>কারণ:</strong>
        <?php echo nl2br(htmlspecialchars($r['reasons'])); ?></p>
        <p><strong>অর্থের বিবরণ:</strong><br/><br/>
        
        <table style="width: 80%; margin-bottom: 4px;">
            <tr>
                <td style="width: 50%;"><strong>মোট জমা:</strong></td>
                <td style="width: 50%; text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['total_deposited'],2))); ?> /-</strong></td>
            </tr>
            <tr>
                <td><strong>অফেরৎযোগ্য ফি:</strong></td>
                <td style="text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['none_refund'],2))); ?> /-</strong></td>
            </tr>
            <tr>
                <td><strong>মোট পরিমাণ:</strong></td>
                <td style="text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['total_amt'],2))); ?> /-</strong></td>
            </tr>
            <tr>
                <td><strong>কর্তনযোগ্য ফি (১০%):</strong></td>
                <td style="text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['deduction'],2))); ?> /-</strong></td>
            </tr>
            <tr>
                <td><strong>ফেরতযোগ্য পরিমাণ:</strong></td>
                <td style="text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['refund_amt'],2))); ?> /-</strong></td>
            </tr>
            <tr>
                <td><strong>মওকুফযোগ্য ফি:</strong></td>
                <td style="text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['waiver'],2))); ?> /-</strong></td>
            </tr>
            <tr>
                <td><strong>&nbsp;</strong></td>
                <td style="text-align: right;"><strong>&nbsp;</strong></td>
            </tr>
            <tr>
                <td><strong>মোট ফেরৎযোগ্য ফি:</strong></td>
                <td style="text-align: right;"><strong><?php echo htmlspecialchars(bn_digits(number_format((float)$r['refund_amt'] + (float)$r['waiver'],2))); ?> /-</strong></td>
            </tr>
        </table>
        <br/><br/>
        <p>অতএব, প্রয়োজনীয় ব্যবস্থা গ্রহণ করে আমার সদস্যপদ ও হিসাবটি বন্ধ করার জন্য বিশেষভাবে অনুরোধ জানাচ্ছি।</p>

        <p>ধন্যবাদান্তে,</p>

        <p>বিনীত<br/><br/><br/>
        <?php echo htmlspecialchars($member_name); ?><br/>
        <?php echo htmlspecialchars($mobile); ?></p>
    </div>
</div>
