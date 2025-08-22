<?php
session_start();
include("../config.php");
require_once("../phpqrcode/qrlib.php");

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please <a href='../index.php'>login</a>.");
}

// ✅ Get role from session
$role = $_SESSION['role'] ?? 'user';

$message = ""; // Store upload status

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $uploadDir = "../uploads/";

    // ✅ Ensure uploads folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = basename($_FILES["file"]["name"]);
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $filepath)) {
        // Insert into database
        $uploaded_by = $_SESSION['user_id'];
        $sql = "INSERT INTO files (filename, filepath, uploaded_by, uploaded_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $filename, $filepath, $uploaded_by);
        $stmt->execute();
        $doc_id = $stmt->insert_id;

        // ✅ Generate QR Code
        $qrDir = "../uploads/qr/";
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0777, true);
        }

        $qrFilename = "qr_" . $doc_id . ".png";
        $qrFilepath = $qrDir . $qrFilename;

        $trackUrl = "http://localhost/pnp_doc_tracking/pages/track.php?id=" . $doc_id;
        QRcode::png($trackUrl, $qrFilepath, QR_ECLEVEL_L, 5);

        // ✅ Save QR file path in DB
        $update_sql = "UPDATE files SET qr_code=? WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $qrFilepath, $doc_id);
        $update_stmt->execute();

        $message = "
            <div style='color:green; text-align:center;'>
                ✅ File uploaded successfully! <br>
                <b>Document:</b> $filename <br>
                <a href='track.php?id=$doc_id'>🔗 View in Tracker</a><br><br>
                <p>📌 Scan this QR to track the document:</p>
                <img src='$qrFilepath' alt='QR Code' class='qr'>
            </div>
        ";
    } else {
        $message = "<div style='color:red; text-align:center;'>❌ Failed to upload file.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
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
            height: 100px;
            object-fit: contain;
            margin: 0 auto 10px auto;
            display: block;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        .card {
            max-width:700px;
            margin:auto;
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align:center; color:#007bff; }
        form { text-align:center; }
        input[type="file"] {
            margin:15px 0;
        }
        button {
            padding:10px 20px;
            background:#007bff;
            border:none;
            color:white;
            border-radius:6px;
            cursor:pointer;
        }
        button:hover {
            background:#0056b3;
        }
        img.qr {
            width:140px;
            height:140px;
            margin-top:10px;
        }
        .sidebar-title {
    font-size: 20px;
    font-weight: bold;
    margin: 10px 0 20px 0;
    color: #fff;   /* White text so it’s visible */
    text-align: center;
}
h2 i {
    margin-right: 8px;
    color: #007bff;
}

button i {
    margin-right: 6px;
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
    <div class="card">
        <h2><i class="fas fa-upload"></i> Upload a Document</h2> <!-- ✅ Fixed Icon -->
        <?php if ($message) echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required><br>
            <button type="submit"><i class="fas fa-cloud-upload-alt"></i> Upload</button> <!-- ✅ Added icon inside button -->
        </form>
    </div>
</div>

</body>
</html>
