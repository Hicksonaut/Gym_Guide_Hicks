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


$plan_id = isset($_SESSION['plan_id']) ? $_SESSION['plan_id'] : null;
if (!$plan_id) {
    echo "Kein Workout ausgewählt.";
    exit();
}


$sql = "
    SELECT
        wk.trainingsziel AS ziel,
        wk.body_part AS muscle,
        wk.equipment,
        wk.Level AS level
    FROM
        link_plan_workout LPW
    JOIN
        workouts wk ON LPW.workout_id = wk.workout_id
    WHERE
        LPW.plan_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();


$ziel_count = [];
$muscle_count = [];
$equipment_count = [];
$level_sum = 0;
$level_count = 0;
$workout_count = 0;



while ($row = $result->fetch_assoc()) {
    $workout_count++;

    $ziel = $row['ziel'];
    $ziel_count[$ziel] = (isset($tziel_count[$ziel]) ? $ziel_count[$ziel] : 0) + 1;


    $muscle = $row['muscle'];
    $muscle_count[$muscle] = (isset($muscle_count[$muscle]) ? $muscle_count[$muscle] : 0) + 1;


    $equipment = $row['equipment'];
    $equipment_count[$equipment] = (isset($equipment_count[$equipment]) ? $equipment_count[$equipment] : 0) + 1;


    $level = $row['level'];
    if ($level) {
        $level_sum += $level;
        $level_count++;
    }
}


$selected_ziel = null;
$selected_body_part = null;
$selected_equipment = null;
$average_level = null;

if (!empty($ziel_count)) {
    $selected_ziel = array_keys($ziel_count, max($ziel_count))[0];
} else {
    $selected_ziel = null;
}

if (!empty($muscle_count)) {
    arsort($muscle_count);
    $selected_body_part = key($muscle_count);
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
    UPDATE workoutplan
    SET
        ziel = ?,
        body_part = ?,
        Level = ?,
        equipment = ?,
        trainingstage = ?
    WHERE
        plan_id = ?
";

$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("iiiiii", $selected_ziel, $selected_body_part, $average_level, $selected_equipment, $workout_count,$plan_id);

if ($update_stmt->execute()) {

} else {

}

$update_stmt->close();
$stmt->close();
$conn->close();
?>
