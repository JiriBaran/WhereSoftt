<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "wheresoft";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventType = isset($_POST['eventType']) ? $_POST['eventType'] : '';
    $fieldName = isset($_POST['fieldName']) ? $_POST['fieldName'] : '';
    $eventName = isset($_POST['eventName']) ? $_POST['eventName'] : '';
    $eventDate = isset($_POST['eventDate']) ? $_POST['eventDate'] : '';
    $eventDescription = isset($_POST['eventDescription']) ? $_POST['eventDescription'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    $attendees = 0;

    $stmt = $conn->prepare("INSERT INTO events (type, field_name, name, date, description, latitude, longitude, attendees) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        echo json_encode(["success" => false, "error" => "Chyba při přípravě SQL dotazu: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssssdi", $eventType, $fieldName, $eventName, $eventDate, $eventDescription, $latitude, $longitude, $attendees);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo json_encode(["success" => false, "error" => "Chyba při ukládání do databáze: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvořit novou Airsoft Akci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">WhereSoft</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Domů</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create-event.php">Vytvořit akci</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Přihlášení</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.html">Registrace</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container">
        <h1 class="text-center mb-4">Vytvořte novou Airsoft Akci</h1>
        <div id="map" style="height: 400px;"></div>
        <form method="POST">
            <div class="mb-3">
                <label for="eventType" class="form-label">Typ akce</label>
                <select id="eventType" class="form-control" name="eventType" required>
                    <option value="unofficial">Neoficiální</option>
                    <option value="official">Oficiální</option>
                </select>
            </div>
            <div class="mb-3" id="fieldNameDiv" style="display: none;">
                <label for="fieldName" class="form-label">Název hřiště</label>
                <input type="text" class="form-control" id="fieldName" name="fieldName">
            </div>
            <div class="mb-3">
                <label for="eventName" class="form-label">Název akce</label>
                <input type="text" class="form-control" id="eventName" name="eventName" required>
            </div>
            <div class="mb-3">
                <label for="eventDate" class="form-label">Datum a čas</label>
                <input type="datetime-local" class="form-control" id="eventDate" name="eventDate" required>
            </div>
            <div class="mb-3">
                <label for="eventDescription" class="form-label">Popis akce</label>
                <textarea id="eventDescription" class="form-control" name="eventDescription"></textarea>
            </div>
            <div class="mb-3">
                <label for="eventLocation" class="form-label">Umístění</label>
                <input type="text" class="form-control" id="eventLocation" readonly>
            </div>
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <button type="submit" class="btn btn-primary w-100">Vytvořit akci</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="mt-5 text-center">
        <p>&copy; 2024 WhereSoft</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="create-event.js"></script>
</body>
</html>
