<?php
session_start();
include "../datenbank_connection.php";
global $conn;

if (!isset($_SESSION['user_id'])) {
    echo "error: not authenticated";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $workout_id = $_SESSION['workout_id'];

    echo "Workout ID: " . $workout_id . ", User ID: " . $user_id; // Zum Testen der übergebenen IDs

    // SQL-Anweisung zum Löschen
    $sql = "DELETE FROM workouts WHERE workout_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $workout_id );

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "deleted";
    } else {
        echo "error: could not delete workout";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "error: invalid request method";
}
?>
