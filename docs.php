<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';

// Always parameterize + fetch *all* rows when you plan to loop
$sql  = "SELECT banner_image, banner_name_bn, banner_name_en FROM banner WHERE banner_category = :cat";
$stmt = $pdo->prepare($sql);
$stmt->execute([':cat' => 'oth']);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);   // <-- important

include_once __DIR__ . '/includes/open.php';
?>

<!-- Hero Start -->
<div class="container-fluid pb-3 hero-header bg-light">
    <div class="row px-4">
      <div class="col-12">
        <div class="glass-card-header mb-1">
          <h5 class="text-center fw-bold" style="color:#045D5D; letter-spacing:1px; text-shadow:1px 2px 8px #fff8; font-size:1.5rem; font-family:'Poppins',sans-serif;">
            নিবন্ধিত সমিতির ডকুমেন্টস দেখুন ( View the documents of the registered association )
          </h5>
        </div>

        <div class="mb-4">
          <div class="glass-card mb-2">
            <div class="row g-3">
              <?php if (!empty($docs)): ?>
                <?php foreach ($docs as $doc): ?>
                  <?php
                    // Fetch both Bangla and English document names along with the PDF file
                    $fileNameBn = $doc['banner_name_bn'] ?? '';
                    $fileNameEn = $doc['banner_name_en'] ?? '';
                    $file = $doc['banner_image'] ?? '';
                    $safeFile = rawurlencode(basename($file)); // Safe file path
                    $pdfUrl = "/banner/{$safeFile}";
                  ?>
                  <div class="col-md-6">
                    <div class="ratio ratio-4x3">
                      <iframe
                        src="<?php echo htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8'); ?>"
                        title="PDF"
                        loading="lazy"
                        style="border:0;width:100%;height:100%;"
                      ></iframe>
                    </div>

                    <!-- Display both Bangla and English names -->
                    <p class="mt-2 small text-muted text-center pt-3">
                      <?php echo htmlspecialchars($fileNameBn, ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($fileNameEn, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="col-12">
                  <div class="alert alert-info mb-0">No documents found for this category.</div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </div>
</div>
<!-- Hero End -->
<?php include_once __DIR__ . '/includes/end.php'; ?>
