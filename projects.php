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
$stmt = $pdo->prepare("SELECT * FROM project WHERE id != 2 ORDER BY id DESC");
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
                            <div class="col-md-4 col-sm-6">
                                <div class="project-card mb-4" style="cursor:pointer;"
                                     onclick="openShareModal(<?= (int)$project['id'] ?>, <?= htmlspecialchars(json_encode($project['project_name_bn'] . ' (' . $project['project_name_en'] . ')'), ENT_QUOTES) ?>)">
                                    <div class="project-header">
                                        <?php
                                        $img = !empty($project['project_image'])
                                            ? htmlspecialchars($project['project_image'])
                                            : 'assets/img/logo.png';
                                        ?>
                                        <img src="<?= $img ?>" class="project-img" alt="<?= htmlspecialchars($project['project_name_bn']) ?>">
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

<!-- hidden trigger for Bootstrap modal (works before/after BS JS loads) -->
<button id="shareModalTrigger" data-bs-toggle="modal" data-bs-target="#shareModal" style="display:none;"></button>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#045D5D,#008080); color:#fff;">
                <h5 class="modal-title fw-bold" id="shareModalTitle">প্রকল্প শেয়ার তথ্য</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="shareModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted">লোড হচ্ছে...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
            </div>
        </div>
    </div>
</div>

<script>
function bn(n) {
    return String(n).replace(/[0-9]/g, function(d){ return '০১২৩৪৫৬৭৮৯'[d]; });
}

function openShareModal(projectId, projectTitle) {
    document.getElementById('shareModalTitle').textContent = projectTitle + ' — শেয়ার তথ্য';
    document.getElementById('shareModalBody').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-success" role="status"></div><p class="mt-2 text-muted">লোড হচ্ছে...</p></div>';

    // Use data-attribute trigger — works regardless of when Bootstrap JS loads
    document.getElementById('shareModalTrigger').click();

    fetch('process/project_share_process.php?project_id=' + projectId)
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (!data.success) {
                document.getElementById('shareModalBody').innerHTML =
                    '<div class="alert alert-danger">' + data.message + '</div>';
                return;
            }

            var perShare = data.project.per_share_value
                ? '<p class="text-muted small mb-3">প্রতিটি শেয়ার মূল্য: <b>৳' + bn(data.project.per_share_value) + '</b></p>'
                : '';

            var html =
                '<div class="row g-3 mb-3">' +
                    '<div class="col-6 col-md-4">' +
                        '<div class="card border-0 text-center h-100" style="background:#e8f5e9;border-radius:12px;">' +
                            '<div class="card-body py-3">' +
                                '<div style="font-size:2rem;font-weight:700;color:#2e7d32;">' + bn(data.total_shares) + '</div>' +
                                '<div class="small text-muted mt-1">মোট শেয়ার</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-6 col-md-4">' +
                        '<div class="card border-0 text-center h-100" style="background:#fff3e0;border-radius:12px;">' +
                            '<div class="card-body py-3">' +
                                '<div style="font-size:2rem;font-weight:700;color:#e65100;">' + bn(data.allocated) + '</div>' +
                                '<div class="small text-muted mt-1">বরাদ্দ শেয়ার</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-6 col-md-4">' +
                        '<div class="card border-0 text-center h-100" style="background:#fce4ec;border-radius:12px;">' +
                            '<div class="card-body py-3">' +
                                '<div style="font-size:2rem;font-weight:700;color:#880e4f;">' + bn(data.standby) + '</div>' +
                                '<div class="small text-muted mt-1">স্ট্যান্ডবাই শেয়ার</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' + perShare;

            if (data.members && data.members.length > 0) {
                html += '<h6 class="fw-bold mb-2" style="color:#045D5D;">শেয়ারধারী সদস্য তালিকা</h6>' +
                    '<div class="table-responsive">' +
                    '<table class="table table-bordered table-sm align-middle mb-0">' +
                    '<thead style="background:#045D5D;color:#fff;"><tr>' +
                    '<th>#</th><th>সদস্যের নাম</th><th>কোড</th>' +
                    '<th class="text-center">সমিতি শেয়ার</th>' +
                    '<th class="text-center">প্রকল্প শেয়ার</th>' +
                    '</tr></thead><tbody>';
                data.members.forEach(function(m, i) {
                    html += '<tr>' +
                        '<td>' + bn(i + 1) + '</td>' +
                        '<td>' + m.name_bn + '</td>' +
                        '<td><span class="badge bg-secondary">' + m.member_code + '</span></td>' +
                        '<td class="text-center"><span class="badge bg-info text-dark">' + bn(m.samity_share || 0) + '</span></td>' +
                        '<td class="text-center"><span class="badge bg-success">' + bn(m.project_shares || 0) + '</span></td>' +
                        '</tr>';
                });
                html += '</tbody></table></div>';
            } else {
                html += '<div class="alert alert-info mb-0">এই প্রকল্পে এখনো কোনো শেয়ারধারী সদস্য নেই।</div>';
            }

            document.getElementById('shareModalBody').innerHTML = html;
        })
        .catch(function() {
            document.getElementById('shareModalBody').innerHTML =
                '<div class="alert alert-danger">ডেটা লোড করতে সমস্যা হয়েছে।</div>';
        });
}
</script>

<?php include_once __DIR__ . '/includes/end.php'; ?>
