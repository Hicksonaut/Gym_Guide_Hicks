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

$sql = "
    SELECT
        wk.workout_id AS id,
        wk.workout_name AS name,
        wk.wk_bild AS Bild, 
        M.muscle_name AS muscle,
        e.equipment_name AS equipment,
        z.ziel_name AS ziel
    FROM
        Workouts wk
    LEFT JOIN
        Muscle M ON wk.body_part = M.muscle_id
    LEFT JOIN
        equipment e ON wk.equipment = e.equipment_id
    LEFT JOIN 
        trainingsziel z ON wk.trainingsziel = z.ziel_id
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='workout-container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='workout-card' data-workout-id='" . htmlspecialchars($row['id']) . "'>";

        echo "<div class='plus-icon' onclick='addToListPl(" . htmlspecialchars($row['id']) . ")'>+</div>";

        if (!empty($row['Bild'])) {
            echo "<img class='workout-image' src='/img/Workout_bilder/" . htmlspecialchars($row["Bild"]) . "' >";
        } else {
            echo "<img class='workout-image' src='/img/image-not-found.png' '>";
        }
        echo "<h2 class='workout-name'>" . htmlspecialchars($row["name"]) . "</h2>";
        echo "<div class='container-attribut'>";
        echo "<p class='workout-attribut-border'>" . htmlspecialchars($row["ziel"]) . "</p>";
        echo "<p class='workout-attribut-border'>" . htmlspecialchars($row["muscle"]) . "</p>";
        echo "<p class='workout-attribut-border'>" . htmlspecialchars($row["equipment"]) . "</p>";
        echo "</div>";
        echo "</div>";

    }
    echo "</div>";
} else {
    echo "Keine Ergebnisse gefunden.";
}

$conn->close();

?>

