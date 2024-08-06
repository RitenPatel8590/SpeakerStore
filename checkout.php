<?php
session_start();
include_once 'classes/dbclass.php';
include_once 'classes/cartClass.php';
require ('fpdf/fpdf186/fpdf.php');

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

    // Enhanced Validation
    if (empty($firstname) || !preg_match("/^[a-zA-Z-' ]{1,30}$/", $firstname))
        $errors['firstname'] = "First name is required, should contain only letters, spaces, hyphens, and apostrophes, and be max 30 characters.";
    if (empty($lastname) || !preg_match("/^[a-zA-Z-' ]{1,30}$/", $lastname))
        $errors['lastname'] = "Last name is required, should contain only letters, spaces, hyphens, and apostrophes, and be max 30 characters.";
    if (empty($address) || strlen($address) > 100)
        $errors['address'] = "Address is required and should be max 100 characters.";
    if (empty($city) || !preg_match("/^[a-zA-Z-' ]{1,30}$/", $city))
        $errors['city'] = "City is required, should contain only letters, spaces, hyphens, and apostrophes, and be max 30 characters.";
    if (empty($state) || !preg_match("/^[a-zA-Z-' ]{1,30}$/", $state))
        $errors['state'] = "Province is required, should contain only letters, spaces, hyphens, and apostrophes, and be max 30 characters.";
    if (!preg_match('/^\+?1?\s*\(?[2-9]\d{2}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/', $phone))
        $errors['phone'] = "Valid Canadian phone number is required (e.g., +1 (123) 456-7890 or 1234567890).";

    // Enhanced Credit Card Validation
    if (!preg_match('/^\d{13,19}$/', $card_number)) {
        $errors['card_number'] = "Valid card number is required (13-19 digits).";
    } else {
        // Luhn algorithm for card number validation
        $sum = 0;
        $length = strlen($card_number);
        for ($i = 0; $i < $length; $i++) {
            $digit = intval($card_number[$length - 1 - $i]);
            if ($i % 2 == 1) {
                $digit *= 2;
                if ($digit > 9)
                    $digit -= 9;
            }
            $sum += $digit;
        }
        if ($sum % 10 != 0) {
            $errors['card_number'] = "Invalid credit card number.";
        }
    }

    if (!preg_match('/^\d{3,4}$/', $cvv))
        $errors['cvv'] = "Valid CVV is required (3-4 digits).";
    if (!preg_match('/^(0[1-9]|1[0-2])$/', $expiry_month)) {
        $errors['expiry_month'] = "Valid expiry month (01-12) is required.";
    }
    if (!preg_match('/^\d{2}$/', $expiry_year)) {
        $errors['expiry_year'] = "Valid expiry year (YY) is required.";
    } else {
        $current_year = intval(date('y'));
        $current_month = intval(date('m'));
        $exp_year = intval($expiry_year);
        $exp_month = intval($expiry_month);
        if ($exp_year < $current_year || ($exp_year == $current_year && $exp_month < $current_month)) {
            $errors['expiry_date'] = "Card has expired.";
        } elseif ($exp_year > $current_year + 10) {
            $errors['expiry_date'] = "Expiry date too far in the future.";
        }
    }
    if (empty($name_on_card) || !preg_match("/^[a-zA-Z-' ]{1,30}$/", $name_on_card))
        $errors['name_on_card'] = "Name on card is required, should contain only letters, spaces, hyphens, and apostrophes, and be max 30 characters.";

    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors['csrf'] = "Invalid form submission.";
    }

    if (empty($errors)) {
        // Mask card number
        $masked_card_number = str_repeat('*', 12) . substr($card_number, -4);

        // Start a transaction
        $db->beginTransaction();

        try {
            $query = "INSERT INTO orders (user_id) VALUES (:user_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            if ($stmt->execute()) {
                $order_id = $db->lastInsertId(); // Get the last inserted order ID

                // Add order items to the database
                $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
                $stmt = $db->prepare($query);
                foreach ($cart_items as $item) {
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $item['product_id']);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }


                // Generate PDF
                class PDF extends FPDF
                {
                    function Header()
                    {
                        $this->SetFillColor(41, 128, 185); // A nice shade of blue
                        $this->Rect(0, 0, 210, 40, 'F');
                        $this->Image('images/Companylogo.png', 20, 0, 30);
                        $this->SetFont('Arial', 'B', 24);
                        $this->SetTextColor(255, 255, 255);
                        $this->Cell(0, 20, 'BoomBox Speakers', 0, 1, 'C');
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
                $pdf->SectionTitle('Customer Details');
                $pdf->ContentRow('Name:', $firstname . ' ' . $lastname);
                $pdf->ContentRow('Address:', $address);
                $pdf->ContentRow('City:', $city);
                $pdf->ContentRow('State:', $state);
                $pdf->ContentRow('Phone:', $phone);
                $pdf->Ln(10);
                $pdf->SectionTitle('Payment Details');
                $pdf->ContentRow('Name on Card:', $name_on_card);
                $pdf->ContentRow('Card Number:', $masked_card_number);
                $pdf->ContentRow('Expiry Date:', $expiry_month . '/' . $expiry_year);
                $pdf->Ln(10);
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

                $pdf->Ln(10);
                $pdf->SetFont('Arial', 'I', 10);
                $pdf->SetTextColor(127, 140, 141); // Subtle gray
                $pdf->Cell(0, 6, 'Thank you for shopping with BoomBox Speakers!', 0, 1, 'C');
                $pdf->Cell(0, 6, '© 2024 Group 3 Speaker\'s Group. All rights reserved.', 0, 1, 'C');
                $pdf->Cell(0, 6, 'Developed by: Mitul, Khush, and Riten', 0, 1, 'C');
                $pdf_filename = 'Invoice_' . $order_id . '.pdf';
                $pdf_path = 'invoices/' . $pdf_filename;
                $pdf->Output('F', $pdf_path);


                $cart->clearCart($user_id);


                $db->commit();


                $_SESSION['pdf_filename'] = $pdf_filename;


                header('Location: open_pdf.php');
                exit();
            }
        } catch (Exception $e) {

            $db->rollBack();
            $errors['database'] = "Error processing your order: " . $e->getMessage();
        }
    } else {

        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <?php include 'header1.php'; ?>
    <main class="container mt-5">
        <h2 class="text-center mb-4">Checkout</h2>
        <?php
        if (!empty($errors)) {
            echo '<div class="alert alert-danger" role="alert">';
            echo '<ul>';
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>
        <form action="checkout.php" method="post" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" maxlength="30" required
                        value="<?= htmlspecialchars($firstname ?? '') ?>">
                    <div class="invalid-feedback">Please enter a valid first name.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" maxlength="30" required
                        value="<?= htmlspecialchars($lastname ?? '') ?>">
                    <div class="invalid-feedback">Please enter a valid last name.</div>
                </div>
            </div>
            <div class="mb-3">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" maxlength="100" required
                    value="<?= htmlspecialchars($address ?? '') ?>">
                <div class="invalid-feedback">Please enter a valid address.</div>
            </div>
            <div class="mb-3">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" maxlength="30" required
                    value="<?= htmlspecialchars($city ?? '') ?>">
                <div class="invalid-feedback">Please enter a valid city name.</div>
            </div>
            <div class="mb-3">
                <label for="state">Province</label>
                <input type="text" class="form-control" id="state" name="state" maxlength="30" required
                    value="<?= htmlspecialchars($state ?? '') ?>">
                <div class="invalid-feedback">Please enter a valid province name.</div>
            </div>
            <div class="mb-3">
                <label for="phone">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" required placeholder="e.g., 1234567890"
                    value="<?= htmlspecialchars($phone ?? '') ?>">
                <div class="invalid-feedback">Please enter a valid Canadian phone number.</div>
            </div>
            <h4 class="mb-3">Billing Details</h4>
            <div class="mb-3">
                <label for="name_on_card">Name on Card</label>
                <input type="text" class="form-control" id="name_on_card" name="name_on_card" maxlength="30" required
                    value="<?= htmlspecialchars($name_on_card ?? '') ?>">
                <div class="invalid-feedback">Please enter the name as it appears on your card.</div>
            </div>
            <div class="mb-3">
                <label for="card_number">Card Number</label>
                <input type="text" class="form-control" id="card_number" name="card_number" maxlength="19" required
                    value="<?= htmlspecialchars($card_number ?? '') ?>">
                <div class="invalid-feedback">Please enter a valid credit card number.</div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="cvv">CVV</label>
                    <input type="password" class="form-control" id="cvv" name="cvv" maxlength="4" required
                        value="<?= htmlspecialchars($cvv ?? '') ?>">
                    <div class="invalid-feedback">Please enter a valid CVV.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="expiry_month">Expiry Month (MM)</label>
                    <input type="text" class="form-control" id="expiry_month" name="expiry_month" maxlength="2" required
                        placeholder="MM" value="<?= htmlspecialchars($expiry_month ?? '') ?>">
                    <div class="invalid-feedback">Please enter a valid expiry month (01-12).</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="expiry_year">Expiry Year (YY)</label>
                    <input type="text" class="form-control" id="expiry_year" name="expiry_year" maxlength="2" required
                        placeholder="YY" value="<?= htmlspecialchars($expiry_year ?? '') ?>">
                    <div class="invalid-feedback">Please enter a valid expiry year (YY).</div>
                </div>
            </div>

            <h4 class="mb-3">Total Purchase Cost: $<?= number_format($total_price, 2) ?></h4>
            <button class="btn btn-primary btn-lg btn-block" type="submit">Complete Purchase</button>
        </form>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Custom form validation
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>

</html>