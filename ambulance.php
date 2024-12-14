<?php 

include('database_connection.php');

$totalAmbulances = 0;
$tableRows = '';
$ambulanceResults = [];
$newEntryUpazila = null;

try {
    
    $sql = "SELECT division, COUNT(*) as ambulance_count FROM ambulance_service GROUP BY division";
    $stmt = $pdo->query($sql);

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $totalAmbulances += $row['ambulance_count'];
            $tableRows .= '<tr><td>' . htmlspecialchars($row['division']) . '</td><td>' . $row['ambulance_count'] . '</td></tr>';
        }
    } else {
        $tableRows = '<tr><td colspan="2">No ambulance data available.</td></tr>';
    }
} catch (PDOException $e) {
    $tableRows = '<tr><td colspan="2">Error fetching ambulance stats: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $nid = $_POST['nid'];
    $pickup = $_POST['pickup'];
    $upazila = $_POST['upazila'];

    $sql = "INSERT INTO request_data (name, phone, nid, pickup, upazila) 
            VALUES (:name, :phone, :nid, :pickup, :upazila)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':nid' => $nid,
            ':pickup' => $pickup,
            ':upazila' => $upazila,
        ]);
        
        $query = "SELECT upazila FROM request_data ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $newEntryUpazila = $stmt->fetchColumn();

        
        $ambulanceQuery = "SELECT driver_name, driver_phone_number, driver_license_number, ambulance_model, belonging_hospital_name 
                           FROM ambulance_service 
                           WHERE hospital_location = :upazila";
        $stmt = $pdo->prepare($ambulanceQuery);
        $stmt->execute([':upazila' => $newEntryUpazila]);
        $ambulanceResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error saving your request: " . $e->getMessage();
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
        <title>Ambulance Service</title>
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
                    <li><a href="ambulance.php" class="active">Ambulance</a></li>
                    <li><a href="about.php">About</a></li>
                    <li id="lg-bag"><a href="cart.php"><i class="fas fa-cart-shopping"></i></a></li>
                    <a href="#" id="close"><i class="fas fa-xmark"></i></a>
                </ul>
            </div>

            <div id="mobile">
                <a href="cart.php"><i class="fas fa-cart-shopping"></i></a>
                <i id="bar" class="fas fa-outdent"></i>
            </div>

        </section>

        <section id="ambulance-header">

            <h2>Ambulance Broker</h2>
            <p>Purchase your necessary medical products <strong>Anywhere/Anytime</strong></p>

        </section>

        <section class="first">

            <div class="stats-section">
                <h2>Registered Ambulance Count</h2>
                <div class="stats-count" id="ambulanceCount"><?php echo $totalAmbulances; ?></div>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Division</th>
                            <th>Ambulance Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $tableRows; ?>
                    </tbody>
                </table>
            </div>

            <div class="container">
                <h1>Ambulance Booking</h1>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">NID Card Number</label>
                        <input type="text" id="nid" name="nid" placeholder="Enter your NID card number" required>
                    </div>
                    <div class="form-group">
                        <label for="pickup">Pickup Location</label>
                        <input type="text" id="pickup" name="pickup" placeholder="Enter the pickup location" required>
                    </div>
                    <div class="form-group">
                        <label for="pickup">Upazila</label>
                        <input type="text" id="upazila" name="upazila" placeholder="Enter the upazila" required>
                    </div>
                    <button type="submit" class="submit-btn">Book Ambulance</button>
                </form>
                <?php if (isset($message)): ?>
                    <p class="message <?= strpos($message, 'Error') !== false ? 'error' : '' ?>">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>
            </div>

        </section>

        <section id="ambulace-search-result">
            <?php if (!empty($ambulanceResults)) : ?>
                <h2>Available Ambulances in <?= htmlspecialchars($newEntryUpazila) ?></h2>
                <table id="ambulace-search-result-table">
                    <thead>
                        <tr>
                            <th>Driver Name</th>
                            <th>Phone Number</th>
                            <th>License Number</th>
                            <th class="ambulance-model">Ambulance Model</th>
                            <th>Hospital Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ambulanceResults as $ambulance) : ?>
                            <tr>
                                <td><?= htmlspecialchars($ambulance['driver_name']) ?></td>
                                <td><?= htmlspecialchars($ambulance['driver_phone_number']) ?></td>
                                <td><?= htmlspecialchars($ambulance['driver_license_number']) ?></td>
                                <td class="ambulance-model"><?= htmlspecialchars($ambulance['ambulance_model']) ?></td>
                                <td><?= htmlspecialchars($ambulance['belonging_hospital_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No ambulances available in <?= htmlspecialchars($newEntryUpazila) ?>.</p>
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

        <script src="script.js"></script>
    </body>

</html>