<?php
require 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_name'], $_FILES['product_image'])) {
        $productName = $_POST['product_name'];
        $productBrand = $_POST['product_brand'];
        $productCategory = $_POST['product_category'];
        $productPrice = $_POST['product_price'];

        $image = $_FILES['product_image'];
        $imagePath = '';

        if ($image['error'] === UPLOAD_ERR_OK) {
            $imageName = uniqid() . '_' . $image['name'];
            $imagePath = 'uploads/' . $imageName;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                
                $stmt = $pdo->prepare("INSERT INTO medical_products (product_name, product_brand, product_category, product_image, product_price) 
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$productName, $productBrand, $productCategory, $imagePath, $productPrice]);

                $successMessage = "Product added successfully!";
            } else {
                $errorMessage = "Failed to upload image. Please try again.";
            }
        } else {
            $errorMessage = "No image uploaded or an error occurred during upload.";
        }
    }

    
    if (isset($_POST['remove_product'])) {
        $productId = $_POST['product_id'];

        if (!empty($productId)) {
            $stmt = $pdo->prepare("DELETE FROM medical_products WHERE product_id = ?");
            $stmt->execute([$productId]);
            $removeMessage = "Product removed successfully!";
        } else {
            $removeMessage = "Please provide a valid product ID.";
        }
    }

    if (isset($_POST['search_product'])) {
        $productId = $_POST['product_id'];
        $productName = $_POST['product_name'];
        $productCategory = $_POST['product_category'];

        $stmt = $pdo->prepare("SELECT * FROM medical_products WHERE product_id = ? OR product_name = ? OR product_category = ?");
        $stmt->execute([$productId, $productName, $productCategory]);

        $productDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image" href="img/short_logo.png">
        <title>Product Management</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=48">
    </head>

    <body>

        <section id="header">

            <a href="admindashboard.php" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="admindashboard.php" class="active">Products</a></li>
                    <li><a href="manage-bank.php" >Blood Bank</a></li>
                    <li><a href="manage-ambulance.php">Ambulance</a></li>
                    <li><a href="manage-checkout.php">Checkout</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                    <!-- <li><a href="cart.html"><i class="fa fa-cart-plus" aria-hidden="true"></i> -->
                    <!-- <li id="lg-bag"><a href="cart.php"><i class="fas fa-cart-shopping"></i></a></li> -->
                    <a href="#" id="close"><i class="fas fa-xmark"></i></a>
                </ul>
            </div>

            <div id="mobile">
                <!-- <a href="cart.php"><i class="fas fa-cart-shopping"></i></a> -->
                <i id="bar" class="fas fa-outdent"></i>
            </div>

        </section>

        <div class="product-management">
        <!-- Upload Product Section -->
        <div class="form-container">
            <h2>Add New Product</h2>
            <?php if (isset($successMessage)) echo "<p class='success-message'>$successMessage</p>"; ?>
            <?php if (isset($errorMessage)) echo "<p class='error-message'>$errorMessage</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <label for="product_name">Product Name</label>
                <input type="text" name="product_name" required>

                <label for="product_brand">Product Brand</label>
                <input type="text" name="product_brand" required>

                <label for="product_category">Product Category</label>
                <input type="text" name="product_category" required>

                <label for="product_price">Product Price</label>
                <input type="number" name="product_price" class="error1" required>

                <label for="product_image">Product Image</label>
                <div class="upload-area">
                    <input type="file" name="product_image" accept="image/*" class="error1" required>
                </div>

                <button type="submit">Add Product</button>
            </form>
        </div>

        <!-- Search and Remove Product Section -->
        <div class="form-container">
            <h2>Search & Remove Product</h2>
            <?php if (isset($removeMessage)) echo "<p class='success-message'>$removeMessage</p>"; ?>
            <form method="POST">
                <label for="product_id">Product ID</label>
                <input type="text" name="product_id" placeholder="Search by product ID">

                <label for="product_name">Product Name</label>
                <input type="text" name="product_name" placeholder="Search by product name">

                <label for="product_category">Product Category</label>
                <input type="text" name="product_category" placeholder="Search by product category">

                <button type="submit" name="search_product">Search Product</button>
                <button type="submit" name="remove_product">Remove Product</button>
            </form>

            <!-- Display product details if found -->
            <?php if (isset($productDetails)): ?>
                <div class="product-details">
                    <h3>Product Details</h3>
                    <p><strong>Product ID:</strong> <?php echo $productDetails['product_id']; ?></p>
                    <p><strong>Product Name:</strong> <?php echo $productDetails['product_name']; ?></p>
                    <p><strong>Product Category:</strong> <?php echo $productDetails['product_category']; ?></p>
                    <p><strong>Price:</strong> <?php echo $productDetails['product_price']; ?></p>
                    <img src="<?php echo $productDetails['product_image']; ?>" alt="Product Image">
                </div>
            <?php endif; ?>
        </div>
    </div>

        <!-- <footer class="section-p1">
            
            <div class="col">

                <img class="logo" src="img/short_logo.png" alt="">
                <h4>Contact</h4>
                <p><strong>Address: </strong>Uttara Sector 10, Dhaka, Bangladesh</p>
                <p><strong>Phone: </strong>+88019266659041, +88018881117230</p>
                <div class="follow">
                    <h4>Follow Us</h4>
                    <div class="icon">
                        <i class="fas fa-square-facebook"></i>
                        <i class="fas fa-twitter"></i>
                        <i class="fas fa-youtube"></i>
                    </div>
                </div>
                
            </div>

            <div class="col">

                <h4>About</h4>
                <a href="about.php">About Us</a>
                <a href="legal-information.php">Delivary Information</a>
                <a href="legal-information.php">Privacy Policy</a>
                <a href="legal-information.php">Terms and Conditions</a>
                <a href="legal-information.php">Contact Us</a>

            </div>

            <div class="col">

                <h4>My Account</h4>
                <a href="#">Sign In</a>
                <a href="cart.php">View Cart</a>
                <a href="#">My Wishlist</a>
                <a href="#">Track My Order</a>
                <a href="#">Help</a>

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
                <p>copyright - 2023 MySpace</p>
            </div>

        </footer> -->

        <script src="script.js"></script>
    </body>

</html>