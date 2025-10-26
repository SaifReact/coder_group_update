<?php

include_once __DIR__ . '/config/config.php';

// Access specific data from session
$stmt = $pdo->prepare("SELECT * FROM members_info a, member_share b WHERE a.id = b.member_id ORDER BY a.id ASC");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
   <?php include_once __DIR__ . '/includes/open.php'; ?>
   <!-- Hero Start -->
   <div class="container-fluid pb-3 hero-header bg-light">
     <div class="container">
       <div class="row justify-content-center">
         <div class="col-12 col-md-12 col-lg-12 col-xl-12">
           <div class="glass-card-header mb-1">
             <h5 class="text-center fw-bold" style="color:#045D5D; letter-spacing:1px; text-shadow:1px 2px 8px #fff8; font-size:1.5rem; font-family:'Poppins',sans-serif;">সদস্যদের তালিকা ( List of members )</h5> 
             <div class="table-responsive">
             <table class="table table-bordered">
             <thead>
               <tr>
                 <th scope="col">ক্রমিক নং</th>
                 <th scope="col">সদস্য কোড</th>
                 <th scope="col">নাম</th>
                 <th scope="col">জন্ম তারিখ</th>
                 <th scope="col">শেয়ার সংখ্যা</th>
                 <th scope="col">পিতার নাম</th>
                 <th scope="col">মাতার নাম</th>
               </tr>
             </thead>
             <tbody>
            <?php $rownum = 1; ?>
            <?php foreach ($members as $member): ?>
            <tr>
              <td class="text-center"><?= $rownum++; ?></td>
              <td><?= htmlspecialchars($member['member_code']); ?></td>
              <td><?= htmlspecialchars($member['name_en']); ?><br/><?= htmlspecialchars($member['name_bn']); ?></td>
              <td><?= htmlspecialchars($member['dob']); ?></td>
              <td class="text-center"><?= htmlspecialchars($member['no_share']); ?></td>
              <td><?= htmlspecialchars($member['father_name']); ?></td>
              <td><?= htmlspecialchars($member['mother_name']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
           </table>
           </div>
         </div>
       </div>
     </div>
   </div>
   </div>
   <!-- Hero End --> <?php include_once __DIR__ . '/includes/end.php'; ?>