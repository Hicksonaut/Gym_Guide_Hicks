<?php
session_start();
include '../datenbank_connection.php';
global $conn;

if (!isset($_SESSION['plan_id'])) {
    echo "<script>alert('Plan ID nicht gesetzt.');</script>";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan_id = $_SESSION['plan_id'];
    $workout_id = $_POST["workout_id"];

    // Überprüfen, ob die Übung bereits dem Workout zugeordnet ist
    $stmt = $conn->prepare("SELECT * FROM link_plan_workout WHERE plan_id = ? AND Workout_id = ?");
    $stmt->bind_param("ii", $plan_id, $workout_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Übung ist bereits zugeordnet, also entfernen wir sie
        $stmt = $conn->prepare("DELETE FROM link_plan_workout WHERE plan_id = ? AND Workout_id = ?");
        $stmt->bind_param("ii", $plan_id, $workout_id);
        if ($stmt->execute()) {
            echo "removed";
        } else {
            echo "Fehler beim Entfernen des Workouts.";
        }
    } else {
        // Übung ist nicht zugeordnet, also fügen wir sie hinzu
        $stmt = $conn->prepare("INSERT INTO link_plan_workout (plan_id, workout_id) VALUES (?, ?)");
        if (!$stmt) {
            die("SQL Error:");
        }
        $stmt->bind_param("ii", $plan_id, $workout_id);
        if ($stmt->execute()) {
            echo "added";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>