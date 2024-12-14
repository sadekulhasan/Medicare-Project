<?php
require 'database_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: admindashboard.php');
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=48">
    <link rel="shortcut icon" type="image" href="img/short_logo.png">
    <title>Admin Login</title>

</head>
<body>
    
    <section class="holder">

    <div id="login-container">
            <h2 id="login-header">Welcome to <span style="color: #0caa0c">Medi</span><span style="color: #FF8080">care</span> Admin Panel</h2>
            <h4>Login</h4>
            <form id="login-form" method="POST">
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
                
                <label for="password">Password</label>
                <div id="password-container">
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <button type="button" id="toggle-password" style="font-size: 25px;" aria-label="Toggle password visibility">🔒</button>
                </div>
                
                <?php if (isset($error)) echo "<p id='login-error'>$error</p>"; ?>
                <button type="submit" id="login-button">Login</button>
            </form>

            <div id="register-container">
                <p>Don't have an account? <a href="#">Register</a></p>
            </div>
    </div>

    </section>
        

    <script>
        const togglePassword = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            togglePassword.textContent = type === 'password' ? '🔒' : '🔓';
        });
    </script>
</body>
</html>
