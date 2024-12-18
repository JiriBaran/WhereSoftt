<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = 'localhost'; // Change if your database is hosted elsewhere
$dbname = 'wheresoft';
$username = 'root'; // Replace with your database username
$password = 'root'; // Replace with your database password

try {
    // Establish a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, return a 500 error with the message
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Set the content type to JSON
header('Content-Type: application/json');

try {
    // Define the SQL query to fetch event data along with the count of attendees
    $query = "
        SELECT 
            e.id, 
            e.date, 
            e.name, 
            e.type, 
            e.field_name, 
            e.description, 
            COUNT(a.user_id) AS attendees,
            e.latitude, 
            e.longitude
        FROM events e
        LEFT JOIN event_attendees a ON e.id = a.event_id
        GROUP BY e.id
    ";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch all results as an associative array
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output the events data as JSON
    echo json_encode($events);
} catch (Exception $e) {
    // If query fails, return a 500 error with the message
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching data: ' . $e->getMessage()]);
}
?>
