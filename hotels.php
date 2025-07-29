<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination = $_POST['destination'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $guests = $_POST['guests'];

    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("INSERT INTO hotel_searches (user_id, destination, check_in_date, check_out_date, guests) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $destination, $check_in_date, $check_out_date, $guests]);
    }

    // Dummy hotel data
    $hotels = [
        ['name' => 'Grand Hotel', 'provider' => 'Booking.com', 'price' => 150.00, 'rating' => 4.5, 'url' => 'https://booking.com'],
        ['name' => 'City Lodge', 'provider' => 'Expedia', 'price' => 120.00, 'rating' => 4.0, 'url' => 'https://expedia.com'],
        ['name' => 'Luxury Suites', 'provider' => 'Hotels.com', 'price' => 200.00, 'rating' => 4.8, 'url' => 'https://hotels.com'],
    ];

    // Apply filters
    $min_price = $_POST['min_price'] ?? 0;
    $max_price = $_POST['max_price'] ?? PHP_INT_MAX;
    $min_rating = $_POST['min_rating'] ?? 0;
    $sort = $_POST['sort'] ?? 'price';

    $hotels = array_filter($hotels, function($hotel) use ($min_price, $max_price, $min_rating) {
        return $hotel['price'] >= $min_price && $hotel['price'] <= $max_price && $hotel['rating'] >= $min_rating;
    });

    if ($sort == 'price') {
        usort($hotels, function($a, $b) { return $a['price'] <=> $b['price']; });
    } elseif ($sort == 'rating') {
        usort($hotels, function($a, $b) { return $b['rating'] <=> $a['rating']; });
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Results - Travel Comparison</title>
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
        function bookHotel(url, name, provider, price) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'booking.php';
            form.innerHTML = `
                <input type="hidden" name="type" value="hotel">
                <input type="hidden" name="name" value="${name}">
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
                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                <input type="hidden" name="check_in_date" value="<?php echo $check_in_date; ?>">
                <input type="hidden" name="check_out_date" value="<?php echo $check_out_date; ?>">
                <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                <div class="form-group">
                    <label for="min_price">Min Price ($)</label>
                    <input type="number" id="min_price" name="min_price" value="<?php echo $min_price ?: ''; ?>">
                </div>
                <div class="form-group">
                    <label for="max_price">Max Price ($)</label>
                    <input type="number" id="max_price" name="max_price" value="<?php echo $max_price == PHP_INT_MAX ? '' : $max_price; ?>">
                </div>
                <div class="form-group">
                    <label for="min_rating">Min Rating</label>
                    <select id="min_rating" name="min_rating">
                        <option value="0" <?php echo $min_rating == 0 ? 'selected' : ''; ?>>Any</option>
                        <option value="3" <?php echo $min_rating == 3 ? 'selected' : ''; ?>>3+</option>
                        <option value="4" <?php echo $min_rating == 4 ? 'selected' : ''; ?>>4+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sort">Sort By</label>
                    <select id="sort" name="sort">
                        <option value="price" <?php echo $sort == 'price' ? 'selected' : ''; ?>>Price</option>
                        <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Rating</option>
                    </select>
                </div>
                <button type="submit">Apply Filters</button>
            </form>
        </div>
        <div class="results">
            <h3>Hotel Results for <?php echo htmlspecialchars($destination); ?></h3>
            <?php foreach ($hotels as $hotel): ?>
                <div class="result-item">
                    <div class="result-details">
                        <h4><?php echo htmlspecialchars($hotel['name']); ?></h4>
                        <p>Provider: <?php echo htmlspecialchars($hotel['provider']); ?></p>
                        <p>Rating: <?php echo $hotel['rating']; ?>/5</p>
                    </div>
                    <div class="result-price">
                        <p><strong>$<?php echo number_format($hotel['price'], 2); ?></strong>/night</p>
                        <button onclick="bookHotel('<?php echo $hotel['url']; ?>', '<?php echo addslashes($hotel['name']); ?>', '<?php echo addslashes($hotel['provider']); ?>', '<?php echo $hotel['price']; ?>')">Book Now</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
