<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=medicare", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if (isset($_POST['update_cart'])) {
    foreach ($_POST['cart_items'] as $cart_id => $quantity) {
        $cart_id = intval($cart_id);
        $quantity = intval($quantity);

        if ($quantity > 0) {
            $update_query = "UPDATE cart SET product_quantity = :quantity WHERE cart_id = :cart_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute(['quantity' => $quantity, 'cart_id' => $cart_id]);
        }
    }

    header("Location: cart.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['cart_id'])) {
    $cart_id = intval($_GET['cart_id']);
    $delete_query = "DELETE FROM cart WHERE cart_id = :cart_id";
    $delete_stmt = $pdo->prepare($delete_query);
    $delete_stmt->execute(['cart_id' => $cart_id]);


    header("Location: cart.php");
    exit();
}

$query = "SELECT * FROM cart";
$stmt = $pdo->query($query);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_bill = 0;
foreach ($cart_items as $row) {
    $total_bill += $row['product_price'] * $row['product_quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image" href="img/short_logo.png">
        <title>Cart</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=43">
    </head>

    <body>

        <section id="header">

            <a href="index.php" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pharmacy.php">Pharmacy</a></li>
                    <li><a href="blood-bank.php">Blood Bank</a></li>
                    <li><a href="ambulance.php">Ambulance</a></li>
                    <li><a href="about.php">About</a></li>
                    <li id="lg-bag"><a href="cart.php" class="active"><i class="fas fa-cart-shopping"></i></a></li>
                    <a href="#" id="close"><i class="fas fa-xmark"></i></a>
                </ul>
            </div>

            <div id="mobile">
                <a href="cart.php"><i class="fas fa-cart-shopping"></i></a>
                <i id="bar" class="fas fa-outdent"></i>
            </div>

        </section>

        <section id="about-header" >

            <h2>#Cart</h2>
            <p>Our vow is to serve people <strong>Anywhere/Anytime</strong></p>

        </section>

        <section class="cart">

            <?php if (empty($cart_items)): ?>
                <p class="empty-cart-message">Your cart is empty.</p>
            <?php else: ?>
                <form method="post" action="cart.php">
                    <div class="cart-container">
                        <?php foreach ($cart_items as $row): ?>
                            <div class="cart-item">

                                
                                <div class="cart-item-details">
                                    <strong><?= htmlspecialchars($row['product_name']) ?></strong>
                                    <span>Unit Price: <?= htmlspecialchars($row['product_price']) ?><span class="taka">৳</span></span>
                                </div>
                                
                                
                                <div class="cart-item-quantity">
                                    <label for="quantity_<?= $row['cart_id'] ?>">Quantity:</label>
                                    <input type="number" id="quantity_<?= $row['cart_id'] ?>" name="cart_items[<?= $row['cart_id'] ?>]" value="<?= $row['product_quantity'] ?>" min="1">
                                </div>
                                
                                
                                <div class="cart-item-total">
                                    <span>$<?= $row['product_price'] * $row['product_quantity'] ?> <span class="taka">৳</span></span>
                                </div>

                                 
                                 <div class="cart-item-actions">
                                    <a href="cart.php?action=remove&cart_id=<?= $row['cart_id'] ?>" class="remove-btn"><i class="fas fa-trash"></i></a>
                                </div>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="update_cart">Update Cart</button>
                </form>

                
                <div class="total-bill">
                    Total Bill: <?= number_format($total_bill, 2) ?> <span class="taka">৳</span>
                </div><br>

            <?php endif; ?>

            <a href="customer_login.php" class="proceed">Proceed to Checkout</a>

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

        <script src="script.js"></script>
    </body>

</html>