<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$flight_searches = $pdo->prepare("SELECT * FROM flight_searches WHERE user_id = ? ORDER BY search_time DESC");
$flight_searches->execute([$user_id]);
$flights = $flight_searches->fetchAll();

$hotel_searches = $pdo->prepare("SELECT * FROM hotel_searches WHERE user_id = ? ORDER BY search_time DESC");
$hotel_searches->execute([$user_id]);
$hotels = $hotel_searches->fetchAll();

$bookings = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_time DESC");
$bookings->execute([$user_id]);
$bookings_list = $bookings->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Travel Comparison</title>
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
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        h2 {
            color: #1e3c72;
            margin-bottom: 1rem;
        }
        .section {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #1e3c72;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        @media (max-width: 600px) {
            table {
                font-size: 0.9rem;
            }
            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>Travel Comparison</div>
        <div>
            <a href="index.php">Home</a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
        <h2>Your Dashboard</h2>
        <div class="section">
            <h3>Saved Flight Searches</h3>
            <table>
                <tr>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Departure</th>
                    <th>Return</th>
                    <th>Passengers</th>
                    <th>Search Time</th>
                </tr>
                <?php foreach ($flights as $flight): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flight['origin']); ?></td>
                        <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                        <td><?php echo $flight['departure_date']; ?></td>
                        <td><?php echo $flight['return_date'] ?: 'N/A'; ?></td>
                        <td><?php echo $flight['passengers']; ?></td>
                        <td><?php echo $flight['search_time']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="section">
            <h3>Saved Hotel Searches</h3>
            <table>
                <tr>
                    <th>Destination</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Guests</th>
                    <th>Search Time</th>
                </tr>
                <?php foreach ($hotels as $hotel): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hotel['destination']); ?></td>
                        <td><?php echo $hotel['check_in_date']; ?></td>
                        <td><?php echo $hotel['check_out_date']; ?></td>
                        <td><?php echo $hotel['guests']; ?></td>
                        <td><?php echo $hotel['search_time']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="section">
            <h3>Booking History</h3>
            <table>
                <tr>
                    <th>Type</th>
                    <th>Provider</th>
                    <th>Details</th>
                    <th>Price</th>
                    <th>Booking Time</th>
                </tr>
                <?php foreach ($bookings_list as $booking): ?>
                    <tr>
                        <td><?php echo ucfirst($booking['type']); ?></td>
                        <td><?php echo htmlspecialchars($booking['provider']); ?></td>
                        <td><?php echo htmlspecialchars($booking['details']); ?></td>
                        <td>$<?php echo number_format($booking['price'], 2); ?></td>
                        <td><?php echo $booking['booking_time']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
