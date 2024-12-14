<?php
require 'database_connection.php';

session_start();

// Handle adding blood donor data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donor'])) {
    // Check for duplicate form submissions using session token
    if (isset($_SESSION['last_token']) && $_SESSION['last_token'] === $_POST['form_token']) {
        die("Duplicate submission detected!");
    }

    // Store the current token to avoid duplicates
    $_SESSION['last_token'] = $_POST['form_token']; 

    try {
        $donorName = $_POST['donor_name'];
        $donorBloodGroup = $_POST['donor_blood_group'];
        $donorPhoneNumber = $_POST['donor_phone_number'];
        $donorDivision = $_POST['donor_division'];
        $donorDistrict = $_POST['donor_district'];
        $donorCity = $_POST['donor_city'];

        if (empty($donorName) || empty($donorBloodGroup) || empty($donorPhoneNumber)) {
            die("Error: All fields are required!");
        }

        // Insert donor data into the database
        $stmt = $pdo->prepare("INSERT INTO blood_donor (donor_name, donor_blood_group, donor_phone_number, donor_divison, donor_district, donor_city) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$donorName, $donorBloodGroup, $donorPhoneNumber, $donorDivision, $donorDistrict, $donorCity]);

        // After successful insertion, set a session message and redirect to avoid double submission
        // $successMessage = "Donor added successfully!";
        $_SESSION['donor_message'] = "Donor added successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $donorName = $_POST['search_name'];
        $bloodGroup = $_POST['search_blood_group'];
        $phoneNumber = $_POST['search_phone_number'];
        $city = $_POST['search_city'];

        if (isset($_POST['search_donor'])) {
            // Search for the donor
            $stmt = $pdo->prepare(
                "SELECT * FROM blood_donor WHERE donor_name = ? AND donor_blood_group = ? AND donor_phone_number = ? AND donor_city = ?"
            );
            $stmt->execute([$donorName, $bloodGroup, $phoneNumber, $city]);
            $donor = $stmt->fetch();

            if ($donor) {
                $searchResult = "<p style='color: green;'>Donor Found:</p>
                <ul>
                    <li><strong>Name:</strong> {$donor['donor_name']}</li>
                    <li><strong>Blood Group:</strong> {$donor['donor_blood_group']}</li>
                    <li><strong>Phone Number:</strong> {$donor['donor_phone_number']}</li>
                    <li><strong>City:</strong> {$donor['donor_city']}</li>
                </ul>";
            } else {
                $searchResult = "<p style='color: red;'>No donor found matching the criteria.</p>";
            }
        } elseif (isset($_POST['remove_donor'])) {
            // Remove the donor
            $stmt = $pdo->prepare(
                "DELETE FROM blood_donor WHERE donor_name = ? AND donor_blood_group = ? AND donor_phone_number = ? AND donor_city = ?"
            );
            $stmt->execute([$donorName, $bloodGroup, $phoneNumber, $city]);

            if ($stmt->rowCount() > 0) {
                $removeMessage = "Donor removed successfully!";
            } else {
                $errorMessage = "No donor found to remove";
            }
        }
    } catch (PDOException $e) {
        $searchResult = "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
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
        <title>Blood Bank Management</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=41">
    </head>

    <body>

        <section id="header">

            <a href="#" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="admindashboard.php">Products</a></li>
                    <li><a href="manage-bank.php" class="active">Blood Bank</a></li>
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

        <div class="blood-management">
            <!-- Upload Product Section -->
            <div class="form-container">
                <h2>Add Blood Donor</h2>
                <?php if (isset($_SESSION['donor_message'])) echo "<p class='success-message'>{$_SESSION['donor_message']}</p>";
                unset($_SESSION['donor_message']) ?>
                <form method="POST">
                    <input type="hidden" name="form_token" value="<?php echo uniqid(); ?>">
                    <label for="donor_name">Donor Name</label>
                    <input type="text" name="donor_name" placeholder="Enter donor name" required>

                    <label for="donor_blood_group">Blood Group</label>
                    <select name="donor_blood_group" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>

                    <label for="donor_phone_number">Phone Number</label>
                    <input type="text" name="donor_phone_number" placeholder="Enter donor phone number" required>

                    <label for="donor_division">Division</label>
                    <input type="text" name="donor_division" placeholder="Enter donor division" required>

                    <label for="donor_district">District</label>
                    <input type="text" name="donor_district" placeholder="Enter donor district" required>

                    <label for="donor_city">City</label>
                    <input type="text" name="donor_city" placeholder="Enter donor city" required>

                    <button type="submit" name="add_donor" id="submit-button">Add Donor</button>
                </form>
            </div>

            <div class="form-container">
                <h2>Search & Remove Donor</h2>
                <?php if (isset($removeMessage)) echo "<p class='success-message'>$removeMessage</p>"; ?>
                <?php if (isset($errorMessage)) echo "<p class='error-message'>$errorMessage</p>"; ?>
                <form method="POST" action="">
                    <label for="search_name">Donor Name</label>
                    <input type="text" name="search_name" placeholder="Enter donor name" required>

                    <label for="search_blood_group">Blood Group</label>
                    <select name="search_blood_group" required>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>

                    <label for="search_phone_number">Donor Phone Number</label>
                    <input type="text" name="search_phone_number" placeholder="Enter donor phone number" required>

                    <label for="search_city">City</label>
                    <input type="text" name="search_city" placeholder="Enter donor city" required>

                    <button type="submit" name="search_donor" id="search-button">Search</button>
                    <button type="submit" name="remove_donor" id="remove-button">Remove</button>
                </form>

                <div id="search-results">
                    <?php if (!empty($searchResult)) echo $searchResult; ?>
                </div>

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

        <script>
            // Show notification when donor is added successfully
            window.onload = function() {
                var notification = document.getElementById("notification");
                if (notification) {
                    notification.style.display = "block";
                    setTimeout(function() {
                        notification.style.display = "none";
                    }, 5000); // Hide notification after 5 seconds
                }
            };
        </script>
    </body>

</html>