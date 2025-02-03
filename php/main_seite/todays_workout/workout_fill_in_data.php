<?php
session_start();
global $conn;
include __DIR__ . '/../../../php/datenbank_connection.php';

if (($_GET['workout_id'])){
    $_SESSION['workout_id'] = $_GET['workout_id'];
    $workout_id = intval($_GET['workout_id']);
} elseif (!isset($_GET['workout_id'])) {
    if (!isset($_SESSION['workout_id'])) {
        die("Kein Workout ausgewählt2.");
    } elseif (isset($_SESSION['workout_id'])) {
        $workout_id = $_SESSION['workout_id'];
    }
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $user_id;
} else {
    echo "Kein Benutzer angemeldet!<br>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verarbeite die Formulareingaben
    if (!empty($_POST['exercise_id']) && is_array($_POST['exercise_id'])) {
        foreach ($_POST['exercise_id'] as $index => $exercise_id) {
            $weight = $_POST['weight'][$index];
            $repetitions = $_POST['repetitions'][$index];
            $date = date('Y-m-d');
            $time = date('H:i:s');

            $insert_sql = "
                INSERT INTO userexerciselog (user_id, workout_id, exercise_id, date, time, repetitions, weight)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param('iiissii', $user_id, $workout_id, $exercise_id, $date, $time, $repetitions, $weight);
            $stmt->execute();
        }
        echo "<p>Deine Eingaben wurden erfolgreich gespeichert!</p>";
    } else {
        echo "<p>Fehler: Keine Übungen ausgewählt.</p>";
    }
}

$sql = "
    SELECT
        lwe.exercise_id_fk AS exercise_id,
        w.workout_name AS workout_name,
        e.name AS exercise_name
    FROM
        link_workout_exercise lwe
    LEFT JOIN
        workouts w ON lwe.workout_id_fk = w.workout_id
    LEFT JOIN
        exercises e ON lwe.exercise_id_fk = e.ex_id
    WHERE
        w.workout_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i',  $workout_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $workout_name = $row['workout_name'];

    echo "<form action='' method='post' id='eintragen_workout'>";
    echo "<h2 style='color: var(--text-primary); margin-bottom: 2rem'>Tracking: $workout_name</h2>";
    echo "<div class='grid_rows'>";
    echo "<div class='grid-three header-row'>";
    echo "
        <h2>Übungen</h2>
        <h2>Gewicht</h2>
        <h2>Wiederholungen</h2>
        </div>
    ";
    do {
        // Daten des letzten Trainings abfragen
        $exercise_id = $row['exercise_id'];
        $last_training_sql = "
            SELECT weight, repetitions
            FROM userexerciselog
            WHERE user_id = ? AND workout_id = ? AND exercise_id = ?
            ORDER BY date DESC, time DESC
            LIMIT 1
        ";
        $last_stmt = $conn->prepare($last_training_sql);
        $last_stmt->bind_param('iii', $user_id, $workout_id, $exercise_id);
        $last_stmt->execute();
        $last_result = $last_stmt->get_result();
        $last_data = $last_result->fetch_assoc();

        $last_weight = isset($last_data['weight']) ? $last_data['weight'] : '';
        $last_repetitions = isset($last_data['repetitions']) ? $last_data['repetitions'] : '';

        echo "
            <div class='grid-three'>
                <h2>" . $row['exercise_name'] . "</h2>
                <input type='hidden' name='exercise_id[]' value='" . $row['exercise_id'] . "'>
                <input type='number' name='weight[]' class='grid-input' placeholder='kg' value='" . htmlspecialchars($last_weight) . "' required>
                <input type='number' name='repetitions[]' class='grid-input' placeholder='Anzahl' value='" . htmlspecialchars($last_repetitions) . "' required>
            </div>
        ";
    } while ($row = $result->fetch_assoc());
    echo "</div>";
    echo "<input id='submitWorkout' class='submit' type='submit' value='Speichern'>";
    echo "</div>";
    echo "</form>";
} else {
    echo "<h2>Kein Workout gefunden</h2>";
}

include "../../Impressum/impressum_link_zeile.php";
?>
