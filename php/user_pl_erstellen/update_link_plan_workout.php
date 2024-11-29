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
    $stmt = $conn->prepare("SELECT * FROM Link_Plan_Workout WHERE plan_id = ? AND Workout_id = ?");
    $stmt->bind_param("ii", $plan_id, $workout_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Übung ist bereits zugeordnet, also entfernen wir sie
        $stmt = $conn->prepare("DELETE FROM Link_Plan_Workout WHERE plan_id = ? AND Workout_id = ?");
        $stmt->bind_param("ii", $plan_id, $workout_id);
        if ($stmt->execute()) {
            echo "removed";
        } else {
            echo "Fehler beim Entfernen des Workouts.";
        }
    } else {
        // Übung ist nicht zugeordnet, also fügen wir sie hinzu
        $stmt = $conn->prepare("INSERT INTO Link_Plan_Workout (plan_id, workout_id) VALUES (?, ?)");
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }
        $stmt->bind_param("ii", $plan_id, $workout_id);
        if ($stmt->execute()) {
            echo "Plan ID: " . $plan_id . "<br>Workout ID: " . $workout_id . "<br>added";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>