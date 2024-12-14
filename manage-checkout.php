<?php

require 'database_connection.php'; 


if (isset($_GET['update_status']) && isset($_GET['checkout_id'])) {
    $newStatus = $_GET['update_status'];
    $checkoutId = $_GET['checkout_id'];

    try {
        
        $stmt = $pdo->prepare("UPDATE checkout SET status = :status WHERE checkout_id = :checkout_id");
        $stmt->execute([
            ':status' => $newStatus,
            ':checkout_id' => $checkoutId
        ]);

        
        if ($newStatus == 'Completed') {
            $stmt = $pdo->prepare("SELECT customer_email FROM checkout WHERE checkout_id = :checkout_id");
            $stmt->execute([':checkout_id' => $checkoutId]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                
                $to = $customer['customer_email'];
                $subject = "Order Completion Notification";
                $message = "Dear Customer,\n\nYour order has been marked as completed. Thank you for shopping with us.\n\nBest Regards,\nMedicare Team";
                $headers = "From: no-reply@medicare.com";

                
                if (mail($to, $subject, $message, $headers)) {
                    $message = "Checkout status updated to Completed and email sent successfully!";
                } else {
                    $message = "Checkout status updated to Completed, but email sending failed.";
                }
            }
        }

        header('Location: manage-checkout.php');
        exit;
    } catch (PDOException $e) {
        $message = "Error updating status: " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM checkout ORDER BY checkout_id DESC");
    $checkouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching checkout records: " . $e->getMessage();
}

?>


<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image" href="img/short_logo.png">
        <title>Manage Checkout</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=26">
    </head>

    <body>

        <section id="header">

            <a href="index.php" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="admindashboard.php">Products</a></li>
                    <li><a href="manage-bank.php">Blood Bank</a></li>
                    <li><a href="manage-ambulance.php">Ambulance</a></li>
                    <li><a href="manage-checkout.php" class="active">Checkout</a></li>
                    <li><a href="logout.php">Logout</a></li>
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

        <div class="checkout-container">

            <!-- Display success or error message -->
            <?php if (!empty($message)): ?>
                <div class="message <?= isset($newStatus) ? 'success' : '' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <h2>Checkout Management</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Checkout ID</th>
                            <th>Customer Details</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th class="extend">Total Price<br>[ <span id="total_taka">৳</span> ]</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkouts as $checkout): ?>
                            <tr>
                                <td><?= htmlspecialchars($checkout['checkout_id']) ?></td>
                                <td>
                                    <?= htmlspecialchars($checkout['customer_name']) ?><br>
                                    <?= htmlspecialchars($checkout['customer_phone']) ?><br>
                                    <?= htmlspecialchars($checkout['customer_email']) ?>
                                </td>
                                <td><?= htmlspecialchars($checkout['customer_address']) ?></td>
                                <td><?= htmlspecialchars($checkout['product_names']) ?></td>
                                <td class="extend"><?= htmlspecialchars($checkout['total_price']) ?></td>
                                <td><?= htmlspecialchars($checkout['status']) ?></td>
                                <td>
                                    <form action="manage-checkout.php" method="get">
                                        <input type="hidden" name="checkout_id" value="<?= $checkout['checkout_id'] ?>">
                                        <select name="update_status" class="status-select">
                                            <option value="Completed" <?= $checkout['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Canceled" <?= $checkout['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                                        </select>
                                        <button type="submit" class="status-button">Update Status</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                        <i class="fas fa-facebook"></i>
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