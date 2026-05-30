<?php include_once __DIR__ . '/../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<?php include_once __DIR__ . '/../includes/head.php'; ?>

<body>
    <div class="container-fluid bg-light sticky-top">
        <div class="container">
            <?php include_once __DIR__ . '/../includes/menu.php'; ?>
        </div>
    </div>

    <div class="container bg-success text-white py-5">
        <div class="container text-center py-5">
            <h1 class="display-5 fw-bold">Coder Mart eCommerce</h1>
            <p class="lead mb-4">Explore our online shop with featured products, real product images, and easy browsing.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-light btn-lg">Back to Home</a>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="https://via.placeholder.com/600x400?text=Smartphone+X1" class="card-img-top" alt="Smartphone X1">
                    <div class="card-body">
                        <h5 class="card-title">Smartphone X1</h5>
                        <p class="card-text">A premium smartphone with crystal-clear display, fast performance, and long battery life.</p>
                        <p class="fw-bold">৳22,500</p>
                        <a href="#" class="btn btn-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="https://via.placeholder.com/600x400?text=Business+Laptop" class="card-img-top" alt="Business Laptop">
                    <div class="card-body">
                        <h5 class="card-title">Business Laptop</h5>
                        <p class="card-text">Lightweight and powerful laptop built for office work, multitasking, and online meetings.</p>
                        <p class="fw-bold">৳45,900</p>
                        <a href="#" class="btn btn-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="https://via.placeholder.com/600x400?text=Wireless+Headphones" class="card-img-top" alt="Wireless Headphones">
                    <div class="card-body">
                        <h5 class="card-title">Wireless Headphones</h5>
                        <p class="card-text">Noise-cancelling headphones with premium sound and comfortable all-day wear.</p>
                        <p class="fw-bold">৳7,200</p>
                        <a href="#" class="btn btn-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="https://via.placeholder.com/600x400?text=Smartwatch+Pro" class="card-img-top" alt="Smartwatch Pro">
                    <div class="card-body">
                        <h5 class="card-title">Smartwatch Pro</h5>
                        <p class="card-text">Track fitness, notifications, and health metrics in a sleek, durable smartwatch.</p>
                        <p class="fw-bold">৳9,800</p>
                        <a href="#" class="btn btn-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="https://via.placeholder.com/600x400?text=Bluetooth+Speaker" class="card-img-top" alt="Bluetooth Speaker">
                    <div class="card-body">
                        <h5 class="card-title">Bluetooth Speaker</h5>
                        <p class="card-text">Portable speaker with deep bass, clear sound, and long playback time.</p>
                        <p class="fw-bold">৳3,950</p>
                        <a href="#" class="btn btn-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="https://via.placeholder.com/600x400?text=Digital+Camera" class="card-img-top" alt="Digital Camera">
                    <div class="card-body">
                        <h5 class="card-title">Digital Camera</h5>
                        <p class="card-text">Capture stunning photos and videos with a compact camera designed for everyday use.</p>
                        <p class="fw-bold">৳18,300</p>
                        <a href="#" class="btn btn-success">Add to Cart</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5">
            <div class="col-md-6">
                <h2 class="mb-3">Fast, secure online shopping</h2>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Browse popular items with product images and clear pricing.</li>
                    <li class="list-group-item">Secure payment options and easy order tracking.</li>
                    <li class="list-group-item">Reliable delivery and customer support for purchases.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-light rounded shadow-sm">
                    <h5>Need a product recommendation?</h5>
                    <p>Contact our support team to find the best item for your needs and budget.</p>
                    <a href="<?= BASE_URL ?>contact.php" class="btn btn-success">Contact Us</a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/end.php'; ?>
</body>

</html>
