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

// Abrufen der Übungen
if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $userid = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $userid;
} else {
    echo "Kein Benutzer angemeldet.<br>";
    exit();
}

$workout_id = $_SESSION['workout_id'];
if (!$workout_id) {
    echo "Keine Workout ID angegeben.<br>";
    exit();
}

$sql = "
    SELECT
        ex.ex_id,
        ex.name,
        ex.bild_ex,
        M.muscle_name AS target_muscle_name,
        e.equipment_name AS e_name,
        me.mechanics_name AS me_name,
        L.level_name AS experience_level_name,
        EXISTS (
            SELECT 1
            FROM link_workout_exercise lwe
            WHERE lwe.exercise_id_fk = ex.ex_id
            AND lwe.workout_id_fk = ?
            ) AS is_in_workout
    FROM
        exercises ex
    LEFT JOIN
        muscle M ON ex.target_muscle = M.muscle_id
    LEFT JOIN
        equipment e ON ex.equipment_requierd = e.equipment_id
    LEFT JOIN 
        mechanics me ON ex.mechanics = me.mechanics_id
    LEFT JOIN
        levels L ON ex.experience_level = L.level_id
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workout_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='module-container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='module-card' data-exercise-id='" . htmlspecialchars($row['ex_id']) . "'>";

        if($row['is_in_workout']) {
            echo "<div class='plus-icon' onclick='addToList(".htmlspecialchars($row['ex_id']).")'>
            <img class='plus-icon-img' src='../svg/check-circle.svg' alt='check' title='check'>
            </div>";
        } else {
            echo "<div class='plus-icon' onclick='addToList(" . htmlspecialchars($row['ex_id']) . ")'>
            <img class='plus-icon-img' src='../svg/plus.svg' alt='plus' title='plus'>
            </div>";
        }

        if (!empty($row['bild_ex'])) {
            echo "<img class='module-image' src='/img/Exercise_bilder/" . htmlspecialchars($row["bild_ex"]) . "' >";
        } else {
            echo "<img class='module-image' src='/img/image-not-found.png' '>";
        }
        echo "<h2 class='module-name'>" . htmlspecialchars($row["name"]) . "</h2>";
        echo "<div class='container-attribut'>";
        echo "<p class='module-attribut-border-four'>" . htmlspecialchars($row["target_muscle_name"]) . "</p>";
        echo "<p class='module-attribut-border-four'>" . htmlspecialchars($row["e_name"]) . "</p>";
        echo "<p class='module-attribut-border-four'>" . htmlspecialchars($row["me_name"]) . "</p>";
        echo "<p class='module-attribut-border-four'>" . htmlspecialchars($row["experience_level_name"]) . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "Keine Ergebnisse gefunden.";
}
$conn->close();
?>