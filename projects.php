<style>
        .project-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 24px;
            margin: 32px auto;
            max-width: 1200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .project-card {
            background: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            margin-bottom: 24px;
            transition: box-shadow 0.2s;
            min-height: 320px;
        }
        .project-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        }
        .project-header {
            padding: 0;
            background: #f5f7fa;
            border-radius: 8px 8px 0 0;
        }
        .project-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
            background: #e9ecef;
            display: block;
        }
        .project-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2px;
        }
        .project-meta {
            color: #888;
            font-size: 0.95rem;
        }
        .project-desc {
            padding: 16px;
            font-size: 1rem;
            color: #444;
        }
        .project-footer {
            padding: 0 16px 12px 16px;
            color: #666;
            font-size: 0.95rem;
        }
    </style>
<?php
// Helper: Convert English numbers to Bangla
function bn_number($number) {
    $en = array('0','1','2','3','4','5','6','7','8','9');
    $bn = array('০','১','২','৩','৪','৫','৬','৭','৮','৯');
    return str_replace($en, $bn, $number);
}
// Helper: Format date to Bangla (Y-m-d to d M, Y in Bangla)
function bn_date($date) {
    if (!$date) return '';
    $months = ['Jan'=>'জানুয়ারি','Feb'=>'ফেব্রুয়ারি','Mar'=>'মার্চ','Apr'=>'এপ্রিল','May'=>'মে','Jun'=>'জুন','Jul'=>'জুলাই','Aug'=>'আগস্ট','Sep'=>'সেপ্টেম্বর','Oct'=>'অক্টোবর','Nov'=>'নভেম্বর','Dec'=>'ডিসেম্বর'];
    $t = strtotime($date);
    $d = date('d', $t);
    $m = $months[date('M', $t)] ?? date('M', $t);
    $y = date('Y', $t);
    return bn_number($d) . ' ' . $m . ', ' . bn_number($y);
}
// project.php - Show all projects from the project table in a styled div
include_once __DIR__ . '/config/config.php'; 

// Fetch all projects
$stmt = $pdo->prepare("SELECT * FROM project ORDER BY id DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/includes/open.php'; 
?>

<div class="container-fluid pb-3 hero-header bg-light">
    <div class="row px-4">
            <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                <div class="glass-card-header mb-1">
                <h5 class="text-center fw-bold mb-4" style="color:#045D5D; letter-spacing:1px; text-shadow:1px 2px 8px #fff8; font-size:1.5rem; font-family:'Poppins',sans-serif;">সকল প্রকল্প ( All Projects )</h5>
                <hr />
                <?php if (count($projects) > 0): ?>
                    <div class="row">
                        <?php foreach ($projects as $project): ?>
                            <div class="col-md-6">
                                <div class="project-card mb-4">
                                    <div class="project-header">
                                        <?php
                                        // Use a placeholder image if no image field exists
                                        $img = !empty($project['image']) ? '../assets/img/' . htmlspecialchars($project['image']) : 'https://via.placeholder.com/600x180?text=Project';
                                        ?>
                                        <img src="<?php echo $img; ?>" class="project-img" alt="Project Image">
                                    </div>
                                    <div class="p-3">
                                        <div class="project-title"><?php echo htmlspecialchars($project['project_name_bn']); ?> (<?php echo htmlspecialchars($project['project_name_en']); ?>)</div>
                                        <div class="project-meta mb-2">
                                            প্রকল্প শুরুর তারিখ: <b><?php echo bn_date($project['start_date']); ?></b> <br/>
                                            প্রকল্প শেষের তারিখ: <b><?php echo bn_date($project['end_date']); ?></b> <br/>
                                            প্রকল্প মূল্যায়ন: <b><?php echo bn_number(htmlspecialchars($project['project_value'])); ?>/-</b>
                                            <?php if (!empty($project['per_share_value'])): ?>
                                                | প্রতিটি শেয়ার মূল্য: <b><?php echo bn_number(htmlspecialchars($project['per_share_value'])); ?>/-</b>
                                            <?php endif; ?>
                                            <br/>
                                            সর্বমোট শেয়ার: <b><?php echo bn_number(htmlspecialchars($project['project_share'])); ?> টি</b> |
                                            শেয়ার নেওয়ার শেষ তারিখ: <b><?php echo bn_date($project['member_last_entry_date']); ?></b>
                                        </div>
                                        <div class="project-desc">                                            <?php if (!empty($project['about_project'])): ?>
                                                <?php echo nl2br(strip_tags($project['about_project'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">No description available.</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="project-footer mt-2">
                                            <?php if (!empty($project['dateline'])): ?>
                                                <span>Dateline: <b><?php echo htmlspecialchars($project['dateline']); ?></b></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No projects found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
</div>

<?php include_once __DIR__ . '/includes/end.php'; ?>
