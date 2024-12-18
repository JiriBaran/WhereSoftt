<?php
// Připojení k databázi
$servername = "localhost"; 
$username = "root";
$password = "root";
$dbname = "wheresoft"; 

// Připojení k databázi
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola připojení
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kontrola, zda byl odeslán POST požadavek
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Získání údajů z POST požadavku s kontrolou na neexistující hodnoty
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validace - kontrola, zda nejsou prázdná pole
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Vyplňte všechna pole.']);
        exit;
    }

    // Kontrola, zda už existuje uživatel s daným e-mailem
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'E-mail je již registrován.']);
        exit;
    }

    // Hasování hesla pro bezpečné uložení do databáze
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Vložení nového uživatele do databáze
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registrace byla úspěšná.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Chyba při registraci: ' . $stmt->error]);
    }

    // Zavření připraveného dotazu a připojení
    $stmt->close();
    $conn->close();
} else {
    // Pokud nebyl odeslán POST požadavek
    echo json_encode(['success' => false, 'message' => 'Neplatný požadavek.']);
}
?>