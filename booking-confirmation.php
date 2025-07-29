<?php
require 'db.php';

if (!isset($_SESSION['booking'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$booking = $_SESSION['booking'];
unset($_SESSION['booking']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Travel Comparison</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #333;
        }
        .navbar {
            background: #1e3c72;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 1.1rem;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            color: #1e3c72;
            margin-bottom: 1.5rem;
        }
        p {
            font-size: 1.1rem;
            margin: 0.5rem 0;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: #1e3c72;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 1rem 0.5rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2a5298;
        }
        @media (max-width: 600px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }
            .btn {
                display: block;
                margin: 1rem 0;
            }
        }
    </style>
    <script>
        function redirectToProvider(url) {
            window.location.href = url;
        }
    </script>
</head>
<body>
    <div class="navbar">
        <div>Travel Comparison</div>
        <div>
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <h2>Booking Confirmation</h2>
        <p><strong><?php echo ucfirst($booking['type']); ?>:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
        <p><strong>Provider:</strong> <?php echo htmlspecialchars($booking['provider']); ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($booking['price'], 2); ?></p>
        <p>You will be redirected to the provider's website to complete your booking.</p>
        <a href="#" class="btn" onclick="redirectToProvider('<?php echo $booking['url']; ?>')">Proceed to <?php echo htmlspecialchars($booking['provider']); ?></a>
        <a href="index.php" class="btn">Back to Home</a>
    </div>
</body>
</html>
