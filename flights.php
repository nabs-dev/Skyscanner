<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];
    $return_date = $_POST['return_date'] ?? null;
    $passengers = $_POST['passengers'];

    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("INSERT INTO flight_searches (user_id, origin, destination, departure_date, return_date, passengers) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $origin, $destination, $departure_date, $return_date, $passengers]);
    }

    // Dummy flight data
    $flights = [
        ['airline' => 'Airline A', 'provider' => 'Expedia', 'price' => 300.00, 'duration' => '5h 30m', 'stops' => 0, 'url' => 'https://expedia.com'],
        ['airline' => 'Airline B', 'provider' => 'Kayak', 'price' => 350.00, 'duration' => '6h 15m', 'stops' => 1, 'url' => 'https://kayak.com'],
        ['airline' => 'Airline C', 'provider' => 'Skyscanner', 'price' => 280.00, 'duration' => '5h 00m', 'stops' => 0, 'url' => 'https://skyscanner.com'],
    ];

    // Apply filters
    $min_price = $_POST['min_price'] ?? 0;
    $max_price = $_POST['max_price'] ?? PHP_INT_MAX;
    $max_stops = $_POST['max_stops'] ?? PHP_INT_MAX;
    $sort = $_POST['sort'] ?? 'price';

    $flights = array_filter($flights, function($flight) use ($min_price, $max_price, $max_stops) {
        return $flight['price'] >= $min_price && $flight['price'] <= $max_price && $flight['stops'] <= $max_stops;
    });

    if ($sort == 'price') {
        usort($flights, function($a, $b) { return $a['price'] <=> $b['price']; });
    } elseif ($sort == 'duration') {
        usort($flights, function($a, $b) { return strcmp($a['duration'], $b['duration']); });
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Results - Travel Comparison</title>
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: flex;
            gap: 2rem;
        }
        .filters {
            flex: 1;
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .results {
            flex: 3;
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #1e3c72;
        }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        .result-item {
            border-bottom: 1px solid #ccc;
            padding: 1rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .result-item:last-child {
            border-bottom: none;
        }
        .result-details {
            flex: 2;
        }
        .result-price {
            flex: 1;
            text-align: right;
        }
        button {
            padding: 0.75rem 1.5rem;
            background: #1e3c72;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #2a5298;
        }
        @media (max-width: 800px) {
            .container {
                flex-direction: column;
            }
            .filters, .results {
                width: 100%;
            }
        }
    </style>
    <script>
        function bookFlight(url, airline, provider, price) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'booking.php';
            form.innerHTML = `
                <input type="hidden" name="type" value="flight">
                <input type="hidden" name="name" value="${airline}">
                <input type="hidden" name="provider" value="${provider}">
                <input type="hidden" name="price" value="${price}">
                <input type="hidden" name="url" value="${url}">
            `;
            document.body.appendChild(form);
            form.submit();
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
        <div class="filters">
            <h3>Filters</h3>
            <form method="POST">
                <input type="hidden" name="origin" value="<?php echo htmlspecialchars($origin); ?>">
                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                <input type="hidden" name="departure_date" value="<?php echo $departure_date; ?>">
                <input type="hidden" name="return_date" value="<?php echo $return_date; ?>">
                <input type="hidden" name="passengers" value="<?php echo $passengers; ?>">
                <div class="form-group">
                    <label for="min_price">Min Price ($)</label>
                    <input type="number" id="min_price" name="min_price" value="<?php echo $min_price ?: ''; ?>">
                </div>
                <div class="form-group">
                    <label for="max_price">Max Price ($)</label>
                    <input type="number" id="max_price" name="max_price" value="<?php echo $max_price == PHP_INT_MAX ? '' : $max_price; ?>">
                </div>
                <div class="form-group">
                    <label for="max_stops">Max Stops</label>
                    <select id="max_stops" name="max_stops">
                        <option value="0" <?php echo $max_stops == 0 ? 'selected' : ''; ?>>Non-stop</option>
                        <option value="1" <?php echo $max_stops == 1 ? 'selected' : ''; ?>>1 Stop</option>
                        <option value="2" <?php echo $max_stops == 2 ? 'selected' : ''; ?>>2+ Stops</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sort">Sort By</label>
                    <select id="sort" name="sort">
                        <option value="price" <?php echo $sort == 'price' ? 'selected' : ''; ?>>Price</option>
                        <option value="duration" <?php echo $sort == 'duration' ? 'selected' : ''; ?>>Duration</option>
                    </select>
                </div>
                <button type="submit">Apply Filters</button>
            </form>
        </div>
        <div class="results">
            <h3>Flight Results from <?php echo htmlspecialchars($origin); ?> to <?php echo htmlspecialchars($destination); ?></h3>
            <?php foreach ($flights as $flight): ?>
                <div class="result-item">
                    <div class="result-details">
                        <h4><?php echo htmlspecialchars($flight['airline']); ?></h4>
                        <p>Provider: <?php echo htmlspecialchars($flight['provider']); ?></p>
                        <p>Duration: <?php echo $flight['duration']; ?></p>
                        <p>Stops: <?php echo $flight['stops'] == 0 ? 'Non-stop' : $flight['stops'] . ' Stop(s)'; ?></p>
                    </div>
                    <div class="result-price">
                        <p><strong>$<?php echo number_format($flight['price'], 2); ?></strong></p>
                        <button onclick="bookFlight('<?php echo $flight['url']; ?>', '<?php echo addslashes($flight['airline']); ?>', '<?php echo addslashes($flight['provider']); ?>', '<?php echo $flight['price']; ?>')">Book Now</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
