<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $name = $_POST['name'];
    $provider = $_POST['provider'];
    $price = $_POST['price'];
    $url = $_POST['url'];

    if (isset($_SESSION['user_id'])) {
        $details = "$name via $provider";
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, type, provider, details, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $type, $provider, $details, $price]);
    }

    // Store booking details in session for confirmation page
    $_SESSION['booking'] = [
        'type' => $type,
        'name' => $name,
        'provider' => $provider,
        'price' => $price,
        'url' => $url
    ];

    echo "<script>window.location.href='booking-confirmation.php';</script>";
}
?>
