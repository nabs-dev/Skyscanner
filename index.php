<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Comparison</title>
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
        .hero {
            text-align: center;
            padding: 3rem 1rem;
            background: url('https://source.unsplash.com/random/1920x1080/?travel') no-repeat center/cover;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .search-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            margin: 2rem auto;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .tabs button {
            padding: 0.75rem 2rem;
            border: none;
            background: #ccc;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background 0.3s;
        }
        .tabs button.active {
            background: #1e3c72;
            color: #fff;
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
        button {
            width: 100%;
            padding: 1rem;
            background: #1e3c72;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #2a5298;
        }
        @media (max-width: 600px) {
            .hero h1 {
                font-size: 1.8rem;
            }
            .search-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            .tabs button {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }
    </style>
    <script>
        function showTab(tab) {
            document.getElementById('flight-form').style.display = tab === 'flight' ? 'block' : 'none';
            document.getElementById('hotel-form').style.display = tab === 'hotel' ? 'block' : 'none';
            document.querySelectorAll('.tabs button').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`.tabs button[onclick="showTab('${tab}')"]`).classList.add('active');
        }
    </script>
</head>
<body onload="showTab('flight')">
    <div class="navbar">
        <div>Travel Comparison</div>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero">
        <h1>Find the Best Flights & Hotels</h1>
        <p>Compare prices from top providers in one place!</p>
    </div>
    <div class="search-container">
        <div class="tabs">
            <button class="active" onclick="showTab('flight')">Flights</button>
            <button onclick="showTab('hotel')">Hotels</button>
        </div>
        <form id="flight-form" action="flights.php" method="POST">
            <div class="form-group">
                <label for="origin">From</label>
                <input type="text" id="origin" name="origin" required>
            </div>
            <div class="form-group">
                <label for="destination">To</label>
                <input type="text" id="destination" name="destination" required>
            </div>
            <div class="form-group">
                <label for="departure_date">Departure Date</label>
                <input type="date" id="departure_date" name="departure_date" required>
            </div>
            <div class="form-group">
                <label for="return_date">Return Date</label>
                <input type="date" id="return_date" name="return_date">
            </div>
            <div class="form-group">
                <label for="passengers">Passengers</label>
                <select id="passengers" name="passengers" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <button type="submit">Search Flights</button>
        </form>
        <form id="hotel-form" action="hotels.php" method="POST" style="display: none;">
            <div class="form-group">
                <label for="hotel_destination">Destination</label>
                <input type="text" id="hotel_destination" name="destination" required>
            </div>
            <div class="form-group">
                <label for="check_in_date">Check-in Date</label>
                <input type="date" id="check_in_date" name="check_in_date" required>
            </div>
            <div class="form-group">
                <label for="check_out_date">Check-out Date</label>
                <input type="date" id="check_out_date" name="check_out_date" required>
            </div>
            <div class="form-group">
                <label for="guests">Guests</label>
                <select id="guests" name="guests" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <button type="submit">Search Hotels</button>
        </form>
    </div>
</body>
</html>
