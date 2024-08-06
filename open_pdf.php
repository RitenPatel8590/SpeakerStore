<?php
session_start();

if (isset($_SESSION['pdf_filename'])) {
    $pdf_filename = $_SESSION['pdf_filename'];
    $pdf_path = 'invoices/' . $pdf_filename;

    // Clear the session variable
    unset($_SESSION['pdf_filename']);

    // HTML to open PDF in a new tab and redirect to thank you page
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Opening Invoice</title>
        <script>
            window.onload = function() {
                window.open("' . $pdf_path . '", "_blank");
                window.location.href = "thankYou.php";
            }
        </script>
    </head>
    <body>
        <p>Opening your invoice and redirecting to the thank you page...</p>
    </body>
    </html>';
} else {
    // Redirect to homepage if no PDF filename is set
    header('Location: index.php');
    exit();
}
?>