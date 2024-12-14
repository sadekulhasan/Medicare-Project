<?php
require 'database_connection.php';

$productIds = [88, 89, 76, 77, 78, 79, 80, 81];

try {
    $placeholders = rtrim(str_repeat('?,', count($productIds)), ',');
    $sql = "SELECT * FROM medical_products WHERE product_id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image" href="img/short_logo.png">
        <title>Home</title>
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="pharmacy.php" >Pharmacy</a></li>
                    <li><a href="blood-bank.php">Blood Bank</a></li>
                    <li><a href="ambulance.php">Ambulance</a></li>
                    <li><a href="about.php">About</a></li>
                    <!-- <li><a href="cart.html"><i class="fa fa-cart-plus" aria-hidden="true"></i> -->
                    <li id="lg-bag"><a href="cart.php"><i class="fas fa-cart-shopping"></i></a></li>
                    <a href="#" id="close"><i class="fas fa-xmark"></i></a>
                </ul>
            </div>

            <div id="mobile">
                <a href="cart.php"><i class="fas fa-cart-shopping"></i></a>
                <i id="bar" class="fas fa-outdent"></i>
            </div>

        </section>

        <section id="hero">

            <div id="inter_section">
                
                <h2>Welcome to our medical service platform, <snap id="medi">Medi</snap><snap id="care">care</snap></h2>
                <p>Try our medicine service platform and allow us to serve you according to your problems and needs.
                    We provide fastest delivery possible. So, what are you waiting for? Explore and order the product you want. </p>
                <button><a href="pharmacy.php">Shop now</a></button>

            </div>

        </section>

        <section id="feature" class="section-p1">

            <div class="fe-box">
                <img src="img/features/f1.png" alt="fast delivary">
                <h6>Fast Delivary</h6>
            </div>
            <div class="fe-box">
                <img src="img/features/f2.png" alt="online order">
                <h6>Online Order</h6>
            </div>
            <div class="fe-box">
                <img src="img/features/f3.png" alt="blood bank">
                <h6>Blood Bank</h6>
            </div>
            <div class="fe-box">
                <img src="img/features/f4.png" alt="ambulance">
                <h6>Ambulance</h6>
            </div>
            <div class="fe-box">
                <img src="img/features/f5.png" alt="telimedicine">
                <h6>Telimedicine</h6>
            </div>
            <div class="fe-box">
                <img src="img/features/f6.png" alt="all-time-support">
                <h6>24/7 Support</h6>
            </div>

        </section>

        <section id="product1" class="section-p1">

            <h2>Featured Products</h2>
            <p>These are the products that everyone should keep in</p>

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

        <section id="sm-banner" class="section-p1">

            <div class="banner-box">
                
                <h4>New Feature</h4>
                <h2>Ambulance Service <strong>24/7</strong></h2>
                <span>Call for ambulance from anywhere and at anytime</span>
                <button class="small-banner-button"><a href="ambulance.php">Learn More</a></button>

            </div>

            <div class="banner-box banner-box-2">
                
                <h4>Help Center</h4>
                <h2>Blood Bank</h2>
                <span>Seach for blood donor with the help of our data</span>
                <button class="small-banner-button"><a href="blood-bank.php">Learn More</a></button>

            </div>

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
                        <!-- <i class="fas fa-square-facebook"></i>
                        <i class="fas fa-instagram"></i>
                        <i class="fas fa-youtube"></i> -->
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
                <!-- <a href="legal-information.php">Contact Us</a> -->
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