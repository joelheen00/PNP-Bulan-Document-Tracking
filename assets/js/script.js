// Sidebar toggle (for mobile)
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }
});

// QR Scanner Auto-Redirect
function onScanSuccess(decodedText, decodedResult) {
    // When QR is scanned → redirect to track page
    window.location.href = "pages/track.php?code=" + encodeURIComponent(decodedText);
}

if (document.getElementById("qr-reader")) {
    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        onScanSuccess
    );
}
