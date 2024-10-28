<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "gym_tracker";

// Verbindung herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
?>
