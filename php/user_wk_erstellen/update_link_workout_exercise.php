<?php
session_start();
include '../datenbank_connection.php';
global $conn;

if (!isset($_SESSION['workout_id'])) {
    echo "<script>alert('Workout ID nicht gesetzt.');</script>";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workout_id = $_SESSION['workout_id'];
    $exercise_id = $_POST["exercise_id"];


    // Überprüfen, ob die Übung bereits dem Workout zugeordnet ist
    $stmt = $conn->prepare("SELECT * FROM link_workout_exercise WHERE workout_id_fk = ? AND exercise_id_fk = ?");
    $stmt->bind_param("ii", $workout_id, $exercise_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Übung ist bereits zugeordnet, also entfernen wir sie
        $stmt = $conn->prepare("DELETE FROM link_workout_exercise WHERE workout_id_fk = ? AND exercise_id_fk = ?");
        $stmt->bind_param("ii", $workout_id, $exercise_id);
        if ($stmt->execute()) {
            echo "removed";
        } else {
            echo "Fehler beim Entfernen der Übung.";
        }
    } else {
        // Übung ist nicht zugeordnet, also fügen wir sie hinzu
        $stmt = $conn->prepare("INSERT INTO link_workout_exercise (workout_id_fk, exercise_id_fk) VALUES (?, ?)");
        if (!$stmt) {
            die("SQL Error:");
        }
        $stmt->bind_param("ii", $workout_id, $exercise_id);
        if ($stmt->execute()) {
            echo "added";
        } else {
            echo "Error: ";
        }
        $stmt->close();
    }
}
?>