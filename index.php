<?php include_once __DIR__ . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<?php include_once __DIR__ . '/includes/head.php'; ?>

<body>
    <!-- Spinner Start 
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">কোডার পেশাজীবী সমবায় সমিতি লিঃ</span>
        </div>
    </div>
    Spinner End -->

    <!-- Navbar Start -->
    <div class="container-fluid bg-light sticky-top">
        <div class="container">
            <?php include_once __DIR__ .'/includes/menu.php'; ?>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Hero Start -->
    <div class="container-fluid pb-3 hero-header bg-light mb-3">
        <div class="container py-3">
            <?php include_once __DIR__ .'/includes/slider.php'; ?>
        </div>
    </div>
    <!-- Hero End -->

    <!-- About Ticket -->
    <div class="container-fluid py-3">
        <?php include_once __DIR__ .'/includes/ticket.php'; ?>
    </div>
    <!-- About Ticket -->

    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <?php include_once __DIR__ .'/includes/about.php'; ?>
        </div>
    </div>
    <!-- About End -->

    <!-- Project Start -->
    <div class="container-fluid bg-light py-5">
        <div class="container py-3">
            <?php include_once __DIR__ .'/includes/project.php'; ?>
        </div>
    </div>
    <!-- Project End -->

    <!-- Service Start -->
    <div class="container-fluid py-5">
        <?php include_once __DIR__ .'/includes/service.php'; ?>
    </div>
    <!-- Service End -->

    <!-- Team Start -->
    <div class="container-fluid bg-light py-5">
        <?php include_once __DIR__ .'/includes/team.php'; ?>
    </div>
    <!-- Team End -->

   <?php include_once __DIR__ . '/includes/end.php'; ?>