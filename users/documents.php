<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

$member_id = $_SESSION['member_id'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM member_documents WHERE member_id = ? ORDER BY doc_type ASC");
$stmt->execute([$member_id]);
$member_docs = $stmt->fetchAll();

// Fetch already uploaded document types
$uploadedDocs = [];
if (!empty($member_id)) {
    $stmtDocs = $pdo->prepare("SELECT doc_type FROM member_documents WHERE member_id = ?");
    $stmtDocs->execute([$member_id]);
    $uploadedDocs = array_map('strval', $stmtDocs->fetchAll(PDO::FETCH_COLUMN) ?: []);
}

// Document options
$docOptions = [
    '101' => 'জাতীয় পরিচয়পত্র / জন্ম সনদ',
    '102' => 'স্বাক্ষর',
    '103' => 'শিক্ষাগত যোগ্যতার সনদ',
    '104' => 'কর্মসংস্থান সনদ',
    '105' => 'অস্থায়ী নাগরিক সনদ'
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
                        <h3 class="mb-3 text-primary fw-bold">Documents Upload <span class="text-secondary">(ডকুমেন্টস আপলোড)</span></h3>
                        <hr class="mb-4" />

                        <?php include_once __DIR__ . '/../includes/toast.php'; ?>

                        <form id="docForm" action="../process/upload_docs.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="insert">
                            <div class="row g-3">
                                <!-- Document Type -->
                                <div class="col-md-6">
                                    <label for="required_document_select" class="form-label fw-semibold">
                                        প্রয়োজনীয় ডকুমেন্ট (Required Document)
                                    </label>
                                    <select class="form-select shadow-sm" id="required_document_select" required>
                                        <option value="">নির্বাচন করুন (Select)</option>
                                        <?php foreach ($docOptions as $optVal => $optLabel): 
                                            $isUploaded = in_array((string)$optVal, $uploadedDocs, true);
                                            $disabledAttr = $isUploaded ? 'disabled' : '';
                                        ?>
                                        <option value="<?= $optVal ?>" <?= $disabledAttr ?> class="<?= $isUploaded ? 'text-muted' : '' ?>">
                                            <?= $optLabel ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Upload File -->
                                <div class="col-md-6">
                                    <label for="required_document_file" class="form-label fw-semibold">ডকুমেন্ট আপলোড করুন (Upload Document)</label>
                                    <input class="form-control shadow-sm" type="file" id="required_document_file" accept=".jpg,.jpeg,.png">
                                    <div class="form-text text-muted mt-1">
                                        প্রতি টাইপ নির্বাচন করে একটি ফাইল অ্যাড করুন। একাধিক টাইপ আলাদা আলাদা করে যোগ করুন।
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div id="requiredDocumentPreview" class="row mt-3 g-2"></div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']); ?>">
                            <input type="hidden" name="member_id" value="<?= htmlspecialchars($_SESSION['member_id']); ?>">
                            <input type="hidden" name="member_code" value="<?= htmlspecialchars($_SESSION['member_code']); ?>">

                            <div class="mt-4 text-end">
                                <button type="submit" name="action" value="insert" class="btn btn-primary btn-lg px-4 shadow-sm">
                                    Save Documents (ডকুমেন্টস সংরক্ষণ করুন)
                                </button>
                            </div>
                        </form>

                        <hr class="my-4" />
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($member_docs): ?>
                                        <?php foreach ($member_docs as $doc): 
                                            $docTypeName = $docOptions[$doc['doc_type']] ?? htmlspecialchars($doc['doc_type']);
                                        ?>
                                        <tr>
                                            <td><?= $doc['doc_type']; ?></td>
                                            <td><?= $docTypeName; ?></td>
                                            <td>
                                                <img src="../<?= htmlspecialchars($doc['doc_path']); ?>" class="doc-thumb zoomable-img" style="width:30px;height:30px;" alt="Doc">
                                            </td>
                                            <td>
                                                <form action="../process/upload_docs.php" method="post" style="display:inline-block;">
                                                    <input type="hidden" name="id" value="<?= $doc['id']; ?>">
                                                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete this Document?');">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-muted text-center">No Documents</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
const docLabels = {
    101: 'জাতীয় পরিচয়পত্র / জন্ম সনদ',
    102: 'স্বাক্ষর',
    103: 'শিক্ষাগত যোগ্যতার সনদ',
    104: 'কর্মসংস্থান সনদ',
    105: 'অস্থায়ী নাগরিক সনদ'
};

const retainedDocs = {}; // { docType: File }
let currentDocType = '';
const selectEl = document.getElementById('required_document_select');
const fileEl = document.getElementById('required_document_file');
const preview = document.getElementById('requiredDocumentPreview');
const form = document.getElementById('docForm');

// When user selects document type
selectEl.addEventListener('change', () => {
    currentDocType = selectEl.value;
    fileEl.value = '';
});

// When user selects file
fileEl.addEventListener('change', () => {
    if (!currentDocType) {
        showToast('ডকুমেন্ট টাইপ নির্বাচন করুন।', 'error');
        fileEl.value = '';
        return;
    }
    const file = fileEl.files[0];
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    if (!['jpg', 'jpeg', 'png'].includes(ext)) {
        showToast('শুধুমাত্র JPG/PNG ফাইল দিন।', 'error');
        fileEl.value = '';
        return;
    }

    // Store file by docType
    retainedDocs[currentDocType] = file;
    renderPreview();
    fileEl.value = '';
});

// Render preview
function renderPreview() {
    preview.innerHTML = '';
    Object.keys(retainedDocs).forEach(docType => {
        const file = retainedDocs[docType];
        const col = document.createElement('div');
        col.className = 'col-md-3 mb-3 text-center';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.maxWidth = '100px';
        img.style.maxHeight = '100px';
        img.style.borderRadius = '4px';
        img.style.boxShadow = '0 2px 8px #0002';
        col.appendChild(img);

        const label = document.createElement('div');
        label.className = 'small mt-2 fw-bold';
        label.innerText = docLabels[docType] || docType;
        col.appendChild(label);

        const rm = document.createElement('button');
        rm.type = 'button';
        rm.className = 'btn btn-sm btn-outline-danger mt-1';
        rm.innerText = 'Remove';
        rm.onclick = () => {
            delete retainedDocs[docType];
            renderPreview();
        };
        col.appendChild(rm);

        preview.appendChild(col);
    });
}

// ✅ Submit handler (normal form submission)
form.addEventListener('submit', e => {
    const types = Object.keys(retainedDocs);

    if (types.length === 0) {
        showToast('কমপক্ষে একটি ডকুমেন্ট যুক্ত করুন।', 'error');
        e.preventDefault();
        return;
    }

    // Clean previous hidden inputs
    form.querySelectorAll('input[name="required_document_types[]"], input[name="required_documents[]"]').forEach(el => el.remove());

    // Append doc types + files
    types.forEach(type => {
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'required_document_types[]';
        typeInput.value = type;
        form.appendChild(typeInput);

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'required_documents[]';
        fileInput.files = createFileList([retainedDocs[type]]);
        form.appendChild(fileInput);
    });

    function createFileList(files) {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        return dt.files;
    }
});
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
