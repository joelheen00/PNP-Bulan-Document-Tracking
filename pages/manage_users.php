<?php
session_start();
include("../config.php");

// ✅ Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. <a href='../index.php'>Login here</a>");
}

$role = $_SESSION['role']; // ✅ Define role for later use

// ✅ Handle delete user (prevent admin deletion)
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role!='admin'");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php");
    exit();
}

// ✅ Fetch all users
$result = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - PNP Doc Tracking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- ✅ Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f5faff;
            margin:0;
            padding:0;
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
            text-align:center;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            margin: 5px 0;
            border-radius: 5px;
            text-align:left;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .sidebar .logo {
            width: 100px;
            height: auto;
            margin: 0 auto 10px auto;
            display: block;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .topbar h1 {
            margin: 0;
            color: #007bff;
        }
        .container, .table-container {
            max-width:900px;
            margin:auto;
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top:10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align:center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) { background: #f9f9f9; }
        .actions a {
            color: #d9534f;
            font-weight: bold;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
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
        <a href="manage_users.php"><i class="fas fa-user-cog"></i> Manage Users</a> <!-- Updated icon -->
    <?php } ?>
    <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Container for centered top placement -->
    <div class="container" style="margin-top: 20px; text-align: center;">
        <!-- Topbar -->
        <div class="topbar">
            <h1><i class="fas fa-user-cog"></i> Manage Users</h1> <!-- Icon matches sidebar -->
        </div>

        <!-- User Table -->
        <div class="table-container" style="margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td class="actions">
                            <?php if($user['role'] != 'admin'): ?>
                                <a href="manage_users.php?delete=<?php echo $user['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                   Delete
                                </a>
                            <?php else: ?>
                                Admin
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


</body>
</html>
