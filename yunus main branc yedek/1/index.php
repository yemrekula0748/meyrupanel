<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard | Admin Panel</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.min.css">
    <link rel="stylesheet" href="assets/css/icons.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-message {
            text-align: center;
        }
        .dashboard-message h1 {
            font-size: 3rem;
            color: #333;
        }
        .dashboard-message p {
            font-size: 1.2rem;
            color: #555;
        }
    </style>
</head>
<body>
    <?php include 'tema/menu.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-message">
            <h1>Dashboard</h1>
            <p>Hoş geldiniz! Bu sayfa gelecekte doldurulacaktır.</p>
        </div>
    </div>

    <?php include 'tema/footer.php'; ?>

    <!-- JS -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
