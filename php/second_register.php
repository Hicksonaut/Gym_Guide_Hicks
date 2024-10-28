<?php
session_start();
global $conn;
include 'datenbank_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "Benutzer-ID nicht gefunden. Bitte erneut registrieren.";
        exit;
    }

    $height = $_POST["height"];
    $weight = $_POST["weight"];
    $weight_aim = $_POST["weight_aim"];
    $age = $_POST["age"];

    $id = $_SESSION['user_id'];

    // Validate and sanitize user input
   // $user_id = filter_var($_COOKIE["user_id"], FILTER_SANITIZE_STRING);

    // Assuming all users' data are stored in a single table with user_id as a foreign key
    $stmt = $conn->prepare("UPDATE users SET height=?, weight=?, weight_aim=?, age=? WHERE id=?");$stmt = $conn->prepare("UPDATE users SET height=?, weight=?, weight_aim=?, age=? WHERE id=?");

    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Bind parameters (assuming user_id is an integer)
    $stmt->bind_param("iiiii", $height, $weight, $weight_aim, $age, $id);

    if ($stmt->execute()) {
        header("Location: /login.html");
        exit;
    } else {
        echo "Error executing statement: " . $stmt->error;
        exit;
    }
}
?>