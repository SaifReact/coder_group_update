<?php
// DB config
include_once __DIR__ . '/../config/config.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'];

// Helper: Generate next member_code (CPSS-00001...)
function generateMemberCode($pdo) {
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM members_info");
    $row = $stmt->fetch();
    $next = ($row && $row['max_id']) ? $row['max_id'] + 1 : 1;
    return 'CPSS-' . str_pad($next, 5, '0', STR_PAD_LEFT);
}

// Helper function to send SMS
function sms_send($mobile, $message) {
    $sms_api_url = "http://bulksmsbd.net/api/smsapi";
    $api_key = "B5NrU3gcYDTzS4AdGGIo";
    $sender_id = "8809648903446";

    $data = [
        'api_key' => $api_key,
        'type' => 'text',
        'number' => $mobile,
        'senderid' => $sender_id,
        'message' => $message,
    ];

    error_log("SMS Data: " . print_r($data, true));

    $url = $sms_api_url . '?' . http_build_query($data);
    error_log("Generated SMS URL: $url");

    error_log("Sending SMS to: $mobile with message: $message");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    error_log("SMS Response: $response");
    return $response;
}

if ($method === 'POST') {
    try {
        $pdo->beginTransaction();

        // Check if NID already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM members_info WHERE nid = ?");
        $stmt->execute([$_POST['nid']]);
        $nidExists = $stmt->fetchColumn();

        $agreeVal = base64_encode('1');

        if ($nidExists > 0) {
            $_SESSION['error_msg'] = '❌ এই জাতীয় পরিচয়পত্র/জন্ম নিবন্ধন নং মধ্যে নিবন্ধিত (This NID/BRN No. is already registered)'; 
            header('Location: ../forms.php?agreed=' . $agreeVal);
            exit;
        }

        // Insert new member
        $member_code = generateMemberCode($pdo);
        $fields = [
            'name_bn', 'name_en', 'father_name', 'mother_name', 'nid', 'dob', 'religion', 'marital_status', 'spouse_name',
            'mobile', 'gender', 'education', 'agreed_rules', 'ref_no', 'email', 'memberType'
        ];
        
        $data = [];
        
        foreach ($fields as $f) {
            $data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : null;
        }

        // Handle profile image upload
        $profile_image_path = null;
        if (isset($_FILES['profile_image']) && is_uploaded_file($_FILES['profile_image']['tmp_name'])) {
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_extensions)) {
                $userImgDir = dirname(__DIR__) . '/user_images';
                $memberDir = $userImgDir . '/member_' . $member_code;
                if (!is_dir($memberDir)) {
                    mkdir($memberDir, 0777, true);
                }
                $imgFileName = 'profile_image_' . time() . '_' . uniqid() . '.' . $ext;
                $imgPath = $memberDir . '/' . $imgFileName;
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $_FILES['profile_image']['tmp_name']);
                finfo_close($finfo);
                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/jpg'];
                if (in_array($mimeType, $allowed_mime_types)) {
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $imgPath)) {
                        $profile_image_path = 'user_images/member_' . $member_code . '/' . $imgFileName;
                    } else {
                        throw new Exception('Error uploading the profile image.');
                    }
                } else {
                    throw new Exception('Invalid image file type.');
                }
            } else {
                throw new Exception('Invalid file extension.');
            }
        }
        
        $ref_no = !empty($data['ref_no']) ? $data['ref_no'] : $member_code;
        $agreeValue = !empty($data['agreed_rules']) ? $data['agreed_rules'] : 1;

        // Insert into members_info
        $sql = "INSERT INTO members_info (member_code, name_bn, name_en, father_name, mother_name, nid, dob, religion, marital_status, spouse_name, mobile, gender, education, agreed_rules, profile_image, created_at, ref_no, email, member_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?,?)";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            $member_code,
            $data['name_bn'],
            $data['name_en'],
            $data['father_name'],
            $data['mother_name'],
            $data['nid'],
            $data['dob'],
            $data['religion'],
            $data['marital_status'],
            $data['spouse_name'],
            $data['mobile'],
            $data['gender'],
            $data['education'],
            $agreeValue,
            $profile_image_path,
            $ref_no,
            $data['email'],
            $data['memberType']
        ]);
        if (!$ok) throw new Exception('Insert failed (members_info)');

        $member_id = $pdo->lastInsertId();

        // Insert into member_office
        $office_fields = ['office_name', 'office_address', 'position', 'present_address', 'permanent_address'];
        $office_data = [];
        foreach ($office_fields as $f) {
            $office_data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : null;
        }
        $sql_office = "INSERT INTO member_office (member_id, member_code, office_name, office_address, position, created_at, present_address, permanent_address) VALUES (?,?,?,?,?,NOW(),?,?)";
        $stmt_office = $pdo->prepare($sql_office);
        $ok_office = $stmt_office->execute([
            $member_id, $member_code,
            $office_data['office_name'], $office_data['office_address'], $office_data['position'],
            $office_data['present_address'], $office_data['permanent_address']
        ]);
        if (!$ok_office) throw new Exception('Insert failed (member_office)');

        // Insert into member_nominee
        $nominee_names = $_POST['nominee_name'] ?? [];
        $nominee_relations = $_POST['nominee_relation'] ?? [];
        $nominee_nids = $_POST['nominee_nid'] ?? [];
        $nominee_dobs = $_POST['nominee_dob'] ?? [];
        $nominee_percents = $_POST['nominee_percent'] ?? [];
        $nominee_images = $_FILES['nominee_image'] ?? null;
        for ($i = 0; $i < count($nominee_names); $i++) {
            $nominee_image_path = null;
            if ($nominee_images && isset($nominee_images['tmp_name'][$i]) && is_uploaded_file($nominee_images['tmp_name'][$i])) {
                $allowed_extensions = ['jpg', 'jpeg', 'png'];
                $ext = strtolower(pathinfo($nominee_images['name'][$i], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed_extensions)) {
                    $userImgDir = dirname(__DIR__) . '/user_images';
                    $memberDir = $userImgDir . '/member_' . $member_code;
                    $imgFileName = 'nominee_' . ($i+1) . '_' . time() . '_' . uniqid() . '.' . $ext;
                    $imgPath = $memberDir . '/' . $imgFileName;
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $nominee_images['tmp_name'][$i]);
                    finfo_close($finfo);
                    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (in_array($mimeType, $allowed_mime_types)) {
                        if (move_uploaded_file($nominee_images['tmp_name'][$i], $imgPath)) {
                            $nominee_image_path = 'user_images/member_' . $member_code . '/' . $imgFileName;
                        } else {
                            throw new Exception('Error uploading the nominee image.');
                        }
                    } else {
                        throw new Exception('Invalid nominee image file type.');
                    }
                } else {
                    throw new Exception('Invalid nominee file extension.');
                }
            }
            $sql_nominee = "INSERT INTO member_nominee (member_id, member_code, name, relation, nid, dob, percentage, nominee_image) VALUES (?,?,?,?,?,?,?,?)";
            $stmt_nominee = $pdo->prepare($sql_nominee);
            $ok_nominee = $stmt_nominee->execute([
                $member_id,
                $member_code,
                $nominee_names[$i] ?? '',
                $nominee_relations[$i] ?? '',
                $nominee_nids[$i] ?? '',
                $nominee_dobs[$i] ?? '',
                $nominee_percents[$i] ?? '',
                $nominee_image_path
            ]);
            
            if (!$ok_nominee) throw new Exception('Nominee Insert Failed');
            
        }

        // Insert into member_share
                $sql_share = "INSERT INTO member_share (member_id, member_code, no_share, admission_fee, idcard_fee, passbook_fee, softuses_fee, project_id, extra_share, late_assign, late_fee, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())";
                $stmt_share = $pdo->prepare($sql_share);
                $extraShare = isset($_POST['share']) ? ($_POST['share'] - 2) : 0;
                $ok_share = $stmt_share->execute([
                    $member_id, $member_code,
                    $_POST['share'] ?? 0,
                    $_POST['admission_fee'] ?? 0,
                    $_POST['idcard_fee'] ?? 0,
                    $_POST['passbook_fee'] ?? 0,
                    $_POST['softuses_fee'] ?? 0,
                    $_POST['project'] ?? 0,
                    $extraShare,
                    'A',
                    0
                ]);
        
        if (!$ok_share) throw new Exception('Share Insert Failed');

        $username = trim($_POST['username'] ?? '');
        $password = md5(trim($_POST['password'] ?? ''));
        $re_password = trim($_POST['retype_password'] ?? '');
        $sql_user = "INSERT INTO user_login (member_id, member_code, user_name, password, re_password, role, status, created_at) VALUES (?,?,?,?,?,?,?,NOW())";
        $stmt_user = $pdo->prepare($sql_user);
        $ok_user = $stmt_user->execute([
            $member_id, $member_code, $username, $password, $re_password, 'user', 'I'
        ]);
        
        if (!$ok_user) throw new Exception('User Insert Failed');

        $pdo->commit();

        $success_msg = '✅ আপনার আবেদনটি সফলভাবে সিস্টেমে সংরক্ষিত হয়েছে, অনুমোদনের জন্য অপেক্ষা করুন অথবা ওয়েবসাইট এর মোবাইল নম্বরে যোগাযোগ করে অনুমোদনের তথ্য জানুন। ব্যাংক হিসাব নাম- কোডার পেশাজীবী সমবায় সমিতি লিঃ, ইসলামিক হিসাব নং- ৫০৩০১০০১৭৬৩, পল্টন ব্রাঞ্চ, ব্যাংক এশিয়া লিঃ, ঢাকা। আপনার সদস্য নং-' . $member_code . ', সদস্য নাম- ' . $data['name_bn'] . ', কোডার পেশাজীবী সমবায় সমিতি লিঃ নিবন্ধন করার জন্য, আপনাকে ধন্যবাদ।';
        
        
        if ($data['mobile']) {
            $sms_response = sms_send($data['mobile'], $success_msg);
            if ($sms_response === false) {
                $sms_error_msg = '❌ SMS পাঠানো যায়নি।';
            } else {
                $sms_result = json_decode($sms_response, true);
                if (isset($sms_result['error']) && $sms_result['error'] != 0) {
                    $sms_error_msg = '❌ SMS পাঠানো যায়নি: ' . ($sms_result['message'] ?? 'Unknown error');
                } else {
                    $sms_success_msg = '';
                    $success_msg .= ' ' . $sms_success_msg;
                }
            }    
        }

        if (isset($sms_error_msg)) {
            $success_msg .= ' ' . $sms_error_msg;
        }

        $_SESSION['success_msg'] = $success_msg;
        header('Location: ../form.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        if (isset($member_code)) {
            $userImgDir = dirname(__DIR__) . '/user_images';
            $memberDir = $userImgDir . '/member_' . $member_code;
            if (is_dir($memberDir)) {
                $files = glob($memberDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($memberDir);
            }
        }
        $_SESSION['error_msg'] = '❌ দুঃখিত, কিছু সমস্যা হয়েছে। আবার চেষ্টা করুন।' . $e->getMessage();
        header('Location: ../forms.php');
        exit;
    }
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>
