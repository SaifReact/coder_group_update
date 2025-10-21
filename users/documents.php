<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../config/config.php';

include_once __DIR__ . '/../includes/open.php';

?>

<div class="container-fluid pb-5 hero-header bg-light">
  <div class="row">
    <?php include_once __DIR__ . '/../includes/side_bar.php'; ?>
    <main class="col-12 col-md-9 col-lg-9 px-md-4">
      <div class="container">
        <div class="card shadow-lg rounded-3 border-0">
          <div class="card-body p-4">
            <h3 class="mb-3 text-primary fw-bold">Documents Upload <span class="text-secondary">( ডকুমেন্টস আপলোড )</span></h3>
            <hr class="mb-4" />

            <form id="docForm" action="../process/upload_docs.php" method="POST" enctype="multipart/form-data">
              <div class="row g-3">
                <!-- Document Type -->
                <div class="col-md-6">
                  <label for="required_document_select" class="form-label fw-semibold">প্রয়োজনীয় ডকুমেন্ট (Required Document)</label>
                  <select class="form-select shadow-sm" id="required_document_select" required>
                    <option value="">নির্বাচন করুন (Select)</option>
                    <option value="101">জাতীয় পরিচয়পত্র / জন্ম সনদ</option>
                    <option value="102">স্বাক্ষর</option>
                    <option value="103">শিক্ষাগত যোগ্যতার সনদ</option>
                    <option value="104">অস্থায়ী নাগরিক সনদ</option>
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
                <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                  Save Documents (ডকুমেন্টস সংরক্ষণ করুন)
                </button>
              </div>
            </form>
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
  104: 'অস্থায়ী নাগরিক সনদ'
};

const retainedDocs = {}; // { docType: File }
let currentDocType = '';
const selectEl = document.getElementById('required_document_select');
const fileEl = document.getElementById('required_document_file');
const preview = document.getElementById('requiredDocumentPreview');
const form = document.getElementById('docForm');

selectEl.addEventListener('change', () => {
  currentDocType = selectEl.value;
  fileEl.value = '';
});

fileEl.addEventListener('change', () => {
  if (!currentDocType) {
    showToast('ডকুমেন্ট টাইপ নির্বাচন করুন।', 'error');
    fileEl.value = '';
    return;
  }
  const file = fileEl.files[0];
  if (!file) return;

  const ext = file.name.split('.').pop().toLowerCase();
  if (!['jpg','jpeg','png'].includes(ext)) {
    showToast('শুধুমাত্র JPG/PNG ফাইল দিন।', 'error');
    fileEl.value = '';
    return;
  }

  // Add/replace the file for this docType
  retainedDocs[currentDocType] = file;
  render();
  fileEl.value = '';
});

function render() {
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
    rm.onclick = () => { delete retainedDocs[docType]; render(); };
    col.appendChild(rm);

    preview.appendChild(col);
  });
}

form.addEventListener('submit', async e => {
  e.preventDefault();

  const types = Object.keys(retainedDocs);
  if (types.length === 0) {
    showToast('কমপক্ষে একটি ডকুমেন্ট যুক্ত করুন।', 'error');
    return;
  }

  const fd = new FormData(form);
  types.forEach(type => {
    fd.append('required_document_types[]', type);
    fd.append('required_documents[]', retainedDocs[type], retainedDocs[type].name);
  });

  try {
    const resp = await fetch(form.action, { method: 'POST', body: fd });
    const data = await resp.json();
    if (data.success) {
      showToast('ডকুমেন্টগুলো সফলভাবে আপলোড হয়েছে।', 'success');
      Object.keys(retainedDocs).forEach(k => delete retainedDocs[k]);
      render();
    } else {
      showToast(data.message || 'সেভ করতে সমস্যা হয়েছে।', 'error');
    }
  } catch (err) {
    console.error(err);
    showToast('নেটওয়ার্ক/সার্ভার ত্রুটি।', 'error');
  }
});
</script>

<?php include_once __DIR__ . '/../includes/end.php'; ?>
