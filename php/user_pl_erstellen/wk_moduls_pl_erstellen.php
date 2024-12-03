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

$plan_id = $_SESSION['plan_id'];
if (!$plan_id) {
    echo "Keine Datenbank-ID angegeben.<br>";
    exit();
}

$sql = "
    SELECT
        wk.workout_id AS id,
        wk.workout_name AS name,
        wk.wk_bild AS Bild, 
        M.muscle_name AS muscle,
        e.equipment_name AS equipment,
        z.ziel_name AS ziel,
        EXISTS(
            SELECT 1
            FROM link_plan_workout lpw 
            WHERE lpw.workout_id = wk.workout_id
            AND lpw.plan_id = ?
        ) AS is_in_plan
    FROM
        workouts wk
    LEFT JOIN
        muscle M ON wk.body_part = M.muscle_id
    LEFT JOIN
        equipment e ON wk.equipment = e.equipment_id
    LEFT JOIN 
        trainingsziel z ON wk.trainingsziel = z.ziel_id
    WHERE 
        wk.creator_user_id = ? OR wk.is_universal = 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $plan_id, $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='module-container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='module-card' data-workout-id='" . htmlspecialchars($row['id']) . "'>";

        if ($row['is_in_plan']) {
            echo "<div class='plus-icon' onclick='addToListPl(" . htmlspecialchars($row['id']) . ")'>
            <img class='plus-icon-img' src='../svg/check-circle.svg' alt='check' title='check'>
            </div>";
        } else {
            echo "<div class='plus-icon' onclick='addToListPl(" . htmlspecialchars($row['id']) . ")'>
            <img class='plus-icon-img' src='../svg/plus.svg' alt='plus' title='plus'>
            </div>";
        }

        if (!empty($row['Bild'])) {
            echo "<img class='module-image' src='/img/workout_bilder/" . htmlspecialchars($row["Bild"]) . "' >";
        } else {
            echo "<img class='module-image' src='/img/image-not-found.png' '>";
        }
        echo "<h2 class='module-name'>" . htmlspecialchars($row["name"]) . "</h2>";
        echo "<div class='container-attribut'>";
        echo "<p class='module-attribut-border-three'>" . htmlspecialchars($row["ziel"]) . "</p>";
        echo "<p class='module-attribut-border-three'>" . htmlspecialchars($row["muscle"]) . "</p>";
        echo "<p class='module-attribut-border-three'>" . htmlspecialchars($row["equipment"]) . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "Keine Ergebnisse gefunden.";
}

$conn->close();

?>

