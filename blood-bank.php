<?php

require 'database_connection.php';
session_start();

try {
    $sql = "SELECT donor_blood_group, COUNT(*) as donor_count 
            FROM blood_donor 
            GROUP BY donor_blood_group";

    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$blood_group = $location = '';
$result = [];
$message = ''; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['search_donor_button'])) {
        $blood_group = $_POST['blood_group'] ?? '';
        $location = $_POST['location'] ?? '';

        
        $query = "
            SELECT donor_name, donor_blood_group, donor_phone_number, donor_divison, donor_district, donor_city 
            FROM blood_donor 
            WHERE donor_blood_group = :blood_group 
            AND (donor_divison LIKE :location OR donor_district LIKE :location OR donor_city LIKE :location)
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':blood_group' => $blood_group,
            ':location' => "%$location%"
        ]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_POST['apply_donor_button'])) {
        
        $donor_name = $_POST['donor_name'] ?? '';
        $donor_blood_group = $_POST['donor_blood_group'] ?? '';
        $donor_phone_number = $_POST['donor_phone_number'] ?? '';
        $donor_division = $_POST['donor_division'] ?? '';
        $donor_district = $_POST['donor_district'] ?? '';
        $donor_city = $_POST['donor_city'] ?? '';

        
        $sql = "INSERT INTO blood_donor (donor_name, donor_blood_group, donor_phone_number, donor_divison, donor_district, donor_city) 
                VALUES (:donor_name, :donor_blood_group, :donor_phone_number, :donor_division, :donor_district, :donor_city)";

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':donor_name' => $donor_name,
                ':donor_blood_group' => $donor_blood_group,
                ':donor_phone_number' => $donor_phone_number,
                ':donor_division' => $donor_division,
                ':donor_district' => $donor_district,
                ':donor_city' => $donor_city
            ]);

            $message = "Thank you for registering as a donor!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }

        
        if (!empty($message)) {
            echo "<script>alert('" . htmlspecialchars($message) . "');</script>";
        }
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
        <title>Pharmacy</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=44">
    </head>

    <body>

        <section id="header">

            <a href="index.php" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pharmacy.php">Pharmacy</a></li>
                    <li><a href="blood-bank.php" class="active">Blood Bank</a></li>
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

        <section id="bank">
        <h2>Virtual Blood Bank</h2>
        <p>Our vow is to serve people <strong>Anywhere/Anytime</strong></p>
    </section>

    <main>
        <div id="donor-search-container">
            <section id="donor-list">
                <h2>Available Donors</h2>
                <?php if (!empty($results)): ?>
                    <table>
                        <tr>
                            <th>Blood Group</th>
                            <th>Number of Donors</th>
                        </tr>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['donor_blood_group']) ?></td>
                                <td><?= htmlspecialchars($row['donor_count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p style="text-align: center;">No data found.</p>
                <?php endif; ?>
            </section>

            <section id="search-donor">
                <h2>Search for a Blood Donor</h2>
                <form method="POST" action="">
                    <select id="blood_group" name="blood_group" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                    <input type="text" id="location" name="location" placeholder="City or District" required>
                    <button type="submit" id="search-donor-button" name="search_donor_button">Search</button>
                </form>

                <?php if (!empty($result)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $data): ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['donor_name']) ?></td>
                                    <td><?= htmlspecialchars($data['donor_phone_number']) ?></td>
                                    <td><?= htmlspecialchars($data['donor_divison']) ?>, <?= htmlspecialchars($data['donor_district']) ?>, <?= htmlspecialchars($data['donor_city']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_donor_button'])): ?>
                    <p>No results found for the given criteria.</p>
                <?php endif; ?>
            </section>
        </div>

        <section id="apply-donor">
            <h2>Apply as a Blood Donor</h2>
            <form action="" method="post">
                <input type="text" id="donor_name" name="donor_name" placeholder="Your name" required><br>
                <select id="donor_blood_group" name="donor_blood_group" required>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select><br>
                <input type="text" id="donor_phone_number" name="donor_phone_number" placeholder="Your phone number" required><br>
                <input type="text" id="donor_division" name="donor_division" placeholder="Your division" required><br>
                <input type="text" id="donor_district" name="donor_district" placeholder="Your district" required><br>
                <input type="text" id="donor_city" name="donor_city" placeholder="Your city" required><br>
                <button type="submit" id="apply-donor-button" name="apply_donor_button">Apply</button>
            </form>
        </section>
    </main>

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