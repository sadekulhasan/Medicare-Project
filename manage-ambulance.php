<?php
require 'database_connection.php';

session_start();

// Handle adding blood donor data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ambulance'])) {
    // Check for duplicate form submissions using session token
    if (isset($_SESSION['last_token']) && $_SESSION['last_token'] === $_POST['form_token']) {
        die("Duplicate submission detected!");
    }

    // Store the current token to avoid duplicates
    $_SESSION['last_token'] = $_POST['form_token']; 

    try {
        $driverName = $_POST['driver_name'];
        $driverPhoneNumber = $_POST['driver_phone_number'];
        $driverLicenseNumber = $_POST['driver_license_number'];
        $ambulanceModel = $_POST['ambulance_model'];
        $belongingHospitalName = $_POST['belonging_hospital_name'];
        $hospitalLocation = $_POST['hospital_location'];
        $district = $_POST['district'];
        $division = $_POST['division'];
        
        

        if (empty($driverName) || empty($driverPhoneNumber) || empty($driverLicenseNumber) || empty($ambulanceModel) || empty($belongingHospitalName) || empty($hospitalLocation) || empty($district) || empty($division)) {
            die("Error: All fields are required!");
        }

        // Insert donor data into the database
        $stmt = $pdo->prepare("INSERT INTO ambulance_service (driver_name, driver_phone_number, driver_license_number, ambulance_model, belonging_hospital_name, hospital_location, district, division) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$driverName, $driverPhoneNumber, $driverLicenseNumber, $ambulanceModel, $belongingHospitalName, $hospitalLocation, $district, $division]);

        // After successful insertion, set a session message and redirect to avoid double submission
        // $successMessage = "Donor added successfully!";
        $_SESSION['ambulance_message'] = "Ambulance added successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $driverNameSearch = $_POST['search_driver_name'];
        $driverPhoneNumberSearch = $_POST['search_driver_phone_number'];
        $driverLicenseNumberSearch = $_POST['search_driver_license_number'];
        $belongingHospitalNameSearch = $_POST['search_belonging_hospital_name'];

        if (isset($_POST['search_ambulance'])) {
            
            $stmt = $pdo->prepare(
                "SELECT * FROM ambulance_service WHERE driver_name = ? AND driver_phone_number = ? AND driver_license_number = ? AND belonging_hospital_name = ?"
            );
            $stmt->execute([$driverNameSearch, $driverPhoneNumberSearch, $driverLicenseNumberSearch, $belongingHospitalNameSearch]);
            $ambulance = $stmt->fetch();

            if ($ambulance) {
                $searchResult = "<p style='color: green;'>Ambulance Found:</p><br>
                <ul>
                    <li><strong>Name: </strong> {$ambulance['driver_name']}</li><br>
                    <li><strong>Phone Number: </strong> {$ambulance['driver_phone_number']}</li><br>
                    <li><strong>License Number: </strong> {$ambulance['driver_license_number']}</li><br>
                    <li><strong>Hospital Location: </strong> {$ambulance['belonging_hospital_name']}</li>
                </ul>";
            } else {
                $searchResult = "<p style='color: red;'>No ambulance found matching the criteria.</p>";
            }
        } elseif (isset($_POST['remove_ambulance'])) {
            
            $stmt = $pdo->prepare(
                "DELETE FROM ambulance_service WHERE driver_name = ? AND driver_phone_number = ? AND driver_license_number = ? AND belonging_hospital_name = ?"
            );
            $stmt->execute([$driverNameSearch, $driverPhoneNumberSearch, $driverLicenseNumberSearch, $belongingHospitalNameSearch]);

            if ($stmt->rowCount() > 0) {
                $ambulanceRemoveMessage = "Ambulance removed successfully!";
            } else {
                $ambulanceErrorMessage = "No ambulance found to remove";
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
        <title>Ambulance Service Management</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="style.css?v=41">
    </head>

    <body>

        <section id="header">

            <a href="#" id="mobile-version"><img src="img/logo.png" class="logo" alt="medicare-logo"></a>

            <div>
                <ul id="navbar">
                    <li><a href="admindashboard.php">Products</a></li>
                    <li><a href="manage-bank.php">Blood Bank</a></li>
                    <li><a href="manage-ambulance.php" class="active">Ambulance</a></li>
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

        <div class="blood-management ambulance-management">
            <!-- Upload Product Section -->
            <div class="form-container">
                <h2>Add Ambulance</h2>
                <?php if (isset($_SESSION['ambulance_message'])) echo "<p class='success-message'>{$_SESSION['ambulance_message']}</p>";
                unset($_SESSION['ambulance_message']) ?>
                <form method="POST">
                    <input type="hidden" name="form_token" value="<?php echo uniqid(); ?>">
                    <label for="driver_name">Driver Name</label>
                    <input type="text" name="driver_name" placeholder="Enter driver name" required>

                    <label for="driver_phone_number">Phone Number</label>
                    <input type="text" name="driver_phone_number" placeholder="Enter driver phone number" required>

                    <label for="driver_license_number">Driving License Number</label>
                    <input type="text" name="driver_license_number" placeholder="Enter driving license number" required>

                    <label for="ambulance_model">Ambulance Model</label>
                    <input type="text" name="ambulance_model" placeholder="Enter ambulance model" required>

                    <label for="belonging_hospital_name">Belonging Hospital Name</label>
                    <input type="text" name="belonging_hospital_name" placeholder="Enter hospital name" required>

                    <label for="hospital_location">Hospital Location (City)</label>
                    <input type="text" name="hospital_location" placeholder="Enter hospital location" required>

                    <label for="district">District</label>
                    <input type="text" name="district" placeholder="Enter district" required>

                    <label for="division">Division</label>
                    <input type="text" name="division" placeholder="Enter division" required>

                    <button type="submit" name="add_ambulance" id="submit-button">Add Ambulance</button>
                </form>
            </div>

            <div class="form-container">
                <h2>Search & Remove Ambulance</h2>
                <?php if (isset($ambulanceRemoveMessage)) echo "<p class='success-message'>$ambulanceRemoveMessage</p>"; ?>
                <?php if (isset($ambulanceErrorMessage)) echo "<p class='error-message'>$ambulanceErrorMessage</p>"; ?>
                <form method="POST" action="">
                    <label for="search_driver_name">Driver Name</label>
                    <input type="text" name="search_driver_name" placeholder="Enter driver name" required>

                    <label for="search_driver_phone_number">Driver Phone Number</label>
                    <input type="text" name="search_driver_phone_number" placeholder="Enter driver phone number" required>

                    <label for="search_driver_license_number">Driving License Number</label>
                    <input type="text" name="search_driver_license_number" placeholder="Enter driving license number" required>

                    <label for="search_belonging_hospital_name">Belonging Hospital Name</label>
                    <input type="text" name="search_belonging_hospital_name" placeholder="Enter hospital name" required>

                    <button type="submit" name="search_ambulance" id="search-button">Search</button>
                    <button type="submit" name="remove_ambulance" id="remove-button">Remove</button>
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