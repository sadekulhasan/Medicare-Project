<?php
require 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['customer_user_name'];
    $password = password_hash($_POST['customer_password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO customer (customer_user_name, customer_password) VALUES (?, ?)");
    try {
        $stmt->execute([$username, $password]);
        header('Location: customer_login.php');
        exit;
    } catch (PDOException $e) {
        $error = "Username already exists. Please log in instead.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image" href="img/short_logo.png">
    <link rel="stylesheet" href="style.css?v=41">
    <title>Customer Register</title>
</head>
<body>
    
    <section class="holder">

        <div class="form-container">
            <h4>Register</h4>
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" name="customer_user_name" placeholder="Enter your username" required>
                
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="text" name="customer_password" id="password" placeholder="Enter your password" minlength="8" required>
                    <!-- <button type="button" id="toggle-password" class="toggle-password">🔒</button> -->
                </div>
                
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                
                <button type="submit">Register</button>
            </form>

            <div class="login-container">
                <p>Already have an account? <a href="customer_login.php" class="login-button">Login</a></p>
                
            </div>
        </div>

    </section>

    <script>
        const togglePassword = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            // Toggle password visibility
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle button icon
            togglePassword.textContent = type === 'password' ? '🔒' : '🔓';
        });
    </script>
</body>
</html>