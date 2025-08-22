<?php
session_start();
include("../config.php");

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please <a href='../index.php'>login</a>.");
}

if (!isset($_GET['id'])) {
    die("<h3 style='color:red;text-align:center;'>⚠ Document ID missing.</h3>");
}

$doc_id = intval($_GET['id']);
$sql = "SELECT * FROM files WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doc_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("<h3 style='color:red;text-align:center;'>⚠ Document not found.</h3>");

$doc = $result->fetch_assoc();
$filepath = "../" . $doc['filepath'];

// ✅ Only allow editing text-based files
$editable_extensions = ['txt', 'csv', 'html', 'md', 'json', 'xml', 'php'];
$file_ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
$is_editable = in_array($file_ext, $editable_extensions);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    if ($is_editable) {
        $content = $_POST['content'];
        if (file_put_contents($filepath, $content) !== false) {
            $message = "✅ File saved successfully!";
        } else {
            $message = "❌ Failed to save file. Check folder permissions.";
        }
    } else {
        $message = "⚠ This file type cannot be edited online.";
    }
}

// ✅ Load file content safely
$file_content = "";
if ($is_editable && file_exists($filepath)) {
    $file_content = file_get_contents($filepath);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Document - <?php echo htmlspecialchars($doc['filename']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f5faff; padding:20px; }
        .container { max-width:800px; margin:auto; background:white; padding:20px; border-radius:12px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#007bff; }
        textarea { width:100%; height:400px; padding:10px; font-family: monospace; font-size:14px; border-radius:6px; border:1px solid #ccc; }
        button { padding:10px 15px; background:#007bff; color:white; border:none; border-radius:6px; cursor:pointer; margin-top:10px; }
        button:hover { background:#0056b3; }
        .message { color:green; text-align:center; margin-bottom:15px; }
        a { text-decoration:none; color:#007bff; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit: <?php echo htmlspecialchars($doc['filename']); ?></h2>
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <?php if ($is_editable): ?>
        <form method="POST">
            <textarea name="content"><?php echo htmlspecialchars($file_content); ?></textarea><br>
            <button type="submit">💾 Save Changes</button>
        </form>
    <?php else: ?>
        <p style="text-align:center; color:red;">⚠ This file type (<?php echo $file_ext; ?>) cannot be edited online.</p>
        <p style="text-align:center;"><a href="../<?php echo $doc['filepath']; ?>" download>⬇ Download to edit locally</a></p>
    <?php endif; ?>

    <p style="text-align:center;"><a href="track.php?id=<?php echo $doc_id; ?>">⬅ Back to Document</a></p>
</div>
</body>
</html>
