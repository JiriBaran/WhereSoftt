<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airsoft Akce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
</head>
<body>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start();
    $isLoggedIn = isset($_SESSION['user']);
    $username = $isLoggedIn ? $_SESSION['user']['username'] : null;

    $host = "localhost";
    $dbname = "wheresoft";
    $dbuser = "root";
    $dbpass = "root";

    $conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);
    if (!$conn) {
        die("Připojení k databázi selhalo: " . mysqli_connect_error());
    }

    mysqli_close($conn);
    ?>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">WhereSoft</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?php if ($isLoggedIn): ?>
                            <a class="nav-link" href="create-event.php">Vytvořit novou akci</a>
                        <?php else: ?>
                            <a class="nav-link disabled" href="#">Vytvořit novou akci</a>
                        <?php endif; ?>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><?php echo htmlspecialchars($username); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Odhlásit</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Přihlášení</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Registrace</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Přihlášení na Airsoft Akci</h1>
        <div id="map" style="height: 400px;"></div>

        <h2 class="mt-4">Seznam akcí k přihlášení</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Datum konání</th>
                    <th>Název akce</th>
                    <th>Název hřiště</th>
                    <th>Popis</th>
                    <th>Počet přihlášených</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody id="events-list">
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2024 WhereSoft</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="index.js"></script>
</body>
</html>
