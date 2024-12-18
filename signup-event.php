<?php

session_start();
if (!isset($_SESSION['user'])) {
    die("Musíte být přihlášeni.");
}

$user_id = $_SESSION['user']['id']; // ID přihlášeného uživatele
$event_id = $_POST['event_id']; // ID události
$action = $_POST['action']; // "signup" nebo "cancel"

// Připojení k databázi
$host = "localhost";
$dbname = "wheresoft";
$dbuser = "root";
$dbpass = "root";

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Připojení k databázi selhalo: " . mysqli_connect_error());
}

if ($action == 'signup') {
    // Zkontrolujeme, zda už uživatel není přihlášený na tuto akci
    $query = "SELECT * FROM event_attendees WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "already_signed_up";
    } else {
        // Přidáme uživatele do tabulky
        $insertQuery = "INSERT INTO event_attendees (user_id, event_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $user_id, $event_id);

        if ($stmt->execute()) {
            // Po přidání uživatele získejte nový počet přihlášených
            $countQuery = "SELECT COUNT(*) AS attendees_count FROM event_attendees WHERE event_id = ?";
            $stmt = $conn->prepare($countQuery);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $attendeesCount = $row['attendees_count'];

            echo $attendeesCount; // Počet přihlášených uživatelů
        } else {
            echo "error_signup";
        }
    }
} elseif ($action == 'cancel') {
    // Zrušíme přihlášení uživatele
    $deleteQuery = "DELETE FROM event_attendees WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ii", $user_id, $event_id);

    if ($stmt->execute()) {
        // Po odstranění uživatele získejte nový počet přihlášených
        $countQuery = "SELECT COUNT(*) AS attendees_count FROM event_attendees WHERE event_id = ?";
        $stmt = $conn->prepare($countQuery);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $attendeesCount = $row['attendees_count'];

        echo $attendeesCount; // Počet přihlášených uživatelů
    } else {
        echo "error_cancel";
    }
}

mysqli_close($conn);
?>
