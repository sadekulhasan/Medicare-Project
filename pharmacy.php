<?php
    require 'database_connection.php';
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 16;
    $offset = ($page - 1) * $limit;

   
    $totalQuery = "SELECT COUNT(*) FROM medical_products";
    $totalParams = [];

    if ($category) {
        $totalQuery .= " WHERE product_category = ?";
        $totalParams[] = $category;
    }

    $totalStmt = $pdo->prepare($totalQuery);
    $totalStmt->execute($totalParams);
    $totalProducts = $totalStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $limit);

 
    $query = "SELECT * FROM medical_products";
    $params = [];

    if ($category) {
        $query .= " WHERE product_category = ?";
        $params[] = $category;
    }


    $query .= " ORDER BY product_name ASC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($query);
    if (!$stmt->execute($params)) {
        die("Query failed: " . implode(", ", $stmt->errorInfo()));
    }

    $products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image" href="img/short_logo.png">
    <title>Pharmacy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=43">

    <script>
        async function addToCart(productId) {
            try {
                const response = await fetch(`add_to_cart.php?product_id=${productId}`, {
                    method: 'GET'
                });
                const result = await response.text();
                if (result === 'success') {
                    showNotification("Product added to cart successfully!");
                } else {
                    showNotification("Failed to add product to cart.", true);
                }
            } catch (error) {
                console.error("Error:", error);
                showNotification("An error occurred.", true);
            }
        }

        function showNotification(message, isError = false) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.backgroundColor = isError ? '#e63946' : '#4caf50';
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
    </script>

</head>
<body>
    <section id="header">
        <a href="index.php" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

        <div>
            <ul id="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="pharmacy.php" class="active">Pharmacy</a></li>
                <li><a href="blood-bank.php">Blood Bank</a></li>
                <li><a href="ambulance.php">Ambulance</a></li>
                <li><a href="about.php">About</a></li>
                <li id="lg-bag"><a href="cart.php"><i class="fas fa-cart-shopping"></i></a></li>
                <a href="#" id="close"><i class="fas fa-xmark"></i></a>
            </ul>
        </div>

        <div id="mobile">
            <a href="cart.php"><i id="scart" class="fas fa-cart-shopping"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>

    <section id="page-header">
        <h2>Virtual Pharmacy</h2>
        <p>Purchase your necessary medical products <strong>Anywhere/Anytime</strong></p>
    </section>

    <section id="feature" class="section-p1">
        <div class="fe-box">
            <img src="img/categories/medical_device.png" alt="Medical Device">
            <h6><a href="pharmacy.php?category=medical_device">Medical Device</a></h6>
        </div>
        <div class="fe-box">
            <img src="img/categories/pregnency.png" alt="Pregnancy">
            <h6><a href="pharmacy.php?category=pregnancy">Pregnancy</a></h6>
        </div>
        <div class="fe-box">
            <img src="img/categories/intimacy.png" alt="Intimacy">
            <h6><a href="pharmacy.php?category=intimacy">Intimacy</a></h6>
        </div>
        <div class="fe-box">
            <img src="img/categories/mother_and_baby.png" alt="Mother and Baby">
            <h6><a href="pharmacy.php?category=mother_and_baby">Mother and Baby</a></h6>
        </div>
        <div class="fe-box">
            <img src="img/categories/headache.png" alt="Headache">
            <h6><a href="pharmacy.php?category=headache">Headache</a></h6>
        </div>
        <div class="fe-box">
            <img src="img/categories/stomachache.png" alt="Stomachache">
            <h6><a href="pharmacy.php?category=stomachache">Stomachache</a></h6>
        </div>
    </section>

    <section id="product1" class="section-p1">
        <div class="product-container">
            <?php if (empty($products)): ?>
                <p>No products found in this category.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                    <img src="<?php echo $product['product_image']; ?>" alt="Product Image">
                        
                        <div class="description">
                            <span><?php echo htmlspecialchars($product['product_brand']); ?></span>
                            <h5><?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <h4><?php echo htmlspecialchars($product['product_price']); ?> <span class="taka">৳</span></h4>
                        </div>

                        <a href="javascript:void(0);" onclick="addToCart(<?= $product['product_id'] ?>)" class="add-to-cart-btn">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section id="pagination" class="section-p1">
        <?php if ($page > 1): ?>
            <a href="pharmacy.php?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                <i class="fas fa-long-arrow-alt-left"></i> Previous
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="pharmacy.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>"
            class="<?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="pharmacy.php?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">
                Next <i class="fas fa-long-arrow-alt-right"></i>
            </a>
        <?php endif; ?>
    </section>

    <footer class="section-p1">
            
            <div class="col">

                <img class="logo" src="img/short_logo.png" alt="">
                <h4>Contact</h4>
                <p><strong>Address: </strong>Uttara Sector 10, Dhaka, Bangladesh</p>
                <p><strong>Phone: </strong>+88019266659041, +88018881117230</p>
                <div class="follow">
                    <h4>Follow Us</h4>
                    <div class="icon">
                        <img src="img/logo/Vector.png" alt="facebook">
                        <img src="img/logo/Vector-1.png" alt="youtube">
                        <img src="img/logo/Vector-2.png" alt="instagram">
                    </div>
                </div>
                
            </div>

            <div class="col">

                <h4>Quick Navigation</h4>
                <a href="cart.php">View Cart</a>
                <a href="blood-bank.php">Apply As Donor</a>
                
            </div>

            <div class="col">

                <h4>Further Information</h4>
                <a href="about.php">About Us</a>
                <a href="legal-information.php">Delivary Information</a>
                <a href="legal-information.php">Privacy Policy</a>
                <a href="legal-information.php">Terms and Conditions</a>

            </div>

            <div class="col install">

                <h4>Install App</h4>
                <p>The mobile version will be available soon.</p>
                <div class="row">

                    <img src="img/pay/app.jpg" alt="">
                    <img src="img/pay/play.jpg" alt="">
                    
                </div>

            </div>

            <div class="copyright">
                <p>&copy copyright - 2024 MySpace</p>
            </div>

        </footer>


    <div id="notification"></div>

    <script src="script.js"></script>
    
</body>
</html>
