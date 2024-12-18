<?php
// Zapnutí zobrazení chyb pro ladění
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Začátek session
session_start();

// Připojení k databázi
$host = "localhost";
$dbname = "wheresoft";
$dbuser = "root";  // Nahraďte skutečným uživatelským jménem
$dbpass = "root";  // Nahraďte skutečným heslem

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Připojení k databázi selhalo: " . mysqli_connect_error());
}

// Zpracování přihlašovacího formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = $_POST['username'];
    $passwordInput = $_POST['password'];

    // Ochrana proti SQL injection
    $userInput = mysqli_real_escape_string($conn, $userInput);

    // Dotaz do databáze
    $query = "SELECT * FROM users WHERE username = '$userInput' OR email = '$userInput'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Ověření hesla
        if (password_verify($passwordInput, $user['password'])) {
            // Uložení uživatele do session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];

            // Přesměrování na index.php
            mysqli_close($conn);
            header("Location: index.php");
            exit();
        } else {
            // Neplatné heslo
            $_SESSION['error_message'] = "Neplatné přihlašovací údaje.";
            mysqli_close($conn);
            header("Location: login.php");
            exit();
        }
    } else {
        // Uživatel nenalezen
        $_SESSION['error_message'] = "Uživatel nenalezen.";
        mysqli_close($conn);
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">WhereSoft</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Domů</a></li>
                </ul>
                <ul class="navbar-nav" id="userNav">
                    <!-- Dynamický obsah pro přihlášeného nebo nepřihlášeného uživatele -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Odhlásit</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Přihlášení</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.html">Registrace</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Přihlášení</h1>
        <form id="loginForm" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="username" class="form-label">Uživatelské jméno nebo E-mail</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Heslo</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                        <i id="passwordIcon" class="bi bi-eye-slash">Zobrazit heslo</i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Přihlásit se</button>
        </form>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger mt-3">
                <?php
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="mt-auto">
        <p>&copy; 2024 WhereSoft</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="login.js"></script>
</body>
</html>
