<?php
session_start();
include("../config.php");

// Get form inputs
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? 'user';  // Default to 'user' if not provided

// Check password confirmation
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $hashed_password, $role);

if ($stmt->execute()) {
    echo "✅ Registration successful. <a href='../index.php'>Login here</a>.";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
