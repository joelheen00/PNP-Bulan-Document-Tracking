<!DOCTYPE html>
<html>
<head>
    <title>Register - PNP Doc Tracking</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <img src="assets/img/pnp_logo.png" class="logo" alt="PNP Logo">
        <h2>Register</h2>
        <form method="POST" action="api/register.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br>
            <button type="submit">Register</button>
        </form>
        <p><a href="index.php">Back to Login</a></p>
    </div>
</body>
</html>
