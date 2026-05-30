<?php include_once __DIR__ . '/../config/config.php';

$companyStmt = $pdo->query("SELECT company_page_url, company_image, company_name_en, company_name_bn FROM company ORDER BY id ASC LIMIT 1");
$company = $companyStmt->fetch();
$companyLink = $company['company_page_url'] ?? '';
$companyImage = $company['company_image'] ?? '';
$companyAlt = ($company['company_name_en'] ?? 'Company') . ' - ' . ($company['company_name_bn'] ?? 'কোম্পানি');
?>
<!DOCTYPE html>
<html lang="bn">

<?php include_once __DIR__ . '/../includes/head.php'; ?>

<body>
    <div class="container-fluid bg-light sticky-top">
        <div class="container">
            <?php include_once __DIR__ . '/../includes/menu.php'; ?>
        </div>
    </div>

    <div class="container bg-primary text-white py-5">
        <div class="container text-center py-5">
            <h1 class="display-5 fw-bold">কোডার ফিন্যান্স সম্পর্কে</h1>
            <p class="lead mb-4">একটি সম্প্রদায়-চালিত আর্থিক মডেল যেখানে সদস্যরা একত্রিত হয়ে পরস্পরকে আর্থিক সহায়তা প্রদান করে।</p>
            <a href="<?= BASE_URL ?>" class="btn btn-light btn-lg">মুখ্য পৃষ্ঠা</a>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">১. সমবায় সমিতির আর্থিক কাঠামো</h2>
                        <p class="mb-2">একটি সমবায় সমিতি হলো একটি সম্প্রদায়-চালিত আর্থিক মডেল, যেখানে সদস্যরা তাদের সঞ্চয় একত্রিত করে পরস্পরকে ক্ষুদ্র ঋণ প্রদান করেন। এটি বাংলাদেশের সমবায় আইনের অধীনে পরিচালিত হয়ে পারস্পরিক অর্থনৈতিক প্রবৃদ্ধি উৎসাহিত করে।</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>শেয়ার মূলধন:</strong> সদস্যরা প্রাথমিক তহবিল প্রতিষ্ঠা করতে এবং ভোটাধিকার লাভ করতে শেয়ার ক্রয় করেন।</li>
                            <li class="list-group-item"><strong>সঞ্চয় (Savings):</strong> দৈনিক, সাপ্তাহিক বা মাসিক ভিত্তিতে করা ক্ষুদ্র সঞ্চয়ী আমানত, যা একজন ব্যক্তির ঋণ গ্রহণের ক্ষমতা বৃদ্ধি করে।</li>
                            <li class="list-group-item"><strong>ডিপিএস (ডিপোজিট পেনশন স্কিম):</strong> সদস্যরা নির্দিষ্ট মাসিক/সাপ্তাহিক পরিমাণ অর্থ বিনিয়োগ করেন এবং মেয়াদপূর্তিতে সুদসহ এককালীন অর্থ লাভ করেন।</li>
                            <li class="list-group-item"><strong>এফডিআর (ফিক্সড ডিপোজিট রিসিপ্ট):</strong> একটি নির্দিষ্ট মেয়াদের জন্য রাখা এককালীন বিনিয়োগ যা থেকে নিয়মিত মুনাফা প্রদান করা হয়।</li>
                            <li class="list-group-item"><strong>ঋণ:</strong> সদস্যরা ব্যবসায়িক বিনিয়োগের জন্য সঞ্চয় বা জামানতের বিপরীতে সাধারণত Tk 1,000 থেকে Tk 10 লাখ পর্যন্ত ঋণ নিতে পারেন।</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">২. পরিচালন ও নিয়ন্ত্রক কাঠামো</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>প্রবিধান:</strong> বাংলাদেশ সংবিধানের অধীনে আনুষ্ঠানিকভাবে স্বীকৃত এবং সমবায় অধিদপ্তরের অধীনে নিবন্ধিত।</li>
                            <li class="list-group-item"><strong>গণতান্ত্রিক নিয়ন্ত্রণ:</strong> ‘এক সদস্য, এক ভোট’ নীতিতে শাসিত, যা একচেটিয়া আর্থিক নিয়ন্ত্রণ প্রতিরোধ করে।</li>
                            <li class="list-group-item"><strong>মুনাফা বন্টন:</strong> ঋণ থেকে প্রাপ্ত সুদ সদস্যদের মধ্যে লভ্যাংশ হিসাবে পুনর্বন্টন করা হয় অথবা সমিতির তহবিল বৃদ্ধির জন্য সংরক্ষণ করা হয়।</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">৩. ব্যবস্থাপনা ও ডিজিটাল সমাধান</h2>
                        <p class="mb-3">সঠিক আর্থিক হিসাবরক্ষণ একটি সমিতির টিকে থাকার মেরুদণ্ড। ঢাকার মিরপুরের মতো এলাকার অনেক আধুনিক সমবায় সমিতি তাৎক্ষণিকভাবে খতিয়ান হিসাব প্রক্রিয়াকরণ, ঋণের কিস্তির হিসাব রাখা এবং দীর্ঘমেয়াদী তথ্য সুরক্ষিত করার জন্য স্বয়ংক্রিয় মাইক্রো-ক্রেডিট সমবায় সমিতি সফটওয়্যার ব্যবহার করে।</p>
                        <div class="alert alert-info">
                            <strong>নোট:</strong> একটি শক্তিশালী ডিজিটাল হিসাব ব্যবস্থা সমবায় সমিতির স্বচ্ছতা, কার্যকারিতা এবং সদস্য বিশ্বাস বৃদ্ধি করে।
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>contact.php" class="btn btn-primary btn-lg">যোগাযোগ করুন</a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/end.php'; ?>
</body>

</html>
