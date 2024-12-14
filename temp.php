<?php

require 'database_connection.php';

// Initialize variables
$cartItems = [];
$totalPrice = 0;
$message = "";

// Fetch cart items
try {
    $stmt = $pdo->query("SELECT product_name, product_price, product_quantity FROM cart");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cartItems[] = $row;
        $totalPrice += $row['product_price'] * $row['product_quantity'];
    }
} catch (PDOException $e) {
    $message = "Error fetching cart items: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['name'];
    $customerPhone = $_POST['phone'];
    $customerEmail = $_POST['email'];
    $customerAddress = $_POST['address'];

    // Prepare product names for insertion
    $productNames = implode(', ', array_column($cartItems, 'product_name'));

    try {
        // Insert checkout data
        $stmt = $pdo->prepare(
            "INSERT INTO checkout (customer_name, customer_phone, customer_email, customer_address, product_names, total_price, status) 
             VALUES (:customer_name, :customer_phone, :customer_email, :customer_address, :product_names, :total_price, :status)"
        );
        $stmt->execute([
            ':customer_name' => $customerName,
            ':customer_phone' => $customerPhone,
            ':customer_email' => $customerEmail,
            ':customer_address' => $customerAddress,
            ':product_names' => $productNames,
            ':total_price' => $totalPrice,
            ':status' => 'Pending',
        ]);
        $message = "Checkout completed successfully!";
    } catch (PDOException $e) {
        $message = "Error during checkout: " . $e->getMessage();
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
        <title>Checkout</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=42">

        <!-- <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-color: #f9f9f9;
        }
        h1, h2, h3 {
            text-align: center;
        }
        .container {
            text-align: center;
            width: 90%;
            max-width: 600px;
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            margin: 0 auto;
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            text-align: center;
        }
        .form-group input, .form-group textarea {
            width: 80%;
            padding: 8px;
            margin: 0 auto;
            display: block;
            text-align: center;
        }
        .submit-btn {
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .message {
            margin-bottom: 20px;
            color: #28a745;
            font-weight: bold;
            text-align: center;
        }
    </style> -->

    </head>

    <body>

        <section id="header">

            <a href="index.php" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="index.php">Home</a></li>
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

        <section class="checkout-function">

            <div class="container">

                <!-- Display Message -->
                <?php if (!empty($message)): ?>
                    <div class="message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <!-- Cart Section -->
                <div class="cart-section">
                    <h2>Your Cart</h2>
                    <div class="cart-table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cartItems)): ?>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td><?= htmlspecialchars($item['product_price']) ?> <span class="taka">৳</span></td>
                                            <td><?= htmlspecialchars($item['product_quantity']) ?></td>
                                            <td><?= htmlspecialchars($item['product_price'] * $item['product_quantity']) ?> <span class="taka">৳</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Your cart is empty.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <h3>Total Price: <?= htmlspecialchars($totalPrice) ?> <span class="taka">৳</span></h3>
                </div>

                <!-- Checkout Form -->
                <div class="checkout-form">
                    <h2>Customer Details</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Complete Checkout</button>

                        <!-- <button type="submit" class="submit-btn" onclick="showNotificationAndRedirect(event)">Complete Checkout</button> -->

                    </form>
                </div>
            </div>

        </section>

        <footer class="section-p1">
            
            <div class="col">

                <img class="logo" src="img/short_logo.png" alt="">
                <h4>Contact</h4>
                <p><strong>Address: </strong>Uttara Sector 10, Dhaka, Bangladesh</p>
                <p><strong>Phone: </strong>+88019266659041, +88018881117230</p>
                
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

        </footer>

        <script src="script.js"></script>

        <!-- <script>
            function showNotificationAndRedirect(event) {
                // Prevent the default form submission behavior
                event.preventDefault();

                // Show a notification (you can customize this further)
                alert("Checkout completed successfully! Your cart is now empty. Redirecting to Pharmacy.");

                // Redirect to pharmacy.php after a short delay
                setTimeout(function() {
                    // Redirect to pharmacy.php
                    window.location.href = 'pharmacy.php';
                }, 2000); // Redirect after 2 seconds
            }
        </script> -->


    </body>

</html>