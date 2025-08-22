<?php
session_start();

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - PNP Doc Tracking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- ✅ Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #d0e7ff; /* light blue */
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 220px;
            background-color: #007bff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding-top: 20px;
            color: white;
        }
        .sidebar .logo {
            width: 80px;
            display: block;
            margin: 0 auto 10px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 15px;
        }
        .sidebar a i {
            margin-right: 10px;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        h1, h3 {
            color: #003366;
        }
        h2 {
            color: white;
        }
        .welcome-box {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .role-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #ffc107;
            color: #000;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="../assets/img/pnp_logo.png" class="logo" alt="PNP Logo">
    <h2>PNP Dashboard</h2>
    <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="upload.php"><i class="fas fa-upload"></i> Upload Document</a>
    <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="track.php"><i class="fas fa-search"></i> Track Document</a>
    <?php if($role === 'admin') { ?>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <?php } ?>
    <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="welcome-box">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Role: <span class="role-badge"><?php echo htmlspecialchars($role); ?></span></p>
        <p>This dashboard allows you to securely upload, track, and generate reports for documents within the PNP system.</p>
    </div>

    <h3>Quick Access</h3>
    <ul>
        <li><a href="upload.php"><i class="fas fa-upload"></i> Upload new documents</a></li>
        <li><a href="reports.php"><i class="fas fa-chart-bar"></i> View reports</a></li>
        <li><a href="track.php"><i class="fas fa-search"></i> Track documents</a></li>
        <?php if($role === 'admin') { ?>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage users</a></li>
        <?php } ?>
    </ul>
</div>

</body>
</html>
