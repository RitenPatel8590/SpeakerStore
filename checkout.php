<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/cartClass.php';
require('fpdf/fpdf186/fpdf.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);
$user_id = $_SESSION['user_id'];

// Get cart items and calculate total price
$cart_items = $cart->getCartItems($user_id);
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $phone = trim($_POST['phone']);
    $card_number = trim($_POST['card_number']);
    $cvv = trim($_POST['cvv']);
    $expiry_month = trim($_POST['expiry_month']);
    $expiry_year = trim($_POST['expiry_year']);
    $name_on_card = trim($_POST['name_on_card']);

    // Validation
    if (empty($firstname) || strlen($firstname) > 30) $errors['firstname'] = "First name is required and should be max 30 characters.";
    if (empty($lastname) || strlen($lastname) > 30) $errors['lastname'] = "Last name is required and should be max 30 characters.";
    if (empty($address) || strlen($address) > 30) $errors['address'] = "Address is required and should be max 30 characters.";
    if (empty($city) || strlen($city) > 30) $errors['city'] = "City is required and should be max 30 characters.";
    if (empty($state) || strlen($state) > 30) $errors['state'] = "State is required and should be max 30 characters.";
    if (!preg_match('/^\d{10}$/', $phone)) $errors['phone'] = "Valid Canadian phone number is required.";
    if (!preg_match('/^\d{16}$/', $card_number)) $errors['card_number'] = "Valid card number is required.";
    if (!preg_match('/^\d{3,4}$/', $cvv)) $errors['cvv'] = "Valid CVV is required.";
    if (!preg_match('/^\d{2}$/', $expiry_month) || !preg_match('/^\d{2}$/', $expiry_year)) {
        $errors['expiry_date'] = "Valid expiry date (MM/YY) is required.";
    } else {
        $current_year = date('y');
        $current_month = date('m');
        if ($expiry_year < $current_year || ($expiry_year == $current_year && $expiry_month < $current_month)) {
            $errors['expiry_date'] = "Expiry date cannot be in the past.";
        }
    }
    if (empty($name_on_card) || strlen($name_on_card) > 30) $errors['name_on_card'] = "Name on card is required and should be max 30 characters.";

    if (empty($errors)) {
        // Mask card number
        $masked_card_number = str_repeat('*', 12) . substr($card_number, -4);

        // Generate PDF
        

class PDF extends FPDF
{
    function Header()
    {
        // Set background color for header
        $this->SetFillColor(41, 128, 185); // A nice shade of blue
        $this->Rect(0, 0, 210, 40, 'F');
        
        // Add logo
        $this->Image('images/Companylogo.png', 20, 0, 30);
        
        // Set font for header
        $this->SetFont('Arial', 'B', 24);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 20, 'BoomBox Speakers', 0, 1, 'C');
        
        // Add a decorative line
        $this->SetDrawColor(236, 240, 241); // Light gray
        $this->SetLineWidth(0.5);
        $this->Line(10, 35, 200, 35);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(127, 140, 141); // Subtle gray
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 16);
        $this->SetFillColor(52, 152, 219); // Lighter blue
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 10, $title, 0, 1, 'L', true);
        $this->Ln(5);
    }

    function ContentRow($label, $value)
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(44, 62, 80); // Dark blue-gray
        $this->Cell(50, 8, $label, 0);
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(52, 73, 94); // Slightly lighter blue-gray
        $this->Cell(0, 8, $value, 0, 1);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Customer Details Section
$pdf->SectionTitle('Customer Details');
$pdf->ContentRow('Name:', $firstname . ' ' . $lastname);
$pdf->ContentRow('Address:', $address);
$pdf->ContentRow('City:', $city);
$pdf->ContentRow('State:', $state);
$pdf->ContentRow('Phone:', $phone);
$pdf->Ln(10);

// Payment Details Section
$pdf->SectionTitle('Payment Details');
$pdf->ContentRow('Name on Card:', $name_on_card);
$pdf->ContentRow('Card Number:', $masked_card_number);
$pdf->ContentRow('Expiry Date:', $expiry_month . '/' . $expiry_year);
$pdf->Ln(10);

// Order Summary Section
$pdf->SectionTitle('Order Summary');
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(236, 240, 241); // Light gray background for table header
$pdf->SetTextColor(44, 62, 80); // Dark blue-gray text for better contrast
$pdf->Cell(80, 10, 'Item', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Unit Price', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Total', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(249, 252, 255); // Very light blue background for even rows
$pdf->SetTextColor(52, 73, 94); // Slightly lighter blue-gray for content
$rowCount = 0;
foreach ($cart_items as $item) {
    $rowCount++;
    $item_total_price = $item['price'] * $item['quantity'];
    $pdf->SetFillColor($rowCount % 2 == 0 ? 249 : 255, 252, 255); // Alternate row colors
    $pdf->Cell(80, 8, $item['product_name'], 1, 0, 'L', true);
    $pdf->Cell(30, 8, $item['quantity'], 1, 0, 'C', true);
    $pdf->Cell(40, 8, '$' . number_format($item['price'], 2), 1, 0, 'R', true);
    $pdf->Cell(40, 8, '$' . number_format($item_total_price, 2), 1, 1, 'R', true);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(236, 240, 241); // Light gray background for total row
$pdf->SetTextColor(44, 62, 80); // Dark blue-gray text for better contrast
$pdf->Cell(150, 10, 'Total Purchase Cost:', 1, 0, 'R', true);
$pdf->Cell(40, 10, '$' . number_format($total_price, 2), 1, 1, 'R', true);

// Footer
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(127, 140, 141); // Subtle gray
$pdf->Cell(0, 6, 'Thank you for shopping with BoomBox Speakers!', 0, 1, 'C');
$pdf->Cell(0, 6, '© 2024 Group 3 Speaker\'s Group. All rights reserved.', 0, 1, 'C');
$pdf->Cell(0, 6, 'Developed by: Mitul, Khush, and Riten', 0, 1, 'C');

$pdf->Output('D', 'order_details.pdf');
         if ($cart->clearCart($user_id)) {
    // Redirect to index.php after clearing the cart
    header("Location: index.php");
    exit();
} else {
    echo "Error clearing cart. Please try again.";
}

exit();
    } else {
        // Handle errors or display them to the user
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BoomBox Speakers</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <?php include 'header1.php'; ?>
    <main class="container mt-5">
        <h2 class="text-center mb-4">Checkout</h2>
        <form action="checkout.php" method="post" class="needs-validation">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" maxlength="30" required value="<?= htmlspecialchars($firstname ?? '') ?>">
                    <div class="error"><?= $errors['firstname'] ?? '' ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" maxlength="30" required value="<?= htmlspecialchars($lastname ?? '') ?>">
                    <div class="error"><?= $errors['lastname'] ?? '' ?></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" maxlength="30" required value="<?= htmlspecialchars($address ?? '') ?>">
                <div class="error"><?= $errors['address'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" maxlength="30" required value="<?= htmlspecialchars($city ?? '') ?>">
                <div class="error"><?= $errors['city'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="state">State</label>
                <input type="text" class="form-control" id="state" name="state" maxlength="30" required value="<?= htmlspecialchars($state ?? '') ?>">
                <div class="error"><?= $errors['state'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="phone">Phone Number (10 digits)</label>
                <input type="text" class="form-control" id="phone" name="phone" required value="<?= htmlspecialchars($phone ?? '') ?>">
                <div class="error"><?= $errors['phone'] ?? '' ?></div>
            </div>
            <h4 class="mb-3">Billing Details</h4>
            <div class="mb-3">
                <label for="name_on_card">Name on Card</label>
                <input type="text" class="form-control" id="name_on_card" name="name_on_card" maxlength="30" required value="<?= htmlspecialchars($name_on_card ?? '') ?>">
                <div class="error"><?= $errors['name_on_card'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="card_number">Card Number</label>
                <input type="text" class="form-control" id="card_number" name="card_number" maxlength="16" required value="<?= htmlspecialchars($card_number ?? '') ?>">
                <div class="error"><?= $errors['card_number'] ?? '' ?></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="cvv">CVV</label>
                    <input type="text" class="form-control" id="cvv" name="cvv" maxlength="4" required value="<?= htmlspecialchars($cvv ?? '') ?>">
                    <div class="error"><?= $errors['cvv'] ?? '' ?></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="expiry_month">Expiry Month (MM)</label>
                    <input type="text" class="form-control" id="expiry_month" name="expiry_month" maxlength="2" required value="<?= htmlspecialchars($expiry_month ?? '') ?>">
                    <div class="error"><?= $errors['expiry_date'] ?? '' ?></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="expiry_year">Expiry Year (YY)</label>
                    <input type="text" class="form-control" id="expiry_year" name="expiry_year" maxlength="2" required value="<?= htmlspecialchars($expiry_year ?? '') ?>">
                    <div class="error"><?= $errors['expiry_date'] ?? '' ?></div>
                </div>
            </div>
            
            <h4 class="mb-3">Total Purchase Cost: $<?= number_format($total_price, 2) ?></h4>
            <button class="btn btn-primary btn-lg btn-block" type="submit">Checkout</button>
        </form>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
