<?php
session_start();
include("../config.php");

// ✅ Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please <a href='../index.php'>login</a>.");
}

// Fetch logs with document & user info
$sql = "SELECT logs.id, logs.action, logs.action_time, users.username, files.filename 
        FROM logs 
        JOIN users ON logs.user_id = users.id 
        JOIN files ON logs.document_id = files.id 
        ORDER BY logs.action_time DESC";
$result = $conn->query($sql);

// ✅ Get user role for sidebar
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - PNP Doc Tracking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- ✅ Load Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Same dashboard layout as track.php */
        body {
            font-family: Arial, sans-serif;
            background: #f5faff;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #007bff;
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
        }
        .sidebar img.logo {
            display: block;
            width: 90px;
            height: auto;
            margin: 0 auto 10px;
        }
        .sidebar-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 15px;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background: #0056b3;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        p {
            text-align: center;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="../assets/img/pnp_logo.png" class="logo" alt="PNP Logo">
    <h2 class="sidebar-title">PNP Dashboard</h2>
    <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="upload.php"><i class="fas fa-upload"></i> Upload Document</a>
    <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="track.php"><i class="fas fa-search"></i> Track Document</a>
    <?php if($role === 'admin') { ?>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <?php } ?>
    <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h2><i class="fas fa-chart-line"></i> Access Reports</h2>
        <?php if ($result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>#</th>
                    <th>Document</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Time</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['filename']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo $row['action_time']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No reports available yet.</p>
        <?php } ?>
    </div>
</div>

</body>
</html>
