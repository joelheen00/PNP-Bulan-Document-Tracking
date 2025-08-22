<?php
session_start();
require_once("../config.php");

// ✅ Define font path for FPDF to avoid undefined constant warning
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/../fpdf/font/');
}

require_once("../fpdf/fpdf.php");

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch filter values
$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;

// Build SQL query
$query = "SELECT f.filename, u.username, f.uploaded_at, f.access_count
          FROM files f
          JOIN users u ON f.uploaded_by = u.id
          WHERE 1";
if ($from) {
    $query .= " AND DATE(f.uploaded_at) >= '" . mysqli_real_escape_string($conn, $from) . "'";
}
if ($to) {
    $query .= " AND DATE(f.uploaded_at) <= '" . mysqli_real_escape_string($conn, $to) . "'";
}
$query .= " ORDER BY f.uploaded_at DESC";

$result = mysqli_query($conn, $query);

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'PNP Document Tracking Report', 0, 1, 'C');
$pdf->Ln(5);

if ($from || $to) {
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Filtered by Date: ' . ($from ?: 'Any') . ' to ' . ($to ?: 'Any'), 0, 1, 'C');
    $pdf->Ln(5);
}

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Filename', 1);
$pdf->Cell(40, 10, 'Uploader', 1);
$pdf->Cell(50, 10, 'Uploaded At', 1);
$pdf->Cell(40, 10, 'Access Count', 1);
$pdf->Ln();

// Table rows
$pdf->SetFont('Arial', '', 11);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(60, 10, $row['filename'], 1);
        $pdf->Cell(40, 10, $row['username'], 1);
        $pdf->Cell(50, 10, $row['uploaded_at'], 1);
        $pdf->Cell(40, 10, $row['access_count'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No records found', 1, 1, 'C');
}

// Output PDF
$pdf->Output("I", "reports.pdf");
?>
