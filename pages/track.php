<?php
session_start();
include("../config.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// If `id` is provided, show document details
if (isset($_GET['id'])) {
    $doc_id = intval($_GET['id']);

    $sql = "SELECT * FROM files WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("<h3 style='color:red;text-align:center;'><i class='fas fa-exclamation-triangle'></i> Document not found.</h3>");
    }

    $doc = $result->fetch_assoc();

    // Log access
    if (isset($_SESSION['user_id'])) {
        $action = "Viewed";
        $user_id = $_SESSION['user_id'];
        $log_sql = "INSERT INTO logs (document_id, action, user_id) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("isi", $doc_id, $action, $user_id);
        $log_stmt->execute();
    }
}

// Function to convert server path to web URL
function toRelativePath($path) {
    // Remove leading "../" for web access
    return preg_replace("/^\.\.\//", "", $path);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Track Document - PNP Doc Tracking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background:#f5faff; margin:0; padding:0; }
        .sidebar { width: 220px; background-color: #007bff; position: fixed; top: 0; left: 0; height: 100%; padding-top: 20px; color: white; text-align:center; }
        .sidebar a { display: block; color: white; padding: 12px 20px; text-decoration: none; margin: 5px 0; border-radius: 5px; text-align:left; }
        .sidebar a:hover { background-color: #0056b3; }
        .sidebar .logo { width: 100px; height: auto; margin: 0 auto 10px auto; display: block; }
        .sidebar-title { font-size: 20px; font-weight: bold; margin: 10px 0 20px 0; color: #fff; text-align: center; }
        .main-content { margin-left: 240px; padding: 20px; }
        .container { max-width:900px; margin:auto; background:white; padding:20px; border-radius:12px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#007bff; }
        ul { list-style: none; padding:0; }
        li { margin:5px 0; padding:8px; background:#eef; border-radius:5px; display:flex; justify-content:space-between; align-items:center; }
        .doc-actions a { margin-left:10px; padding:5px 8px; background:#007bff; color:white; border-radius:5px; text-decoration:none; }
        .doc-actions a:hover { background:#0056b3; }
        img.qr { width:140px; height:140px; margin-top:10px; }
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
        <?php if (isset($doc)) { ?>
            <h2><i class="fas fa-file-alt"></i> Document Details</h2>
            <p><b>Filename:</b> <?php echo htmlspecialchars($doc['filename']); ?></p>
            <p><b>Uploaded At:</b> <?php echo htmlspecialchars($doc['uploaded_at']); ?></p>
            <p><a href="../<?php echo htmlspecialchars($doc['filepath']); ?>" download><i class="fas fa-download"></i> Download File</a></p>
            <p><a href="edit.php?id=<?php echo $doc_id; ?>" class="doc-actions"><i class="fas fa-edit"></i> Edit Document</a></p>

            <?php if(!empty($doc['qr_code'])): ?>
                <p><i class="fas fa-qrcode"></i> Scan this QR to track the document:</p>
                <img src="../<?php echo toRelativePath($doc['qr_code']); ?>" alt="QR Code" class="qr">
            <?php endif; ?>

            <h3><i class="fas fa-history"></i> Access Logs</h3>
            <ul>
                <?php
                $log_sql = "SELECT logs.action, logs.action_time, users.username 
                            FROM logs 
                            JOIN users ON logs.user_id=users.id 
                            WHERE logs.document_id=? 
                            ORDER BY logs.action_time DESC";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("i", $doc_id);
                $log_stmt->execute();
                $logs = $log_stmt->get_result();
                if ($logs->num_rows > 0) {
                    while ($row = $logs->fetch_assoc()) {
                        echo "<li><i class='fas fa-clock'></i> " . $row['action_time'] . " - " . htmlspecialchars($row['username']) . " " . htmlspecialchars($row['action']) . "</li>";
                    }
                } else {
                    echo "<li><i class='fas fa-info-circle'></i> No logs yet.</li>";
                }
                ?>
            </ul>
            <p><a href="track.php"><i class="fas fa-arrow-left"></i> Back to All Documents</a></p>
        <?php } else { ?>
            <h2><i class="fas fa-folder-open"></i> All Uploaded Documents</h2>
            <ul>
                <?php
                $all_sql = "SELECT id, filename, uploaded_at, qr_code FROM files ORDER BY uploaded_at DESC";
                $all_docs = $conn->query($all_sql);
                if ($all_docs->num_rows > 0) {
                    while ($row = $all_docs->fetch_assoc()) {
                        echo "<li>
                                <span><i class='fas fa-file'></i> " . htmlspecialchars($row['filename']) . " (Uploaded: " . $row['uploaded_at'] . ")</span>
                                <span class='doc-actions'>
                                    <a href='track.php?id=" . $row['id'] . "'><i class='fas fa-eye'></i> View</a>
                                    <a href='edit.php?id=" . $row['id'] . "'><i class='fas fa-edit'></i> Edit</a>
                                </span>
                              </li>";
                    }
                } else {
                    echo "<li><i class='fas fa-info-circle'></i> No documents uploaded yet.</li>";
                }
                ?>
            </ul>
        <?php } ?>
    </div>
</div>

</body>
</html>
