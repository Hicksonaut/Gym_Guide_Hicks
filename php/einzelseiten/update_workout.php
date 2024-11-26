<?php
include '../datenbank_connection.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workout_id = intval($_POST['workout_id']);
    $workout_name = $_POST['workout_name'];
    $description = $_POST['description'];
    $ziel_name = $_POST['ziel_name'];
    $muscle_name = $_POST['muscle_name'];
    $equipment_name = $_POST['equipment_name'];
    $level_name = $_POST['level_name'];

    $sql = "
        UPDATE Workouts
        SET workout_name = ?, description = ?, trainingsziel = ?, body_part = ?, equipment = ?, Level = ?
        WHERE workout_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $workout_name, $description, $ziel_name, $muscle_name, $equipment_name, $level_name, $workout_id);

    if ($stmt->execute()) {
        header("Location: workout_details.php?workout_id=" . $workout_id);
        exit();
    } else {
        echo "Fehler beim Speichern der Änderungen.";
    }
}
?>
