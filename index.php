<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - PNP Doc Tracking</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <img src="assets/img/pnp_logo.png" class="logo" alt="PNP Logo">
        <h2>Login</h2>

        <!-- Login form -->
        <form method="POST" action="api/login.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <!-- Corrected Register link (always goes to root register.php) -->
        <p><a href="register.php">Register</a></p>
    </div>
</body>
</html>
