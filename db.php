<?php
$servername = "localhost"; // Adresa serveru (např. localhost)
$username = "root";        // Uživatelské jméno MySQL
$password = "root";        // Heslo MySQL
$database = "wheresoft";   // Název databáze

// Vytvoření připojení pomocí MySQLi
$conn = new mysqli($servername, $username, $password, $database);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}

// Nastavení kódování na UTF-8
$conn->set_charset("utf8");
?>
