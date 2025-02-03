<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../datenbank_connection.php';
global $conn;

if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}


if (!isset($_SESSION['user_id'])) {
    echo "Kein Benutzer angemeldet.";
    exit();
}


$workout_id = isset($_SESSION['workout_id']) ? $_SESSION['workout_id'] : null;
if (!$workout_id) {
    echo "Kein Workout ausgewählt.";
    exit();
}


$sql = "
    SELECT
        ex.exercise_type AS trainingsziel,
        ex.target_muscle AS body_part,
        ex.equipment_requierd AS equipment,
        ex.experience_level AS level
    FROM
        link_workout_exercise lwe
    JOIN
        exercises ex ON lwe.exercise_id_fk = ex.ex_id
    WHERE
        lwe.workout_id_fk = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workout_id);
$stmt->execute();
$result = $stmt->get_result();


$trainingsziel_count = [];
$body_part_count = [];
$equipment_count = [];
$level_sum = 0;
$level_count = 0;


while ($row = $result->fetch_assoc()) {

    $trainingsziel = $row['trainingsziel'];
    $trainingsziel_count[$trainingsziel] = (isset($trainingsziel_count[$trainingsziel]) ? $trainingsziel_count[$trainingsziel] : 0) + 1;


    $body_part = $row['body_part'];
    $body_part_count[$body_part] = (isset($body_part_count[$body_part]) ? $body_part_count[$body_part] : 0) + 1;


    $equipment = $row['equipment'];
    $equipment_count[$equipment] = (isset($equipment_count[$equipment]) ? $equipment_count[$equipment] : 0) + 1;


    $level = $row['level'];
    if ($level) {
        $level_sum += $level;
        $level_count++;
    }
}


$selected_trainingsziel = null;
$selected_body_part = null;
$selected_equipment = null;
$average_level = null;

if (!empty($trainingsziel_count)) {
    $selected_trainingsziel = array_keys($trainingsziel_count, max($trainingsziel_count))[0];
} else {
    $selected_trainingsziel = null;
}

if (!empty($body_part_count)) {
    arsort($body_part_count);
    $selected_body_part = key($body_part_count);
} else {
    $selected_body_part = null;
}

if (!empty($equipment_count)) {
    arsort($equipment_count);
    $selected_equipment = key($equipment_count);
} else {
    $selected_equipment = null;
}


if ($level_count > 0) {
    $average_level = round($level_sum / $level_count);
} else {
    $average_level = null;
}


$update_sql = "
    UPDATE workouts
    SET
        trainingsziel = ?,
        body_part = ?,
        Level = ?,
        equipment = ?
    WHERE
        workout_id = ?
";

$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("iiiii", $selected_trainingsziel, $selected_body_part, $average_level, $selected_equipment, $workout_id);

if ($update_stmt->execute()) {
    //echo "Workout aktualisiert.";
} else {
   // echo "Fehler beim Aktualisieren des Workouts: " . $conn->error;
}

$update_stmt->close();
$stmt->close();
$conn->close();
?>
