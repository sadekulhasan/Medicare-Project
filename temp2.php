<?php
require 'database_connection.php'; // Include database connection

// Handle the status update
if (isset($_GET['update_status']) && isset($_GET['checkout_id'])) {
    $newStatus = $_GET['update_status'];
    $checkoutId = $_GET['checkout_id'];

    try {
        // Update the status in the checkout table
        $stmt = $pdo->prepare("UPDATE checkout SET status = :status WHERE checkout_id = :checkout_id");
        $stmt->execute([
            ':status' => $newStatus,
            ':checkout_id' => $checkoutId
        ]);

        // Fetch customer email to send the completion email if the status is 'Completed'
        if ($newStatus == 'Completed') {
            $stmt = $pdo->prepare("SELECT customer_email FROM checkout WHERE checkout_id = :checkout_id");
            $stmt->execute([':checkout_id' => $checkoutId]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                // Send the completion email to the customer
                $to = $customer['customer_email'];
                $subject = "Order Completion Notification";
                $message = "Dear Customer,\n\nYour order has been marked as completed. Thank you for shopping with us.\n\nBest Regards,\nMedicare Team";
                $headers = "From: no-reply@medicare.com";

                // Send the email
                if (mail($to, $subject, $message, $headers)) {
                    $message = "Checkout status updated to Completed and email sent successfully!";
                } else {
                    $message = "Checkout status updated to Completed, but email sending failed.";
                }
            }
        }

        // Redirect to the same page after update to refresh the status
        header('Location: manage-checkout.php');
        exit;
    } catch (PDOException $e) {
        $message = "Error updating status: " . $e->getMessage();
    }
}

// Fetch all checkout records
try {
    $stmt = $pdo->query("SELECT * FROM checkout");
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
    <title>Admin - Checkout Management</title>
    <link rel="stylesheet" href="style.css?v=44">
</head>

<body>

    <section id="header">
        <a href="#" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>
        <div>
            <ul id="navbar">
                <li><a href="admindashboard.php">Products</a></li>
                <li><a href="manage-bank.php">Blood Bank</a></li>
                <li><a href="manage-ambulance.php">Ambulance</a></li>
                <li><a href="manage-checkout.php" class="active">Checkout</a></li>
                <li><a href="logout.php">Log Out</a></li>
                <a href="#" id="close"><i class="fas fa-xmark"></i></a>
            </ul>
        </div>
        <div id="mobile">
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
                        <th>Total Price</th>
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
                            <td><?= htmlspecialchars($checkout['total_price']) ?> ৳</td>
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

    <script src="script.js"></script>

</body>

</html>

<?

require 'database_connection.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer if using Composer

if (isset($_GET['update_status']) && isset($_GET['checkout_id'])) {
    $newStatus = $_GET['update_status'];
    $checkoutId = $_GET['checkout_id'];

    try {
        
        $stmt = $pdo->prepare("UPDATE checkout SET status = :status WHERE checkout_id = :checkout_id");
        $stmt->execute([
            ':status' => $newStatus,
            ':checkout_id' => $checkoutId
        ]);

        // START MODIFICATION
        if ($newStatus == 'Completed') {
            $stmt = $pdo->prepare("SELECT customer_email FROM checkout WHERE checkout_id = :checkout_id");
            $stmt->execute([':checkout_id' => $checkoutId]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                $to = $customer['customer_email'];
                $subject = "Order Completion Notification";
                $message = "Dear Customer,\n\nYour order has been marked as completed. Thank you for shopping with us.\n\nBest Regards,\nMedicare Team";
                $headers = "From: no-reply@medicare.com";

                // Use PHPMailer for reliable email delivery
                $mail = new PHPMailer(true);
                try {
                    // SMTP Configuration
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'medicare.org.bd@gmail.com'; // Sender's email
                    $mail->Password = 'your-email-password'; // App password for Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Email Settings
                    $mail->setFrom('medicare.org.bd@gmail.com', 'Medicare');
                    $mail->addAddress($to);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    // Send the email
                    $mail->send();
                    $message = "Checkout status updated to Completed and email sent successfully!";
                } catch (Exception $e) {
                    $message = "Checkout status updated to Completed, but email sending failed. Error: " . $mail->ErrorInfo;
                }
            }
        }
        // END MODIFICATION

        header('Location: manage-checkout.php');
        exit;
    } catch (PDOException $e) {
        $message = "Error updating status: " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM checkout");
    $checkouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching checkout records: " . $e->getMessage();
}
?>
